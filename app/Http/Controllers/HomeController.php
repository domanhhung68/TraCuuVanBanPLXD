<?php

namespace App\Http\Controllers;

use App\Models\Law;

class HomeController extends Controller
{
    public function index()
    {
        $recentLaws = collect();
        $stats = ['total' => 0, 'active' => 0];
        $featuredField1 = null;
        $featuredField2 = null;
        $featuredField3 = null;
        $featuredField4 = null;

        try {
            $recentLaws = Law::query()->latest('id')->paginate(9);
            $stats['total'] = Law::count();
            $stats['active'] = Law::where('tinh_trang_hieu_luc', 'Có hiệu lực')->count();

            $topFields = Law::query()
                ->select('linh_vuc')
                ->selectRaw('count(*) as total')
                ->whereNotNull('linh_vuc')
                ->where('linh_vuc', '<>', '')
                ->groupBy('linh_vuc')
                ->orderByDesc('total')
                ->orderBy('linh_vuc')
                ->limit(4)
                ->get();

            $featuredField1 = $this->buildFeaturedField($topFields->get(0));
            $featuredField2 = $this->buildFeaturedField($topFields->get(1));
            $featuredField3 = $this->buildFeaturedField($topFields->get(2));
            $featuredField4 = $this->buildFeaturedField($topFields->get(3));
        } catch (\Throwable $e) {
            $recentLaws = collect();
        }

        return view('home', compact(
            'recentLaws',
            'stats',
            'featuredField1',
            'featuredField2',
            'featuredField3',
            'featuredField4'
        ));
    }

    private function buildFeaturedField($field): array
    {
        if (! $field) {
            return [
                'name' => 'Chưa cập nhật',
                'count' => 0,
            ];
        }

        return [
            'name' => $field->linh_vuc ?: 'Chưa cập nhật',
            'count' => (int) ($field->total ?? 0),
        ];
    }
}
