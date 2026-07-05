<header class="topbar">
    <div class="d-flex align-items-center justify-content-between gap-3">
        <div>
            <h1 class="page-title">Tra cứu Văn bản Pháp luật</h1>
            <p class="page-subtitle">Bảng điều khiển quản trị</p>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="d-none d-md-flex align-items-center gap-2 text-muted">
                <i class="fas fa-bell"></i>
                <span>Thông báo mới</span>
            </div>
            <div class="dropdown">
                <button class="btn btn-light d-flex align-items-center gap-2 rounded-pill shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::check() ? (Auth::user()->name ?? 'Admin') : 'Admin') }}&background=2563EB&color=fff" alt="{{ Auth::check() ? (Auth::user()->name ?? 'Admin') : 'Admin' }}" class="avatar">
                    <span class="fw-semibold">{{ \Illuminate\Support\Str::limit(Auth::check() ? (Auth::user()->name ?? 'Admin') : 'Admin', 20) }}</span>
                    <i class="fas small"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    @if (Auth::check())
                        <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user me-2"></i>Quản lý hồ sơ</a></li>
                        @if (Auth::user()->role !== 'admin')
                            <li><a class="dropdown-item" href="{{ route('profile.favorites') }}"><i class="fas fa-heart me-2"></i>Văn bản yêu thích</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="{{ route('logout') }}"><i class="fas fa-right-from-bracket me-2"></i>Đăng xuất</a></li>
                    @else
                        <li><a class="dropdown-item" href="{{ route('login') }}"><i class="fas fa-right-to-bracket me-2"></i>Đăng nhập</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</header>
