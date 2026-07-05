@extends('layouts.app')

@section('title', 'Văn bản yêu thích')

@section('content')
    <main class="container py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
            <div>
                <h1 class="h3 fw-bold mb-1">Văn bản yêu thích</h1>
                <p class="text-soft mb-0">Quản lý danh sách văn bản bạn đã lưu lại.</p>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-3">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Tìm kiếm</label>
                        <input type="text" name="q" class="form-control" value="{{ $query }}" placeholder="Số hiệu, tiêu đề, loại văn bản...">
                    </div>
                    {{-- <div class="col-md-3">
                        <label class="form-label small fw-semibold">Trạng thái</label>
                        <input type="text" name="status" class="form-control" value="{{ $status }}" placeholder="VD: Có hiệu lực">
                    </div> --}}
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Sắp xếp</label>
                        <select name="sort" class="form-select">
                            <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Mới thêm</option>
                            <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Lọc</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 70px;">STT</th>
                            <th style="width: 140px;">Số hiệu</th>
                            <th>Tên văn bản</th>
                            <th style="width: 160px;">Loại văn bản</th>
                            <th style="width: 170px;">Trạng thái hiệu lực</th>
                            <th style="width: 140px;">Ngày thêm</th>
                            <th style="width: 120px;" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($favorites as $index => $favorite)
                            @php $law = $favorite->law; @endphp
                            <tr>
                                <td>{{ $favorites->firstItem() + $index }}</td>
                                <td class="fw-semibold">{{ $law->so_ky_hieu ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('law.show', $law->id) }}" class="text-decoration-none fw-semibold text-dark" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 1.4; max-height: 2.8em;">{{ $law->title }}</a>
                                </td>
                                <td>{{ $law->loai_van_ban ?? '—' }}</td>
                                <td>{{ $law->tinh_trang_hieu_luc ?? '—' }}</td>
                                <td>{{ $favorite->created_at ? $favorite->created_at->format('d/m/Y H:i') : '—' }}</td>
                                <td class="text-center">
                                    <form method="POST" action="{{ route('favorites.destroy', $law->id) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Bỏ yêu thích</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="alert alert-info mb-0">Bạn chưa có văn bản yêu thích nào.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($favorites->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $favorites->links() }}
            </div>
        @endif
    </main>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const buttons = document.querySelectorAll('.favorite-toggle');

                buttons.forEach(function (button) {
                    button.addEventListener('click', async function () {
                        const lawId = this.dataset.lawId;
                        const action = this.dataset.action;

                        if (action !== 'remove') {
                            return;
                        }

                        this.disabled = true;
                        this.textContent = 'Đang xử lý...';

                        try {
                            const response = await fetch('{{ url('/api/favorites') }}/' + lawId, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                            });
                            const data = await response.json();
                            if (!response.ok || !data.success) {
                                throw new Error(data.message || 'Không thể bỏ yêu thích');
                            }
                            window.location.reload();
                        } catch (error) {
                            this.disabled = false;
                            this.textContent = 'Bỏ yêu thích';
                            showFavoriteToast(error.message || 'Đã xảy ra lỗi');
                        }
                    });
                });

                window.showFavoriteToast = function (message) {
                    const toast = document.createElement('div');
                    toast.className = 'position-fixed top-0 end-0 p-3';
                    toast.style.zIndex = '1080';
                    toast.innerHTML = '<div class="toast show align-items-center text-bg-danger border-0" role="alert"><div class="d-flex"><div class="toast-body">' + message + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>';
                    document.body.appendChild(toast);
                    setTimeout(function () {
                        toast.remove();
                    }, 3000);
                };
            });
        </script>
    @endpush
@endsection
