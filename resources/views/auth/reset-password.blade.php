@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu')

@push('styles')
<style>
    .auth-page {
        min-height: calc(100vh - 72px);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2rem 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .auth-container {
        background: white;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 400px;
    }
    .auth-container h1 {
        text-align: center;
        margin-bottom: 10px;
        color: #333;
        font-size: 24px;
    }
    .description {
        text-align: center;
        color: #666;
        margin-bottom: 30px;
        font-size: 14px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .auth-container label {
        display: block;
        margin-bottom: 5px;
        color: #555;
        font-weight: bold;
    }
    .auth-container input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    .auth-container input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
    }
    .auth-container button {
        width: 100%;
        padding: 10px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        margin-top: 10px;
    }
    .auth-container button:hover {
        background: #5568d3;
    }
    .error {
        color: #d32f2f;
        font-size: 14px;
        margin-top: 5px;
    }
    .links {
        text-align: center;
        margin-top: 20px;
    }
    .auth-container a {
        color: #667eea;
        text-decoration: none;
    }
    .auth-container a:hover {
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-container">
        <h1>Đặt lại mật khẩu</h1>
        <p class="description">Nhập mật khẩu mới cho tài khoản của bạn</p>

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="error">{{ $error }}</div>
            @endforeach
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ $email }}" readonly>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu mới</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Xác nhận mật khẩu</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                @error('password_confirmation')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit">Đặt lại mật khẩu</button>
        </form>

        <div class="links">
            <a href="{{ route('login') }}">Quay lại đăng nhập</a>
        </div>
    </div>
</div>
@endsection
