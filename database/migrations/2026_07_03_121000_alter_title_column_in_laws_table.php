<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('laws')) {
            Schema::dropIfExists('laws');
        }

        Schema::create('laws', function (Blueprint $table) {
            $table->id();
            $table->string('source_id')->nullable()->unique();
            $table->text('title')->nullable();
            $table->string('so_ky_hieu')->nullable();
            $table->string('ngay_ban_hanh')->nullable();
            $table->string('loai_van_ban')->nullable();
            $table->string('ngay_co_hieu_luc')->nullable();
            $table->string('ngay_het_hieu_luc')->nullable();
            $table->string('nguon_thu_thap')->nullable();
            $table->string('ngay_dang_cong_bao')->nullable();
            $table->string('nganh')->nullable();
            $table->string('linh_vuc')->nullable();
            $table->string('co_quan_ban_hanh')->nullable();
            $table->string('chuc_danh')->nullable();
            $table->string('nguoi_ky')->nullable();
            $table->string('pham_vi')->nullable();
            $table->text('thong_tin_ap_dung')->nullable();
            $table->string('tinh_trang_hieu_luc')->nullable();
            $table->longText('content_html')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laws');
    }
};
