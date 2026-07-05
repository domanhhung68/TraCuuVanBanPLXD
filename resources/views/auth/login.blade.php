@extends('layouts.app')

@section('title', 'Đăng nhập')

@push('styles')
<style>
    .auth-page {
        min-height: calc(100vh - 72px);
        display: flex;
        justify-content: center;
        align-items: center;
        /* padding: 2rem 0; */
        /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
        background-image: url('{{ asset('images/220721_Khoa-boi-duong.jpg') }}');
        background-size: cover;
        background-position: center;
    }
    .login-container {
        background: white;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 400px;
    }
    .login-container h1 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .login-container label {
        display: block;
        margin-bottom: 5px;
        color: #555;
        font-weight: bold;
    }
    .login-container input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    .login-container input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
    }
    .login-container button {
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
    .login-container button:hover {
        background: #5568d3;
    }
    .error {
        color: #d32f2f;
        font-size: 14px;
        margin-top: 5px;
    }
    .success {
        color: #388e3c;
        font-size: 14px;
        margin-bottom: 15px;
        padding: 10px;
        background: #e8f5e9;
        border-radius: 4px;
    }
    .links {
        text-align: center;
        margin-top: 20px;
    }
    .login-container a {
        color: #667eea;
        text-decoration: none;
    }
    .login-container a:hover {
        text-decoration: underline;
    }
    .link-separator {
        color: #ccc;
        margin: 0 5px;
    }
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="login-container">
        <h1>Đăng Nhập</h1>

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="error">{{ $error }}</div>
            @endforeach
        @endif

        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit">Đăng Nhập</button>
        </form>

        <div class="links">
            <a href="{{ route('register') }}">Đăng ký</a>
            <span class="link-separator">|</span>
            <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
            <span class="link-separator">|</span>
            <a href="{{ route('home') }}">Trang chủ</a>
        </div>
    </div>
</div>
@endsection
