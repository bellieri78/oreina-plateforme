<?php

namespace App\Services;

use ZipArchive;

class OfficeOpenXmlVerifier
{
    public function isDocx(string $filePath): bool
    {
        return $this->contentTypeContains($filePath, 'wordprocessingml');
    }

    public function isXlsx(string $filePath): bool
    {
        return $this->contentTypeContains($filePath, 'spreadsheetml');
    }

    private function contentTypeContains(string $filePath, string $needle): bool
    {
        if (!is_file($filePath)) {
            return false;
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return false;
        }

        $contentTypes = $zip->getFromName('[Content_Types].xml');
        $zip->close();

        if ($contentTypes === false) {
            return false;
        }

        return str_contains($contentTypes, $needle);
    }
}
