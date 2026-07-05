@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')

@section('content')
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=2563EB&color=fff" alt="{{ $user->name }}" class="rounded-circle" style="width: 72px; height: 72px; object-fit: cover;">
                            <div>
                                <h1 class="h3 fw-bold mb-1">Hồ sơ cá nhân</h1>
                                <p class="text-muted mb-0">Quản lý tên hiển thị và mật khẩu đăng nhập của bạn.</p>
                            </div>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Tên hiển thị</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="current_password" class="form-label fw-semibold">Mật khẩu hiện tại</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Để trống nếu không đổi mật khẩu">
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="new_password" class="form-label fw-semibold">Mật khẩu mới</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Tối thiểu 6 ký tự">
                                </div>
                                <div class="col-md-6">
                                    <label for="new_password_confirmation" class="form-label fw-semibold">Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" placeholder="Nhập lại mật khẩu mới">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('profile.favorites') }}" class="btn btn-outline-secondary">Văn bản yêu thích</a>
                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
