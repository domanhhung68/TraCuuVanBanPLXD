<?php

namespace Tests\Feature;

use App\Models\Law;
use Tests\TestCase;

class LawSearchPageTest extends TestCase
{
    public function test_home_page_is_accessible(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Tra cứu văn bản pháp lý');
    }

    public function test_search_page_is_accessible(): void
    {
        $response = $this->get('/tim-van-ban');

        $response->assertStatus(200);
        $response->assertSee('Tìm kiếm văn bản');
    }

    public function test_home_page_displays_pagination_navigation(): void
    {
        Law::query()->delete();

        for ($i = 1; $i <= 10; $i++) {
            Law::create([
                'source_id' => 1000 + $i,
                'title' => 'Văn bản mẫu ' . $i,
                'content_html' => 'Nội dung mẫu ' . $i,
            ]);
        }

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('pagination');
    }

    public function test_home_page_displays_featured_fields_and_links_to_search(): void
    {
        Law::query()->delete();

        $law = Law::create([
            'source_id' => 2000,
            'title' => 'Văn bản mẫu lĩnh vực xây dựng',
            'linh_vuc' => 'Xây dựng',
            'content_html' => 'Nội dung mẫu',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Lĩnh vực tiêu biểu');
        $response->assertSee($law->linh_vuc);
        $response->assertSee(route('law.search', ['linh_vuc' => [$law->linh_vuc]]));
    }
}
