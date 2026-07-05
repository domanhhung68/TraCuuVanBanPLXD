<?php

namespace App\Console\Commands;

use App\Models\Law;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportLawsCommand extends Command
{
    protected $signature = 'laws:import {--file=}';

    protected $description = 'Import laws from a JSON file into the laws table';

    public function handle(): int
    {
        $file = $this->option('file') ?: base_path('laws.json');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $json = file_get_contents($file);
        $items = json_decode($json, true);

        if (!is_array($items)) {
            $this->error('The JSON is invalid.');
            return 1;
        }

        $count = 0;
        foreach ($items as $item) {
            if (empty($item['id'])) {
                continue;
            }

            $data = [
                'source_id' => (int) $item['id'],
                'title' => $item['title'] ?? null,
                'so_ky_hieu' => $item['so_ky_hieu'] ?? null,
                'loai_van_ban' => $item['loai_van_ban'] ?? null,
                'ngay_ban_hanh' => $this->parseDate($item['ngay_ban_hanh'] ?? null),
                'ngay_co_hieu_luc' => $this->parseDate($item['ngay_co_hieu_luc'] ?? null),
                'ngay_het_hieu_luc' => $this->parseDate($item['ngay_het_hieu_luc'] ?? null),
                'ngay_dang_cong_bao' => $item['ngay_dang_cong_bao'] ?? null,
                'nganh' => $item['nganh'] ?? null,
                'linh_vuc' => $item['linh_vuc'] ?? null,
                'co_quan_ban_hanh' => $item['co_quan_ban_hanh'] ?? null,
                'chuc_danh' => $item['chuc_danh'] ?? null,
                'nguoi_ky' => $item['nguoi_ky'] ?? null,
                'pham_vi' => $item['pham_vi'] ?? null,
                'thong_tin_ap_dung' => $item['thong_tin_ap_dung'] ?? null,
                'tinh_trang_hieu_luc' => $item['tinh_trang_hieu_luc'] ?? null,
                'nguon_thu_thap' => $item['nguon_thu_thap'] ?? null,
                'content_html' => $item['content_html'] ?? null,
            ];

            Law::updateOrCreate(['source_id' => $data['source_id']], $data);
            $count++;
        }

        $this->info("Imported {$count} law records.");
        return 0;
    }

    protected function parseDate(?string $value): ?string
    {
        if (empty($value) || $value === '...' || $value === '?') {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
