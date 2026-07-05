@extends('layouts.app')

@section('title', $law->title)

@section('content')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #lawDetailTabsContent,
            #lawDetailTabsContent * {
                visibility: visible;
            }

            #lawDetailTabsContent {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .no-print,
            .col-lg-4,
            .nav-tabs {
                display: none !important;
            }

            .tab-pane {
                display: none !important;
            }

            #content-pane {
                display: block !important;
            }
        }
    </style>

    <main class="container py-5">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <span class="badge bg-primary mb-3">{{ $law->loai_van_ban ?? 'Văn bản' }}</span>
                        <h1 class="h3 fw-bold mb-3">{{ $law->title }}</h1>
                        <p class="text-soft mb-4">{{ $law->so_ky_hieu ?? '—' }}</p>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="fw-semibold">Cơ quan ban hành</div>
                                    <div class="text-soft">{{ $law->co_quan_ban_hanh ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="fw-semibold">Hiệu lực</div>
                                    <div class="text-soft">{{ $law->tinh_trang_hieu_luc ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="fw-semibold">Ngày ban hành</div>
                                    <div class="text-soft">{{ $law->ngay_ban_hanh ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="fw-semibold">Ngày có hiệu lực</div>
                                    <div class="text-soft">{{ $law->ngay_co_hieu_luc ?? '—' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-3 no-print">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="print-law-btn">
                                In / Tải PDF
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="share-law-btn">
                                Chia sẻ bài viết
                            </button>
                            @if (Auth::check())
                                <form method="POST" action="{{ route('favorites.store') }}" class="d-inline" id="favorite-law-form">
                                    @csrf
                                    <input type="hidden" name="law_id" value="{{ $law->id }}">
                                    @if ($isFavorite)
                                        <button type="submit" formaction="{{ route('favorites.destroy', $law->id) }}" formmethod="POST" class="btn btn-sm btn-warning" id="favorite-law-btn">
                                            <span>★</span>
                                            <span>Đã yêu thích</span>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-warning" id="favorite-law-btn">
                                            <span>☆</span>
                                            <span>Thêm vào yêu thích</span>
                                        </button>
                                    @endif
                                </form>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-warning" id="favorite-law-btn" data-login-url="{{ route('login') }}">
                                    <span>☆</span>
                                    <span>Thêm vào yêu thích</span>
                                </button>
                            @endif
                        </div>

                        <ul class="nav nav-tabs mb-3" id="lawDetailTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active text-dark" id="content-tab" data-bs-toggle="tab" data-bs-target="#content-pane" type="button" role="tab" aria-controls="content-pane" aria-selected="true">
                                    Nội dung
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link text-dark" id="files-tab" data-bs-toggle="tab" data-bs-target="#files-pane" type="button" role="tab" aria-controls="files-pane" aria-selected="false">
                                    File văn bản
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link text-dark" id="relation-tab" data-bs-toggle="tab" data-bs-target="#relation-pane" type="button" role="tab" aria-controls="relation-pane" aria-selected="false">
                                    Lược đồ quan hệ
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="lawDetailTabsContent">
                            <div class="tab-pane fade show active" id="content-pane" role="tabpanel" aria-labelledby="content-tab">
                                <div class="border rounded p-3 bg-light" id="law-content-block">
                                    {{-- <h2 class="h5 fw-bold mb-3">Nội dung</h2> --}}
                                    <div class="text-soft">
                                        {!! $law->content_html !!}
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="files-pane" role="tabpanel" aria-labelledby="files-tab">
                                <div class="border rounded p-3 bg-light">
                                    <h2 class="h5 fw-bold mb-3">File văn bản</h2>
                                    @if($law->lawFiles->isEmpty())
                                        <div class="text-soft">Chưa có file đính kèm nào.</div>
                                    @else
                                        <div class="list-group">
                                            @foreach($law->lawFiles as $file)
                                                <div class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                                    <div>
                                                        <div class="fw-semibold">{{ $file->original_name }}</div>
                                                        <div class="small text-muted">
                                                            {{ $file->file_type ?: 'file' }} • {{ number_format(max(1, (int) ceil(($file->file_size ?? 0) / 1024)), 0) }} KB
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('law.file.download', $file->id) }}" class="btn btn-outline-primary btn-sm rounded-pill">
                                                        Tải file
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="tab-pane fade" id="relation-pane" role="tabpanel" aria-labelledby="relation-tab">
                                <div class="border rounded p-3 bg-light">
                                    <h2 class="h5 fw-bold mb-3">Lược đồ quan hệ</h2>
                                    <p class="text-muted mb-4">Các quan hệ luật pháp của văn bản này.</p>
                                    @php
                                        $outgoingLabels = [
                                            'MODIFY' => 'Sửa đổi',
                                            'REPLACE' => 'Thay thế',
                                            'ABOLISH' => 'Bãi bỏ',
                                            'GUIDE' => 'Hướng dẫn',
                                            'SỬA ĐỔI' => 'Sửa đổi',
                                            'THAY THẾ' => 'Thay thế',
                                            'BÃI BỎ' => 'Bãi bỏ',
                                            'HƯỚNG DẪN' => 'Hướng dẫn',
                                            'Sửa đổi' => 'Sửa đổi',
                                            'Thay thế' => 'Thay thế',
                                            'Bãi bỏ' => 'Bãi bỏ',
                                            'Hướng dẫn' => 'Hướng dẫn',
                                        ];
                                        $incomingLabels = [
                                            'MODIFY' => 'Được sửa đổi bởi',
                                            'REPLACE' => 'Được thay thế bởi',
                                            'ABOLISH' => 'Bị bãi bỏ bởi',
                                            'GUIDE' => 'Được hướng dẫn bởi',
                                            'SỬA ĐỔI' => 'Được sửa đổi bởi',
                                            'THAY THẾ' => 'Được thay thế bởi',
                                            'BÃI BỎ' => 'Bị bãi bỏ bởi',
                                            'HƯỚNG DẪN' => 'Được hướng dẫn bởi',
                                            'Sửa đổi' => 'Được sửa đổi bởi',
                                            'Thay thế' => 'Được thay thế bởi',
                                            'Bãi bỏ' => 'Bị bãi bỏ bởi',
                                            'Hướng dẫn' => 'Được hướng dẫn bởi',
                                        ];
                                    @endphp
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 h-100">
                                                <h3 class="h6 fw-semibold mb-3">Văn bản này tác động tới</h3>
                                                @if($law->outgoingRelations->isEmpty())
                                                    <div class="text-soft">Không có quan hệ.</div>
                                                @else
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($law->outgoingRelations as $relation)
                                                            @if($relation->toLaw)
                                                                <li class="mb-3">
                                                                    <div class="small text-muted">{{ $outgoingLabels[$relation->relation_type] ?? $relation->relation_type }}</div>
                                                                    <a href="{{ route('law.show', $relation->toLaw->id) }}" class=" text-dark" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 1.4; max-height: 2.8em;">
                                                                        {{ $relation->toLaw->title ?? '—' }}
                                                                    </a>
                                                                    <div class="small text-soft">{{ $relation->toLaw->so_ky_hieu ?? '—' }}</div>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 h-100">
                                                <h3 class="h6 fw-semibold mb-3">Văn bản tác động tới văn bản này</h3>
                                                @if($law->incomingRelations->isEmpty())
                                                    <div class="text-soft">Không có quan hệ.</div>
                                                @else
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($law->incomingRelations as $relation)
                                                            @if($relation->fromLaw)
                                                                <li class="mb-3">
                                                                    <div class="small text-muted">{{ $incomingLabels[$relation->relation_type] ?? $relation->relation_type }}</div>
                                                                    <a href="{{ route('law.show', $relation->fromLaw->id) }}" class=" text-dark" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 1.4; max-height: 2.8em;">
                                                                        {{ $relation->fromLaw->title ?? '—' }}
                                                                    </a>
                                                                    <div class="small text-soft">{{ $relation->fromLaw->so_ky_hieu ?? '—' }}</div>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4 mt-4">
                        <h2 class="h5 fw-bold mb-3">Thông tin chi tiết</h2>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><strong>Ngành:</strong> {{ $law->nganh ?? '—' }}</li>
                            <li class="mb-2"><strong>Lĩnh vực:</strong> {{ $law->linh_vuc ?? '—' }}</li>
                            <li class="mb-2"><strong>Phạm vi:</strong> {{ $law->pham_vi ?? '—' }}</li>
                            <li class="mb-2"><strong>Người ký:</strong> {{ $law->nguoi_ky ?? '—' }}</li>
                            <li class="mb-2"><strong>Chức danh:</strong> {{ $law->chuc_danh ?? '—' }}</li>
                            <li class="mb-2"><strong>Nguồn thu thập:</strong> {{ $law->nguon_thu_thap ?? '—' }}</li>
                        </ul>
                    </div>
                </div>

                @if (session('recent_searches') && count(session('recent_searches')))
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3">Lịch sử tra cứu gần đây</h2>
                            <ul class="list-unstyled mb-0">
                                @foreach (session('recent_searches') as $item)
                                    <li class="mb-2">
                                        <a href="{{ route('law.search', ['q' => $item['query']]) }}" class="text-decoration-none">
                                            {{ $item['query'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @if (session('recent_views') && count(session('recent_views')))
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3">Bạn vừa xem</h2>
                            <ul class="list-unstyled mb-0">
                                @foreach (session('recent_views') as $item)
                                    <li class="mb-2">
                                        <a href="{{ route('law.show', $item['id']) }}" class="text-decoration-none" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 1.4; max-height: 2.8em;">
                                            {{ $item['title'] }}
                                        </a>
                                        <hr>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </main>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const shareButton = document.getElementById('share-law-btn');
            const favoriteButton = document.getElementById('favorite-law-btn');
            const printButton = document.getElementById('print-law-btn');
            const isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};

            function showContentTabForPrint() {
                const contentTab = document.getElementById('content-tab');
                const contentPane = document.getElementById('content-pane');

                if (contentTab && contentPane) {
                    document.querySelectorAll('#lawDetailTabs .nav-link').forEach(function (tab) {
                        tab.classList.remove('active');
                    });

                    document.querySelectorAll('#lawDetailTabsContent .tab-pane').forEach(function (pane) {
                        pane.classList.remove('active', 'show');
                    });

                    contentTab.classList.add('active');
                    contentPane.classList.add('active', 'show');
                }
            }

            if (printButton) {
                printButton.addEventListener('click', function () {
                    showContentTabForPrint();
                    window.setTimeout(function () {
                        window.print();
                    }, 150);
                });
            }

            if (shareButton) {
                shareButton.addEventListener('click', async function () {
                    const shareData = {
                        title: document.title,
                        text: 'Xem văn bản pháp lý này tại TCTLPL',
                        url: window.location.href,
                    };

                    try {
                        if (navigator.share) {
                            await navigator.share(shareData);
                            return;
                        }
                    } catch (error) {
                        console.warn('Share cancelled', error);
                    }

                    try {
                        await navigator.clipboard.writeText(window.location.href);
                        shareButton.textContent = 'Đã sao chép liên kết';
                        setTimeout(() => shareButton.textContent = 'Chia sẻ bài viết', 2000);
                    } catch (error) {
                        shareButton.textContent = 'Không thể chia sẻ';
                    }
                });
            }

            function showFavoriteToast(message, type = 'success', loginUrl = null) {
                const toast = document.createElement('div');
                toast.className = 'position-fixed top-0 end-0 p-3';
                toast.style.zIndex = '1080';
                const color = type === 'danger' ? 'text-bg-danger' : type === 'warning' ? 'text-bg-warning' : 'text-bg-success';
                let body = '<div class="toast show align-items-center ' + color + ' border-0" role="alert"><div class="d-flex flex-column align-items-start gap-2"><div class="toast-body">' + message + '</div>';
                if (loginUrl) {
                    body += '<a href="' + loginUrl + '" class="btn btn-sm btn-light ms-2">Đăng nhập</a>';
                }
                body += '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>';
                toast.innerHTML = body;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 4000);
            }

            if (favoriteButton) {
                favoriteButton.addEventListener('click', function () {
                    if (!isAuthenticated) {
                        const loginUrl = this.getAttribute('data-login-url');
                        showFavoriteToast('Bạn cần đăng nhập để sử dụng chức năng yêu thích.', 'warning', loginUrl);
                    }
                });
            }

            const favoriteForm = document.getElementById('favorite-law-form');
            if (favoriteForm) {
                favoriteForm.addEventListener('submit', function (event) {
                    const submitButton = event.submitter;
                    if (submitButton && submitButton.formMethod === 'POST' && submitButton.getAttribute('formaction')) {
                        const form = this;
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = '_method';
                        hiddenInput.value = 'DELETE';
                        form.appendChild(hiddenInput);
                    }
                });
            }

        });
    </script>
@endpush
@endsection
