<?php

namespace Tests\Unit\Services;

use App\Services\OfficeOpenXmlVerifier;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class OfficeOpenXmlVerifierTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tmpDir = sys_get_temp_dir() . '/ooxml_verifier_tests_' . uniqid();
        mkdir($this->tmpDir);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->tmpDir . '/*') as $f) {
            @unlink($f);
        }
        @rmdir($this->tmpDir);
        parent::tearDown();
    }

    public function test_valid_docx_is_detected(): void
    {
        $path = $this->tmpDir . '/valid.docx';
        $this->makeFakeOoxml($path, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $verifier = new OfficeOpenXmlVerifier();
        $this->assertTrue($verifier->isDocx($path));
        $this->assertFalse($verifier->isXlsx($path));
    }

    public function test_valid_xlsx_is_detected(): void
    {
        $path = $this->tmpDir . '/valid.xlsx';
        $this->makeFakeOoxml($path, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $verifier = new OfficeOpenXmlVerifier();
        $this->assertTrue($verifier->isXlsx($path));
        $this->assertFalse($verifier->isDocx($path));
    }

    public function test_empty_zip_is_rejected(): void
    {
        $path = $this->tmpDir . '/empty.zip';
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE);
        $zip->addFromString('dummy.txt', 'hello');
        $zip->close();

        $verifier = new OfficeOpenXmlVerifier();
        $this->assertFalse($verifier->isDocx($path));
        $this->assertFalse($verifier->isXlsx($path));
    }

    public function test_non_zip_is_rejected(): void
    {
        $path = $this->tmpDir . '/not-a-zip.docx';
        file_put_contents($path, 'this is plain text, not a zip');

        $verifier = new OfficeOpenXmlVerifier();
        $this->assertFalse($verifier->isDocx($path));
    }

    private function makeFakeOoxml(string $path, string $contentType): void
    {
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE);
        $zip->addFromString(
            '[Content_Types].xml',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Override PartName="/word/document.xml" ContentType="' . $contentType . '"/>
</Types>'
        );
        $zip->close();
    }
}
