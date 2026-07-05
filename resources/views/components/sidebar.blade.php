@php
$menus = [
    [
        'label' => 'Dashboard',
        'icon' => 'fa-house',
        'url' => route('admin.ui.dashboard'),
        'active' => request()->is('admin-ui/dashboard') || request()->is('admin/dashboard'),
    ],
    [
        'label' => 'Quản lý văn bản',
        'icon' => 'fa-file-contract',
        'active' => request()->is('admin-ui/legal-documents*'),
        'children' => [
            ['label' => 'Danh sách văn bản', 'url' => route('admin.ui.legal-documents'), 'active' => request()->is('admin-ui/legal-documents') && !request()->is('admin-ui/legal-documents/create') && !request()->is('admin-ui/legal-documents/*/edit')],
            ['label' => 'Thêm văn bản', 'url' => route('admin.ui.law.create'), 'active' => request()->is('admin-ui/legal-documents/create')],
        ],
    ],
    [
        'label' => 'Quản lý người dùng',
        'icon' => 'fa-users',
        'url' => '/admin-ui/users',
        'active' => request()->is('admin-ui/users'),
    ],
];
@endphp

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('admin.ui.dashboard') }}" class="brand">
            <div class="brand-icon"><i class="fas fa-gavel"></i></div>
            <div class="brand-text">
                <span class="brand-title">TCVBPL</span>
                <span class="brand-subtitle">Admin Panel</span>
            </div>
        </a>
        {{-- <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Thu gọn sidebar">
            <i class="fas fa-bars"></i>
        </button> --}}
    </div>

    <nav class="sidebar-nav">
        <p class="nav-section">Điều hướng</p>
        @foreach ($menus as $menu)
            @if (!empty($menu['children']))
                <div class="nav-group">
                    <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-{{ Str::slug($menu['label']) }}" aria-expanded="false">
                        <i class="fas {{ $menu['icon'] }}"></i>
                        <span class="link-text">{{ $menu['label'] }}</span>
                        <i class="fas fa-chevron-down ms-auto small"></i>
                    </a>
                    <div class="submenu collapse" id="submenu-{{ Str::slug($menu['label']) }}">
                        @foreach ($menu['children'] as $child)
                            <a class="submenu-link {{ $child['active'] ? 'active' : '' }}" href="{{ $child['url'] }}">
                                <i class="fas fa-circle"></i>
                                <span>{{ $child['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <a class="nav-link {{ $menu['active'] ? 'active' : '' }}" href="{{ $menu['url'] }}">
                    <i class="fas {{ $menu['icon'] }}"></i>
                    <span class="link-text">{{ $menu['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>
</aside>
