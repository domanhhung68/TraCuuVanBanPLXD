@extends('layouts.app')

@section('title', 'Dashboard Khách hàng')

@push('styles')
<style>
    .dashboard-page {
        background: #f5f5f5;
        min-height: calc(100vh - 72px);
        padding: 2rem 0;
    }
    .welcome-card {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    .welcome-card h1 {
        color: #333;
        margin-bottom: 10px;
    }
    .welcome-card p {
        color: #666;
        font-size: 16px;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .info-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    .info-card h3 {
        color: #667eea;
        margin-bottom: 10px;
    }
    .info-card p {
        color: #666;
    }
    .btn {
        display: inline-block;
        padding: 10px 20px;
        background: #667eea;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        cursor: pointer;
        border: none;
        font-size: 14px;
    }
    .btn:hover {
        background: #5568d3;
    }
    .btn-logout {
        background: #d32f2f;
    }
    .btn-logout:hover {
        background: #b71c1c;
    }
</style>
@endpush

@section('content')
<div class="dashboard-page">
    <div class="container">
        <div class="welcome-card">
            <h1>Chào mừng, {{ Auth::user()->name }}!</h1>
            <p>Bạn đang đăng nhập với vai trò: <strong>Khách hàng</strong></p>
        </div>

        <div class="info-grid">
            <div class="info-card">
                <h3>📧 Email</h3>
                <p>{{ Auth::user()->email }}</p>
            </div>
            <div class="info-card">
                <h3>👤 Vai trò</h3>
                <p>Khách hàng</p>
            </div>
            <div class="info-card">
                <h3>📅 Thành viên từ</h3>
                <p>{{ Auth::user()->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
