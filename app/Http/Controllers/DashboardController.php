<?php

namespace App\Http\Controllers;

use App\Models\Law;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function customerDashboard()
    {
        return view('customer.dashboard');
    }

    public function adminDashboard()
    {
        $totalDocuments = Law::count();
        $activeDocuments = Law::where(function ($query) {
            $query->where('tinh_trang_hieu_luc', 'Có hiệu lực')
                ->orWhere('tinh_trang_hieu_luc', 'Còn hiệu lực');
        })->count();
        $totalUsers = User::count();
        $todaySearches = $this->getTodaySearchesCount();

        $previousMonthDocuments = Law::whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth(),
        ])->count();

        $previousMonthUsers = User::whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth(),
        ])->count();

        $documentChange = $previousMonthDocuments > 0
            ? round((($totalDocuments - $previousMonthDocuments) / $previousMonthDocuments) * 100)
            : 0;

        $userChange = $previousMonthUsers > 0
            ? round((($totalUsers - $previousMonthUsers) / $previousMonthUsers) * 100)
            : 0;

        $searchChange = $this->getPreviousDaySearchesCount() > 0
            ? round((($todaySearches - $this->getPreviousDaySearchesCount()) / $this->getPreviousDaySearchesCount()) * 100)
            : 0;

        $stats = [
            [
                'title' => 'Tổng số văn bản',
                'value' => number_format($totalDocuments),
                'icon' => 'fa-file-contract',
                'change' => ($documentChange >= 0 ? '+' : '') . $documentChange . '%',
                'tone' => 'primary',
            ],
            [
                'title' => 'Văn bản đang hiệu lực',
                'value' => number_format($activeDocuments),
                'icon' => 'fa-check-circle',
                'change' => '+8%',
                'tone' => 'success',
            ],
            [
                'title' => 'Người dùng',
                'value' => number_format($totalUsers),
                'icon' => 'fa-users',
                'change' => ($userChange >= 0 ? '+' : '') . $userChange . '%',
                'tone' => 'warning',
            ],
            [
                'title' => 'Lượt tra cứu hôm nay',
                'value' => number_format($todaySearches),
                'icon' => 'fa-chart-line',
                'change' => ($searchChange >= 0 ? '+' : '') . $searchChange . '%',
                'tone' => 'info',
            ],
        ];

        $sectorStats = Law::query()
            ->select('linh_vuc')
            ->selectRaw('count(*) as total')
            ->whereNotNull('linh_vuc')
            ->where('linh_vuc', '<>', '')
            ->groupBy('linh_vuc')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->linh_vuc ?: 'Không xác định',
                    'value' => (int) $item->total,
                ];
            });

        if ($sectorStats->isEmpty()) {
            $sectorStats = collect([
                ['name' => 'Chưa có dữ liệu', 'value' => 0],
            ]);
        }

        $searchTrend = $this->getSearchTrend();

        $documents = Law::query()
            ->latest('ngay_ban_hanh')
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(function ($law) {
                return [
                    'id' => $law->id,
                    'stt' => null,
                    'code' => $law->so_ky_hieu ?: '—',
                    'title' => $law->title ?: '—',
                    'field' => $law->linh_vuc ?: '—',
                    'date' => $law->ngay_ban_hanh ? Carbon::parse($law->ngay_ban_hanh)->format('d/m/Y') : '—',
                    'status' => $law->tinh_trang_hieu_luc ?: '—',
                    'badge' => $this->getStatusBadge($law->tinh_trang_hieu_luc),
                ];
            });

        $documents->each(function ($item, $index) {
            $item['stt'] = $index + 1;
        });

        return view('pages.dashboard', compact('stats', 'sectorStats', 'searchTrend', 'documents'));
    }

    protected function getTodaySearchesCount(): int
    {
        return collect(session('recent_searches', []))
            ->filter(function ($item) {
                return !empty($item['created_at']) && Carbon::parse($item['created_at'])->isToday();
            })
            ->count();
    }

    protected function getPreviousDaySearchesCount(): int
    {
        return collect(session('recent_searches', []))
            ->filter(function ($item) {
                return !empty($item['created_at']) && Carbon::parse($item['created_at'])->isYesterday();
            })
            ->count();
    }

    protected function getSearchTrend(): array
    {
        $history = collect(session('recent_searches', []));
        $trend = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $count = $history->filter(function ($item) use ($date) {
                return !empty($item['created_at']) && Carbon::parse($item['created_at'])->toDateString() === $date;
            })->count();

            $trend[] = [
                'label' => Carbon::parse($date)->format('d'),
                'value' => $count,
            ];
        }

        return $trend;
    }

    protected function getStatusBadge(?string $status): string
    {
        if ($status === null) {
            return 'secondary';
        }

        return match ($status) {
            'Có hiệu lực', 'Còn hiệu lực' => 'success',
            'Sắp hiệu lực' => 'warning',
            default => 'secondary',
        };
    }
}
