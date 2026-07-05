<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Xóa bảng cũ nếu tồn tại
        Schema::dropIfExists('laws');

        // Tạo lại bảng
        Schema::create('laws', function (Blueprint $table) {
            $table->id();

            // ID của nguồn dữ liệu
            $table->unsignedBigInteger('source_id')->unique();

            // Thông tin văn bản
            $table->text('title');
            $table->string('so_ky_hieu')->nullable();
            $table->string('loai_van_ban')->nullable();

            // Ngày tháng
            $table->date('ngay_ban_hanh')->nullable();
            $table->date('ngay_co_hieu_luc')->nullable();
            $table->date('ngay_het_hieu_luc')->nullable();
            $table->string('ngay_dang_cong_bao')->nullable();

            // Phân loại
            $table->string('nganh')->nullable()->index();
            $table->string('linh_vuc')->nullable()->index();

            // Cơ quan ban hành
            $table->string('co_quan_ban_hanh')->nullable()->index();
            $table->string('chuc_danh')->nullable();
            $table->string('nguoi_ky')->nullable();

            // Thông tin khác
            $table->text('pham_vi')->nullable();
            $table->text('thong_tin_ap_dung')->nullable();
            $table->string('tinh_trang_hieu_luc')->nullable()->index();
            $table->string('nguon_thu_thap')->nullable();

            // Nội dung văn bản
            $table->longText('content_html')->nullable();

            $table->timestamps();

            // Index phục vụ tìm kiếm
            $table->index('so_ky_hieu');
            $table->index('ngay_ban_hanh');
            $table->index('ngay_co_hieu_luc');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laws');
    }
};