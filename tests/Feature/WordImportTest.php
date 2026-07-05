<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class WordImportTest extends TestCase
{
    public function test_admin_can_import_docx_into_html(): void
    {
        $file = new UploadedFile(
            base_path('public/ND 258.2026 Final.docx'),
            'ND 258.2026 Final.docx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            null,
            true
        );

        $response = $this->postJson(route('admin.ui.law.import-word'), [
            'document' => $file,
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $this->assertNotEmpty($response->json('html'));
        $this->assertStringContainsString('<p', $response->json('html'));
    }
}
