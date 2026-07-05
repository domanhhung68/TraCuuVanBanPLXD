<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tra cứu văn bản pháp lý')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #2563EB;
            --primary-soft: #dbeafe;
            --surface: #f8fafc;
            --border: #e2e8f0;
            --text: #0f172a;
            --muted: #64748b;
        }

        body {
            font-family: Inter, 'Segoe UI', sans-serif;
            background: #f3f6fb;
            color: var(--text);
        }

        .admin-shell {
            min-height: 100vh;
            display: flex;
            background: #f3f6fb;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #0f172a 0%, #111827 100%);
            color: #fff;
            padding: 1.25rem 1rem;
            transition: width 0.25s ease;
            flex-shrink: 0;
        }

        .sidebar.collapsed {
            width: 86px;
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            text-decoration: none;
            color: #fff;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: rgba(255,255,255,.15);
            font-size: 1rem;
        }

        .brand-title {
            display: block;
            font-weight: 700;
            font-size: 1rem;
        }

        .brand-subtitle {
            display: block;
            font-size: .75rem;
            color: #cbd5e1;
        }

        .sidebar-toggle {
            border: 0;
            background: rgba(255,255,255,.12);
            color: #fff;
            border-radius: 10px;
            width: 38px;
            height: 38px;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: .3rem;
        }

        .nav-section {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #94a3b8;
            margin: 1rem 0 .4rem;
            padding: 0 .5rem;
        }

        .nav-link,
        .submenu-link {
            display: flex;
            align-items: center;
            gap: .8rem;
            text-decoration: none;
            color: #e2e8f0;
            padding: .75rem .8rem;
            border-radius: 12px;
            transition: all .2s ease;
        }

        .nav-link:hover,
        .submenu-link:hover,
        .nav-link.active {
            background: var(--primary);
            color: #fff;
        }

        .submenu-link.active {
            background: rgba(37, 99, 235, 0.2);
            color: #fff;
            font-weight: 600;
        }

        .submenu {
            display: flex;
            flex-direction: column;
            gap: .2rem;
            margin-left: 1.2rem;
            padding-left: .6rem;
            border-left: 1px solid rgba(255,255,255,.12);
        }

        .submenu-link {
            font-size: .92rem;
            color: #cbd5e1;
            padding: .55rem .7rem;
        }

        .admin-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: #fff;
            padding: 1.15rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .page-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
        }

        .page-subtitle {
            color: var(--muted);
            margin: 0;
            font-size: .9rem;
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
        }

        .admin-content {
            padding: 1.5rem;
        }

        .card {
            border-radius: 1rem;
            border: 1px solid var(--border);
        }

        .dashboard-card {
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 .5rem 1.2rem rgba(37, 99, 235, .12) !important;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 1.1rem;
        }

        .chart-bars {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 1rem;
            align-items: end;
            height: 240px;
            padding-top: 1rem;
        }

        .bar-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .6rem;
            height: 100%;
        }

        .bar-track {
            width: 100%;
            height: 100%;
            background: #eef2ff;
            border-radius: 999px;
            display: flex;
            align-items: end;
            overflow: hidden;
        }

        .bar-fill {
            width: 100%;
            background: linear-gradient(180deg, #60a5fa 0%, #2563EB 100%);
            border-radius: 999px;
        }

        .bar-label {
            font-size: .8rem;
            text-align: center;
            color: var(--muted);
        }

        .line-chart {
            position: relative;
            padding: 0 .5rem;
        }

        .line-chart-grid {
            position: absolute;
            inset: 0;
            background-image: linear-gradient(to bottom, rgba(148,163,184,.2) 1px, transparent 1px);
            background-size: 100% 36px;
            pointer-events: none;
        }

        .btn {
            border-radius: 999px;
        }

        .table thead th {
            color: var(--muted);
            font-weight: 600;
            background: #f8fafc;
        }

        .dropdown-menu {
            display: none;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown:hover > .dropdown-menu,
        .dropdown.show > .dropdown-menu {
            display: block;
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 86px;
            }

            .link-text,
            .brand-text,
            .brand-subtitle {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .admin-shell {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .admin-content {
                padding: 1rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php $layoutType = trim($__env->yieldContent('layout_type')); @endphp

    @if ($layoutType === 'admin')
        <div class="admin-shell">
            @include('components.sidebar')
            <div class="admin-main">
                @include('components.navbar')
                <main class="admin-content">
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        <nav class="navbar navbar-expand-lg shadow-sm bg-white">
            <div class="container">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <img src="{{ asset('images/images.png') }}" alt="Logo" class="me-2" style="height: 40px;">
                    <span class="h6 text-dark">Văn bản pháp lý ngành xây dựng</span>
                </a>
                <div class="ms-auto d-flex gap-2 align-items-center">
                    @if (Auth::check())
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm d-flex align-items-center gap-2 rounded-pill shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563EB&color=fff" alt="{{ Auth::user()->name }}" class="rounded-circle" style="width: 34px; height: 34px; object-fit: cover;">
                                <span class="fw-semibold text-dark">{{ \Illuminate\Support\Str::limit(Auth::user()->name, 20) }}</span>
                                <i class="fas small text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user me-2"></i>Quản lý hồ sơ</a></li>
                                @if (Auth::user()->role !== 'admin')
                                    <li><a class="dropdown-item" href="{{ route('profile.favorites') }}"><i class="fas fa-heart me-2"></i>Văn bản yêu thích</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="{{ route('logout') }}"><i class="fas fa-right-from-bracket me-2"></i>Đăng xuất</a></li>
                            </ul>
                        </div>
                    @else
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">Đăng nhập</a>
                        <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Đăng ký</a>
                    @endif
                </div>
            </div>
        </nav>

        @yield('content')
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');

            if (toggle && sidebar) {
                toggle.addEventListener('click', function () {
                    sidebar.classList.toggle('collapsed');
                });
            }

            document.querySelectorAll('.dropdown').forEach(function (dropdown) {
                const trigger = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');

                if (!trigger || !menu) {
                    return;
                }

                trigger.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    const isOpen = dropdown.classList.contains('show');
                    document.querySelectorAll('.dropdown.show').forEach(function (openDropdown) {
                        if (openDropdown !== dropdown) {
                            openDropdown.classList.remove('show');
                            const openMenu = openDropdown.querySelector('.dropdown-menu');
                            if (openMenu) {
                                openMenu.classList.remove('show');
                            }
                        }
                    });
                    dropdown.classList.toggle('show', !isOpen);
                    menu.classList.toggle('show', !isOpen);
                });

                dropdown.addEventListener('mouseenter', function () {
                    dropdown.classList.add('show');
                    menu.classList.add('show');
                });

                dropdown.addEventListener('mouseleave', function () {
                    dropdown.classList.remove('show');
                    menu.classList.remove('show');
                });
            });

            document.addEventListener('click', function (event) {
                document.querySelectorAll('.dropdown.show').forEach(function (dropdown) {
                    if (!dropdown.contains(event.target)) {
                        dropdown.classList.remove('show');
                        const menu = dropdown.querySelector('.dropdown-menu');
                        if (menu) {
                            menu.classList.remove('show');
                        }
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
