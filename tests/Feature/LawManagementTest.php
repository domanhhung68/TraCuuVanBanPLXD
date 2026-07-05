<?php

namespace Tests\Feature;

use App\Models\Law;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LawManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_laws(): void
    {
        Law::create([
            'source_id' => 1001,
            'title' => 'Nghị định về đất đai',
            'so_ky_hieu' => 'NĐ 01',
            'loai_van_ban' => 'Nghị định',
            'nganh' => 'Nông nghiệp',
            'linh_vuc' => 'Đất đai',
            'co_quan_ban_hanh' => 'Chính phủ',
            'tinh_trang_hieu_luc' => 'Có hiệu lực',
            'content_html' => '<p>Đất đai</p>',
        ]);

        Law::create([
            'source_id' => 1002,
            'title' => 'Thông tư về bảo hiểm',
            'so_ky_hieu' => 'TT 02',
            'loai_van_ban' => 'Thông tư',
            'nganh' => 'Tài chính',
            'linh_vuc' => 'Bảo hiểm',
            'co_quan_ban_hanh' => 'Bộ Tài chính',
            'tinh_trang_hieu_luc' => 'Sắp hiệu lực',
            'content_html' => '<p>Bảo hiểm</p>',
        ]);

        $response = $this->get(route('admin.ui.legal-documents', ['q' => 'đất', 'linh_vuc' => 'Đất đai']));

        $response->assertOk();
        $response->assertSee('Nghị định về đất đai');
        $response->assertDontSee('Thông tư về bảo hiểm');
    }

    public function test_admin_can_create_update_and_delete_law(): void
    {
        $response = $this->post(route('admin.ui.law.store'), [
            'title' => 'Quyết định mới',
            'so_ky_hieu' => 'QĐ 99',
            'loai_van_ban' => 'Quyết định',
            'linh_vuc' => 'Xây dựng',
            'co_quan_ban_hanh' => 'Bộ Xây dựng',
            'tinh_trang_hieu_luc' => 'Có hiệu lực',
            'content_html' => '<p>Nội dung mới</p>',
        ]);

        $response->assertRedirect(route('admin.ui.legal-documents'));
        $this->assertDatabaseHas('laws', ['title' => 'Quyết định mới']);

        $law = Law::where('title', 'Quyết định mới')->firstOrFail();

        $updateResponse = $this->put(route('admin.ui.law.update', $law), [
            'title' => 'Quyết định cập nhật',
            'so_ky_hieu' => 'QĐ 99',
            'loai_van_ban' => 'Quyết định',
            'linh_vuc' => 'Xây dựng',
            'co_quan_ban_hanh' => 'Bộ Xây dựng',
            'tinh_trang_hieu_luc' => 'Có hiệu lực',
            'content_html' => '<p>Nội dung mới</p>',
        ]);

        $updateResponse->assertRedirect(route('admin.ui.legal-documents'));
        $this->assertDatabaseHas('laws', ['id' => $law->id, 'title' => 'Quyết định cập nhật']);

        $deleteResponse = $this->delete(route('admin.ui.law.destroy', $law));

        $deleteResponse->assertRedirect(route('admin.ui.legal-documents'));
        $this->assertDatabaseMissing('laws', ['id' => $law->id]);
    }
}
