<?php

namespace Tests\Unit\Rules;

use App\Rules\SafeUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use ZipArchive;

class SafeUploadTest extends TestCase
{
    public function test_valid_pdf_passes(): void
    {
        $file = UploadedFile::fake()->createWithContent(
            'test.pdf',
            "%PDF-1.4\n%%EOF\n"
        );

        $rule = new SafeUpload(
            allowedMimes: ['application/pdf'],
            allowedExts: ['pdf'],
            maxSizeKb: 1024,
        );

        $v = Validator::make(['file' => $file], ['file' => ['file', $rule]]);
        $this->assertTrue($v->passes(), json_encode($v->errors()->toArray()));
    }

    public function test_exe_renamed_pdf_is_rejected(): void
    {
        $file = UploadedFile::fake()->createWithContent(
            'trojan.pdf',
            "MZ\x90\x00\x03\x00\x00\x00"
        );

        $rule = new SafeUpload(
            allowedMimes: ['application/pdf'],
            allowedExts: ['pdf'],
            maxSizeKb: 1024,
        );

        $v = Validator::make(['file' => $file], ['file' => ['file', $rule]]);
        $this->assertFalse($v->passes());
    }

    public function test_valid_docx_passes(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'docx_');
        $this->makeFakeOoxml($path, 'wordprocessingml');

        $file = new UploadedFile($path, 'article.docx', 'application/zip', null, true);

        $rule = new SafeUpload(
            allowedMimes: ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
            allowedExts: ['doc', 'docx'],
            maxSizeKb: 1024,
        );

        $v = Validator::make(['file' => $file], ['file' => ['file', $rule]]);
        $this->assertTrue($v->passes(), json_encode($v->errors()->toArray()));
    }

    public function test_plain_zip_renamed_docx_is_rejected(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'fakezip_');
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('hello.txt', 'not an office document');
        $zip->close();

        $file = new UploadedFile($path, 'fake.docx', 'application/zip', null, true);

        $rule = new SafeUpload(
            allowedMimes: ['application/zip'],
            allowedExts: ['doc', 'docx'],
            maxSizeKb: 1024,
        );

        $v = Validator::make(['file' => $file], ['file' => ['file', $rule]]);
        $this->assertFalse($v->passes());
    }

    public function test_oversized_file_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('huge.pdf', 2048);

        $rule = new SafeUpload(
            allowedMimes: ['application/pdf'],
            allowedExts: ['pdf'],
            maxSizeKb: 1024,
        );

        $v = Validator::make(['file' => $file], ['file' => ['file', $rule]]);
        $this->assertFalse($v->passes());
    }

    public function test_disallowed_extension_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('malware.exe', 10);

        $rule = new SafeUpload(
            allowedMimes: ['application/pdf'],
            allowedExts: ['pdf'],
            maxSizeKb: 1024,
        );

        $v = Validator::make(['file' => $file], ['file' => ['file', $rule]]);
        $this->assertFalse($v->passes());
    }

    private function makeFakeOoxml(string $path, string $needle): void
    {
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString(
            '[Content_Types].xml',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.' . $needle . '.document"/>
</Types>'
        );
        $zip->close();
    }
}
