<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        if (in_array(auth()->user()->role, $roles)) {
            return $next($request);
        }

        return response()->view('errors.403', [], 403);
    }
}
