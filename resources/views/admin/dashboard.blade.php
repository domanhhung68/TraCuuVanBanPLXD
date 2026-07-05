@extends('layouts.app')

@section('title', 'Dashboard Admin')

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
    .admin-badge {
        display: inline-block;
        background: #c41c3b;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-weight: bold;
        margin-left: 10px;
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
        color: #c41c3b;
        margin-bottom: 10px;
    }
    .info-card p {
        color: #666;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .stat-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
    }
    .stat-box h4 {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 10px;
    }
    .stat-number {
        font-size: 32px;
        font-weight: bold;
    }
    .btn {
        display: inline-block;
        padding: 10px 20px;
        background: #c41c3b;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        cursor: pointer;
        border: none;
        font-size: 14px;
    }
    .btn:hover {
        background: #a01830;
    }
    .btn-logout {
        background: #666;
    }
    .btn-logout:hover {
        background: #555;
    }
</style>
@endpush

@section('content')
<div class="dashboard-page">
    <div class="container">
        <div class="welcome-card">
            <h1>Chào mừng, {{ Auth::user()->name }}!</h1>
            <p>Bạn đang đăng nhập với vai trò: <strong>Quản trị viên</strong></p>
        </div>

        <div class="info-grid">
            <div class="info-card">
                <h3>📧 Email</h3>
                <p>{{ Auth::user()->email }}</p>
            </div>
            <div class="info-card">
                <h3>👤 Vai trò</h3>
                <p>Admin</p>
            </div>
            <div class="info-card">
                <h3>📅 Thành viên từ</h3>
                <p>{{ Auth::user()->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="stats-grid" style="margin-top: 30px;">
            <div class="stat-box">
                <h4>Tổng người dùng</h4>
                <div class="stat-number">{{ $totalUsers ?? 0 }}</div>
            </div>
            <div class="stat-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h4>Khách hàng</h4>
                <div class="stat-number">{{ $customerCount ?? 0 }}</div>
            </div>
            <div class="stat-box" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <h4>Admin</h4>
                <div class="stat-number">{{ $adminCount ?? 0 }}</div>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <a href="{{ route('admin.ui.users') }}" class="btn">Quản lý Người dùng</a>
            <a href="#" class="btn" style="margin-left: 10px;">Báo cáo</a>
            <a href="#" class="btn" style="margin-left: 10px;">Cài đặt</a>
        </div>
    </div>
</div>
@endsection
