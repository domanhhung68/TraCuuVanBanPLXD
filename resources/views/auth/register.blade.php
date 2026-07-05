@extends('layouts.app')

@section('title', 'Đăng ký')

@push('styles')
<style>
    .auth-page {
        min-height: calc(100vh - 72px);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2rem 0;
        background-image: url('{{ asset('images/220721_Khoa-boi-duong.jpg') }}');
    }
    .register-container {
        background: white;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 400px;
    }
    .register-container h1 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .register-container label {
        display: block;
        margin-bottom: 5px;
        color: #555;
        font-weight: bold;
    }
    .register-container input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    .register-container input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
    }
    .register-container button {
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
    .register-container button:hover {
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
    .register-container a {
        color: #667eea;
        text-decoration: none;
    }
    .register-container a:hover {
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="register-container">
        <h1>Đăng Ký</h1>

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="error">{{ $error }}</div>
            @endforeach
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Họ tên</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

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

            <div class="form-group">
                <label for="password_confirmation">Xác nhận mật khẩu</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                @error('password_confirmation')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit">Đăng Ký</button>
        </form>

        <div class="links">
            <a href="{{ route('login') }}">Đăng nhập</a>
        </div>
    </div>
</div>
@endsection
