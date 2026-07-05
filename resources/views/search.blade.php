@extends('layouts.app')

@section('title', 'Tìm kiếm văn bản pháp lý')

@section('content')
    <main class="py-5">
        <section class="container">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('law.search') }}">
                        <div class="row g-3 align-items-top">
                            <h3 class="form-label fw-semibold" for="search-query">Từ khóa tra cứu</h3>
                            <div class="col-lg-9">
                                <input id="search-query" name="q" class="form-control"
                                    placeholder="Ví dụ: quy định, lao động, số 123" value="{{ $query }}">
                                <div class="mt-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="search_scope"
                                            id="search-scope-all" value="all"
                                            {{ ($searchScope ?? 'all') === 'all' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="search-scope-all">Tất cả</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="search_scope"
                                            id="search-scope-title" value="title"
                                            {{ ($searchScope ?? 'all') === 'title' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="search-scope-title">Tiêu đề</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="search_scope"
                                            id="search-scope-document" value="so_ky_hieu"
                                            {{ ($searchScope ?? 'all') === 'so_ky_hieu' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="search-scope-document">Số hiệu văn bản</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="search_scope"
                                            id="search-scope-content" value="content_html"
                                            {{ ($searchScope ?? 'all') === 'content_html' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="search-scope-content">Nội dung</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <button class="btn btn-primary w-50">Tìm kiếm</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h1 class="h4 fw-bold mb-1">Kết quả tra cứu</h1>
                            <p class="text-soft mb-0">Danh sách văn bản được sắp xếp theo dạng list để đọc nhanh hơn.</p>
                        </div>
                        @if ($hasSearch)
                            <span class="text-soft small">Tìm thấy {{ $laws->total() }} văn bản</span>
                        @endif
                    </div>

                    @if (session('recent_searches') && count(session('recent_searches')))
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <h2 class="h6 fw-bold mb-3">Lịch sử tra cứu gần đây</h2>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach (session('recent_searches') as $item)
                                        <a href="{{ route('law.search', ['q' => $item['query'], 'search_scope' => $item['search_scope'] ?? 'all']) }}"
                                            class="btn btn-outline-primary btn-sm">
                                            {{ $item['query'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (!$hasSearch)
                        <div class="alert alert-info mb-0">Hãy nhập từ khóa hoặc chọn bộ lọc để bắt đầu tra cứu.</div>
                    @elseif ($laws->isEmpty())
                        <div class="alert alert-warning mb-0">Không tìm thấy văn bản nào phù hợp với các điều kiện hiện tại.
                        </div>
                    @else
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
                                        @foreach ($laws as $law)
                                            <tr onclick="window.location='{{ route('law.show', $law->id) }}'"
                                                style="cursor: pointer;">
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
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-center">
                            {{ $laws->withQueryString()->links() }}
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 filter-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h6 fw-bold mb-0">Bộ lọc</h2>
                            </div>

                            <div id="filter-panel" class="collapse show">
                                <form method="GET" action="{{ route('law.search') }}">
                                    <input type="hidden" name="q" value="{{ $query }}">
                                    <input type="hidden" name="search_scope" value="{{ $searchScope ?? 'all' }}">

                                    @php
                                        $filterGroups = [
                                            [
                                                'key' => 'loai_van_ban',
                                                'label' => 'Loại văn bản',
                                                'values' => $filterOptions['loai_van_ban'],
                                                'selected' => $selectedFilters['loai_van_ban'],
                                            ],
                                            [
                                                'key' => 'linh_vuc',
                                                'label' => 'Lĩnh vực',
                                                'values' => $filterOptions['linh_vuc'],
                                                'selected' => $selectedFilters['linh_vuc'],
                                            ],
                                            [
                                                'key' => 'tinh_trang_hieu_luc',
                                                'label' => 'Tình trạng hiệu lực',
                                                'values' => $filterOptions['tinh_trang_hieu_luc'],
                                                'selected' => $selectedFilters['tinh_trang_hieu_luc'],
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($filterGroups as $group)
                                        <div class="filter-group mb-3">
                                            <button
                                                class="btn btn-link p-0 text-start text-dark text-decoration-none d-flex justify-content-between align-items-center w-100 filter-group-title"
                                                type="button" aria-expanded="true"
                                                onclick="toggleFilterGroup('{{ $group['key'] }}-options', this)">
                                                <span class="fw-semibold">{{ $group['label'] }}</span>
                                                <i id="{{ $group['key'] }}-icon" class="fa-solid fa-chevron-up small"></i>
                                            </button>
                                            <div id="{{ $group['key'] }}-options" class="collapse show mt-2">
                                                <div class="filter-option-list">
                                                    @foreach ($group['values'] as $value)
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="{{ $group['key'] }}[]" value="{{ $value }}"
                                                                id="{{ $group['key'] }}-{{ Str::slug($value) }}"
                                                                {{ in_array($value, $group['selected'], true) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="{{ $group['key'] }}-{{ Str::slug($value) }}">{{ $value }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary btn-sm" type="submit">Áp dụng</button>
                                        <a href="{{ route('law.search') }}" class="btn btn-outline-secondary btn-sm">Đặt
                                            lại</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        function toggleFilterGroup(id, trigger) {
            const panel = document.getElementById(id);
            const icon = trigger.querySelector('i');
            const expanded = trigger.getAttribute('aria-expanded') === 'true';

            if (panel) {
                panel.classList.toggle('show', !expanded);
            }

            if (icon) {
                icon.classList.toggle('fa-chevron-up', expanded);
                icon.classList.toggle('fa-chevron-down', !expanded);
            }

            trigger.setAttribute('aria-expanded', String(!expanded));
        }
    </script>
@endsection
