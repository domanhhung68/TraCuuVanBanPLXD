@extends('layouts.app')

@section('title', 'Tra cứu văn bản pháp lý')

@section('content')
    <style>
        .main-panel {
            background: linear-gradient(135deg, #0d6efd 0%, #2563eb 45%, #0f766e 100%);
        }
    </style>
    <header class="main-panel text-white pt-5">
        <div class="container pt-2 pb-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-12">
                    {{-- <span class="badge bg-light text-primary mb-3">Hệ thống tra cứu văn bản pháp lý</span> --}}
                    <h1 class="display-5 fw-bold mb-3">Tra cứu nhanh, dễ dàng và chính xác</h1>
                    <p class="lead mb-4">Khám phá các văn bản pháp luật, điều kiện hiệu lực, cơ quan ban hành và nội dung
                        liên quan trong ngành xây dựng.</p>
                    {{-- <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-light btn-lg" href="{{ route('law.search') }}">Bắt đầu tìm kiếm</a>
                        <a class="btn btn-outline-light btn-lg" href="#featured">Xem nổi bật</a>
                    </div> --}}
                </div>
                <div class="col-lg-12 py-5">
                    <div class="card shadow border-0 rounded-4">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3">Tìm kiếm theo từ khóa</h2>
                            <form method="GET" action="{{ route('law.search') }}">
                                <input type="text" name="q" class="form-control form-control-lg"
                                    placeholder="Nhập từ khóa..." value="{{ request('q', '') }}">
                                <div class="mt-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="search_scope"
                                            id="home-scope-all" value="all"
                                            {{ request('search_scope', 'all') === 'all' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="home-scope-all">Tất cả</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="search_scope"
                                            id="home-scope-title" value="title"
                                            {{ request('search_scope') === 'title' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="home-scope-title">Tiêu đề</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="search_scope"
                                            id="home-scope-document" value="so_ky_hieu"
                                            {{ request('search_scope') === 'so_ky_hieu' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="home-scope-document">Số hiệu văn bản</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="search_scope"
                                            id="home-scope-content" value="content_html"
                                            {{ request('search_scope') === 'content_html' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="home-scope-content">Nội dung</label>
                                    </div>
                                </div>
                                <button class="btn btn-primary w-100 mt-3">Tra cứu ngay</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @if (session('recent_searches') && count(session('recent_searches')))
                <section class="card shadow-sm border-0">
                    <div class="card-body px-4 d-flex">
                        <h2 class="h5 fw-bold mb-3 pe-3">Lịch sử tra cứu:</h2>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach (session('recent_searches') as $item)
                                <a href="{{ route('law.search', ['q' => $item['query'], 'search_scope' => $item['search_scope'] ?? 'all']) }}"
                                    class="">
                                    {{ $item['query'] }}
                                </a>,
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </header>

    <main class="container py-5">
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="h4 fw-bold mb-1">Lĩnh vực tiêu biểu</h2>
                    <p class="text-soft mb-0">Các lĩnh vực có nhiều văn bản nhất.</p>
                </div>
            </div>

            <div class="row g-4">
                @php
                    $featuredBlocks = [
                        $featuredField1 ?? null,
                        $featuredField2 ?? null,
                        $featuredField3 ?? null,
                        $featuredField4 ?? null,
                    ];
                @endphp

                @foreach ($featuredBlocks as $index => $field)
                    <div class="col-md-6 col-lg-3">
                        <a href="{{ route('law.search', ['linh_vuc' => [$field['name'] ?? '']]) }}"
                            class="text-decoration-none text-dark">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                <img src="{{ asset('images/field-' . ($index + 1) . '.jpg') }}" class="card-img-top"
                                    alt="{{ $field['name'] ?? 'Lĩnh vực' }}" style="height: 180px; object-fit: cover;">
                                <div class="card-body">
                                    <h3 class="h6 fw-bold mb-1">{{ $field['name'] ?? 'Chưa cập nhật' }}</h3>
                                    <p class="small text-soft mb-0">{{ $field['count'] ?? 0 }} văn bản</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>

        <section id="featured" class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="h4 fw-bold mb-1">Văn bản mới cập nhật</h2>
                    {{-- <p class="text-soft mb-0">Danh sách văn bản được cập nhật gần đây, hiển thị theo cấu trúc dễ đọc.</p> --}}
                </div>
            </div>

            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 20%">Số hiệu</th>
                                <th style="width: 20%">Ngày ban hành</th>
                                <th>Tiêu đề</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentLaws as $law)
                                <tr onclick="window.location='{{ route('law.show', $law->id) }}'" style="cursor: pointer;">
                                    <td class="fw-semibold">{{ $law->so_ky_hieu ?? '—' }}</td>
                                    <td>{{ $law->ngay_ban_hanh ? \Illuminate\Support\Carbon::parse($law->ngay_ban_hanh)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold line-clamp-2"
                                            style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 1.4; max-height: 2.8em;">
                                            {{ $law->title }}</div>
                                        <div class="small text-soft">{{ $law->loai_van_ban ?? 'Văn bản' }} •
                                            {{ $law->tinh_trang_hieu_luc ?? '—' }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">
                                        <div class="alert alert-info mb-0">Chưa có dữ liệu văn bản để hiển thị.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if (
                $recentLaws instanceof \Illuminate\Contracts\Pagination\Paginator ||
                    $recentLaws instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                <div class="mt-4 d-flex justify-content-center">
                    {{ $recentLaws->links() }}
                </div>
            @endif
        </section>
    </main>
@endsection
