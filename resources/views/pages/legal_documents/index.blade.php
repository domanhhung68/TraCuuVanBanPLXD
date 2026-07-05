@extends('layouts.app')

@section('title', 'Quản lý Văn bản Pháp luật')
@section('layout_type', 'admin')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Quản lý Văn bản Pháp luật</h2>
            <p class="text-muted mb-0">Theo dõi, tìm kiếm và quản lý toàn bộ văn bản trong hệ thống.</p>
        </div>
        <a href="{{ route('admin.ui.law.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus me-2"></i>Thêm văn bản
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.ui.legal-documents') }}" class="row g-3">
                <div class="col-12 col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Từ khóa</label>
                    <input type="text" name="q" value="{{ old('q', $query) }}" class="form-control" placeholder="Nhập từ khóa">
                </div>
                <div class="col-12 col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Lĩnh vực</label>
                    <select name="linh_vuc" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach ($fields as $field)
                            <option value="{{ $field }}" {{ old('linh_vuc', $filters['linh_vuc']) === $field ? 'selected' : '' }}>{{ $field }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Cơ quan ban hành</label>
                    <select name="co_quan_ban_hanh" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach ($agencies as $agency)
                            <option value="{{ $agency }}" {{ old('co_quan_ban_hanh', $filters['co_quan_ban_hanh']) === $agency ? 'selected' : '' }}>{{ $agency }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Trạng thái</label>
                    <select name="tinh_trang_hieu_luc" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" {{ old('tinh_trang_hieu_luc', $filters['tinh_trang_hieu_luc']) === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Ngày ban hành</label>
                    <input type="date" name="ngay_ban_hanh" value="{{ old('ngay_ban_hanh', $filters['ngay_ban_hanh']) }}" class="form-control">
                </div>
                <div class="col-12 col-md-6 col-xl-2 d-flex align-items-end gap-2">
                    <button class="btn btn-primary w-100"><i class="fas fa-search me-2"></i>Tìm</button>
                    <a href="{{ route('admin.ui.legal-documents') }}" class="btn btn-outline-secondary"><i class="fas fa-rotate-left"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Số hiệu</th>
                            <th>Tên văn bản</th>
                            <th>Loại văn bản</th>
                            <th>Lĩnh vực</th>
                            <th>Cơ quan ban hành</th>
                            <th>Ngày ban hành</th>
                            <th>Hiệu lực</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laws as $law)
                            <tr>
                                <td>{{ $loop->iteration + ($laws->perPage() * ($laws->currentPage() - 1)) }}</td>
                                <td class="fw-semibold">{{ $law->so_ky_hieu ?? '—' }}</td>
                                <td>{{ $law->title }}</td>
                                <td>{{ $law->loai_van_ban ?? '—' }}</td>
                                <td>{{ $law->linh_vuc ?? '—' }}</td>
                                <td>{{ $law->co_quan_ban_hanh ?? '—' }}</td>
                                <td>{{ $law->ngay_ban_hanh ? \Illuminate\Support\Carbon::parse($law->ngay_ban_hanh)->format('d/m/Y') : '—' }}</td>
                                <td><span class="badge bg-success-subtle text-success">{{ $law->tinh_trang_hieu_luc ?? '—' }}</span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('law.show', $law->id) }}" target="_blank"><i class="fas fa-eye"></i></a>
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.ui.law.edit', $law) }}"><i class="fas fa-edit"></i></a>
                                        <form method="POST" action="{{ route('admin.ui.law.destroy', $law) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa văn bản này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Chưa có văn bản nào phù hợp với bộ lọc hiện tại.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $laws->links() }}
            </div>
        </div>
    </div>
@endsection
