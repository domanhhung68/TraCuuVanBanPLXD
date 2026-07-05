<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpWord\IOFactory;
use RuntimeException;
use HTMLPurifier;
use HTMLPurifier_Config;

class ImportWordService
{
    /**
     * Convert a DOCX file to sanitized HTML.
     * This service is designed to be extended later for PDF import.
     */
    public function import(UploadedFile $file): string
    {
        if ($file->getClientOriginalExtension() !== 'docx') {
            throw new RuntimeException('Chỉ hỗ trợ file .docx.');
        }

        $tmpPath = $file->getRealPath();
        if ($tmpPath === false || !is_file($tmpPath)) {
            throw new RuntimeException('Không thể đọc file Word đã upload.');
        }

        $content = $this->convertDocxToHtml($tmpPath);
        $html = $this->normalizeHtml($content);

        return $this->sanitizeHtml($html);
    }

    protected function convertDocxToHtml(string $path): string
    {
        $phpWord = IOFactory::load($path);
        $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');

        $tempFile = tempnam(sys_get_temp_dir(), 'word-html-');
        if ($tempFile === false) {
            throw new RuntimeException('Không thể tạo file tạm để chuyển đổi.');
        }

        $htmlWriter->save($tempFile);
        $html = file_get_contents($tempFile);
        @unlink($tempFile);

        if ($html === false) {
            throw new RuntimeException('Không thể đọc nội dung từ file Word.');
        }

        return $html;
    }

    protected function normalizeHtml(string $html): string
    {
        $html = preg_replace('/<!--.*?-->/s', '', $html) ?? $html;
        $html = preg_replace('/<w:.*?>/i', '', $html) ?? $html;
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html) ?? $html;
        $html = preg_replace('/class="[^"]*mso[^"]*"/i', '', $html) ?? $html;
        $html = preg_replace('/class="[^"]*WordSection[^"]*"/i', '', $html) ?? $html;
        $html = preg_replace('/<\/?div[^>]*class="[^"]*Section[^"]*"[^>]*>/i', '', $html) ?? $html;
        $html = preg_replace('/<\/?(meta|link|title|script|head|body)[^>]*>/i', '', $html) ?? $html;

        $html = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $html) ?? $html;
        $html = preg_replace('/<p[^>]*>\s*<p/i', '<p', $html) ?? $html;
        $html = preg_replace('/<\/p>\s*<\/p>/i', '</p>', $html) ?? $html;

        return trim($html);
    }

    protected function sanitizeHtml(string $html): string
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Allowed', 'p,h1,h2,h3,h4,h5,h6,strong,em,u,a,ul,ol,li,blockquote,table,thead,tbody,tr,th,td,br,img,span');
        $config->set('HTML.AllowedAttributes', 'a.href,a.target,a.rel,img.src,img.alt,img.width,img.height,span.style,td.colspan,td.rowspan,th.colspan,th.rowspan,p.style,h1.style,h2.style,h3.style,h4.style,h5.style,h6.style,li.style,ul.style,ol.style,table.style,th.style,td.style');
        $config->set('CSS.AllowedProperties', ['font-weight','font-style','text-decoration','text-align','margin','margin-top','margin-bottom','margin-left','margin-right','padding','padding-top','padding-bottom','padding-left','padding-right','line-height','color','border','border-collapse','width','height','vertical-align','text-indent']);
        $config->set('AutoFormat.RemoveEmpty', true);
        $config->set('HTML.TidyLevel', 'light');
        $config->set('HTML.ForbiddenElements', ['script', 'style', 'link', 'meta']);

        $purifier = new HTMLPurifier($config);

        return $purifier->purify($html);
    }
}
