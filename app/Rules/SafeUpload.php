<?php

namespace App\Rules;

use App\Services\OfficeOpenXmlVerifier;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class SafeUpload implements ValidationRule
{
    public function __construct(
        private array $allowedMimes,
        private array $allowedExts,
        private int $maxSizeKb = 51200,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            $fail('Le fichier est invalide.');
            return;
        }

        if (!$value->isValid()) {
            $fail('Le fichier n\'a pas pu être uploadé correctement.');
            return;
        }

        // Taille
        if ($value->getSize() > $this->maxSizeKb * 1024) {
            $fail("Le fichier dépasse la taille maximale autorisée ({$this->maxSizeKb} Ko).");
            return;
        }

        // Extension
        $ext = strtolower($value->getClientOriginalExtension());
        if (!in_array($ext, $this->allowedExts, true)) {
            $fail('Extension de fichier non autorisée. Extensions acceptées : ' . implode(', ', $this->allowedExts) . '.');
            return;
        }

        // MIME via finfo (pas getMimeType qui peut se baser sur l'extension)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $value->getRealPath());
        finfo_close($finfo);

        if (!in_array($mime, $this->allowedMimes, true)) {
            $fail('Le type MIME détecté du fichier n\'est pas autorisé.');
            return;
        }

        // Double-check OOXML pour .docx / .xlsx
        if (in_array($ext, ['docx', 'xlsx'], true)) {
            $verifier = new OfficeOpenXmlVerifier();
            $ok = $ext === 'docx'
                ? $verifier->isDocx($value->getRealPath())
                : $verifier->isXlsx($value->getRealPath());
            if (!$ok) {
                $fail('Le fichier ne semble pas être un document Office valide.');
                return;
            }
        }
    }
}
