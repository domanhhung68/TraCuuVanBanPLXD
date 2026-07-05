<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ], [
            'email.exists' => 'Email này không được đăng ký trong hệ thống',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email không tồn tại']);
        }

        // Tạo token reset password
        $token = Str::random(60);
        
        // Lưu token vào database
        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => $token,
                'created_at' => now(),
            ]
        );

        // Gửi email với link reset
        $user->notify(new ResetPasswordNotification($token, $user->email));

        return back()->with('success', 'Link đặt lại mật khẩu đã được gửi đến email của bạn');
    }

    public function showResetForm($token)
    {
        $passwordReset = DB::table('password_resets')
            ->where('token', $token)
            ->first();

        if (!$passwordReset || now()->diffInMinutes($passwordReset->created_at) > 60) {
            return redirect()->route('login')->withErrors(['error' => 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn']);
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $passwordReset->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
            'token' => 'required',
        ]);

        $passwordReset = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset || now()->diffInMinutes($passwordReset->created_at) > 60) {
            return back()->withErrors(['token' => 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email không tồn tại']);
        }

        // Cập nhật mật khẩu
        $user->update([
            'password' => bcrypt($request->password),
        ]);

        // Xóa token reset
        DB::table('password_resets')
            ->where('email', $request->email)
            ->delete();

        return redirect()->route('login')->with('success', 'Mật khẩu đã được đặt lại thành công. Vui lòng đăng nhập');
    }
}
