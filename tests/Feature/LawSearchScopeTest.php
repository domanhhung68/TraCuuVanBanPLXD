<?php

namespace Tests\Feature;

use App\Models\Law;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LawSearchScopeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
        ]);

        Schema::dropIfExists('laws');
        Schema::create('laws', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('so_ky_hieu')->nullable();
            $table->text('content_html')->nullable();
            $table->timestamps();
        });
    }

    public function test_it_searches_only_by_title_when_title_scope_is_selected(): void
    {
        $this->createLaw([
            'title' => 'Quy định về lao động',
            'so_ky_hieu' => 'Số 123/QĐ-TTg',
            'content_html' => 'Nội dung về bảo hiểm xã hội',
        ]);

        $response = $this->get(route('law.search', ['q' => 'Số 123', 'search_scope' => 'title']));

        $response->assertStatus(200);
        $response->assertSee('Không tìm thấy văn bản nào phù hợp');
    }

    public function test_it_searches_only_by_document_number_when_document_number_scope_is_selected(): void
    {
        $this->createLaw([
            'title' => 'Quy định về lao động',
            'so_ky_hieu' => 'Số 123/QĐ-TTg',
            'content_html' => 'Nội dung về bảo hiểm xã hội',
        ]);

        $response = $this->get(route('law.search', ['q' => 'lao động', 'search_scope' => 'so_ky_hieu']));

        $response->assertStatus(200);
        $response->assertSee('Không tìm thấy văn bản nào phù hợp');
    }

    public function test_it_searches_only_by_content_when_content_scope_is_selected(): void
    {
        $this->createLaw([
            'title' => 'Quy định về lao động',
            'so_ky_hieu' => 'Số 123/QĐ-TTg',
            'content_html' => 'Nội dung về bảo hiểm xã hội',
        ]);

        $response = $this->get(route('law.search', ['q' => 'Quy định', 'search_scope' => 'content_html']));

        $response->assertStatus(200);
        $response->assertSee('Không tìm thấy văn bản nào phù hợp');
    }

    private function createLaw(array $attributes): void
    {
        Law::create($attributes);
    }
}
