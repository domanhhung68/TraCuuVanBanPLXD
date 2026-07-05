@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('layout_type', 'admin')

@section('content')
    @php
    $stats = $stats ?? [
        ['title' => 'Tổng số văn bản', 'value' => '0', 'icon' => 'fa-file-contract', 'change' => '+0%', 'tone' => 'primary'],
        ['title' => 'Văn bản đang hiệu lực', 'value' => '0', 'icon' => 'fa-check-circle', 'change' => '+0%', 'tone' => 'success'],
        ['title' => 'Người dùng', 'value' => '0', 'icon' => 'fa-users', 'change' => '+0%', 'tone' => 'warning'],
        ['title' => 'Lượt tra cứu hôm nay', 'value' => '0', 'icon' => 'fa-chart-line', 'change' => '+0%', 'tone' => 'info'],
    ];
    $sectors = $sectorStats ?? collect();
    $trends = $searchTrend ?? collect();
    $documents = $documents ?? collect();
    $trendValues = collect($trends)->pluck('value')->all();
    $maxTrendValue = max(1, ...$trendValues);
    $trendPoints = collect($trends)->map(function ($trend, $index) use ($maxTrendValue) {
        $x = 20 + ($index * 60);
        $y = 155 - (($trend['value'] / $maxTrendValue) * 110);
        return ['x' => $x, 'y' => $y, 'label' => $trend['label'], 'value' => $trend['value']];
    });
@endphp

    <div class="row g-4 mb-4">
        @foreach ($stats as $stat)
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card dashboard-card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2">{{ $stat['title'] }}</p>
                            <h3 class="fw-bold mb-2">{{ $stat['value'] }}</h3>
                            <span class="badge bg-{{ $stat['tone'] }}-subtle text-{{ $stat['tone'] }}">{{ $stat['change'] }} so với tháng trước</span>
                        </div>
                        <div class="stat-icon bg-{{ $stat['tone'] }}-subtle text-{{ $stat['tone'] }}">
                            <i class="fas {{ $stat['icon'] }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Số lượng văn bản theo lĩnh vực</h5>
                        <span class="badge bg-light text-primary">Thống kê</span>
                    </div>
                    <div class="chart-bars">
                        @foreach ($sectors as $sector)
                            <div class="bar-item" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $sector['name'] }}: {{ $sector['value'] }} văn bản">
                                <div class="bar-track">
                                    <div class="bar-fill" style="height: {{ max(12, min(100, ($sector['value'] / max(1, $sectors->max('value'))) * 100)) }}%"></div>
                                </div>
                                <span class="bar-label">{{ $sector['name'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Lượt tra cứu theo ngày</h5>
                        <span class="badge bg-light text-primary">7 ngày qua</span>
                    </div>
                    <div class="line-chart">
                        <div class="line-chart-grid"></div>
                        <svg viewBox="0 0 360 180" class="w-100">
                            <path d="M20 155 L{{ $trendPoints->implode('x', ' L') }}" fill="none" stroke="#2563EB" stroke-width="3"></path>
                            @foreach ($trendPoints as $point)
                                <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="5" fill="#2563EB" data-bs-toggle="tooltip" data-bs-placement="top" title="Ngày {{ $point['label'] }}: {{ $point['value'] }} lượt tra cứu"></circle>
                            @endforeach
                        </svg>
                        <div class="d-flex justify-content-between text-muted small mt-2">
                            @foreach ($trends as $trend)
                                <span>{{ $trend['label'] }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Văn bản mới nhất</h5>
                <a href="/admin-ui/legal-documents" class="btn btn-outline-primary btn-sm">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Số hiệu</th>
                            <th>Tên văn bản</th>
                            <th>Lĩnh vực</th>
                            <th>Ngày ban hành</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $document)
                            <tr>
                                <td>{{ $document['stt'] }}</td>
                                <td class="fw-semibold">{{ $document['code'] }}</td>
                                <td>{{ $document['title'] }}</td>
                                <td>{{ $document['field'] }}</td>
                                <td>{{ $document['date'] }}</td>
                                <td><span class="badge bg-{{ $document['badge'] }}-subtle text-{{ $document['badge'] }}">{{ $document['status'] }}</span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('law.show', ['id' => $document['id']]) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.ui.law.edit', $document['id']) }}"><i class="fas fa-edit"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.bootstrap && typeof window.bootstrap.Tooltip === 'function') {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {
                new bootstrap.Tooltip(element);
            });
        }
    });
</script>
@endpush
