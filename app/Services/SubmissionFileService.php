<?php

namespace App\Services;

use App\Models\Submission;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionFileService
{
    public const TYPE_MANUSCRIPT   = 'manuscript';
    public const TYPE_SUPPLEMENTARY = 'supplementary';
    public const TYPE_REVISIONS    = 'revisions';
    public const TYPE_FIGURES      = 'figures';

    public const ALLOWED_TYPES = [
        self::TYPE_MANUSCRIPT,
        self::TYPE_SUPPLEMENTARY,
        self::TYPE_REVISIONS,
        self::TYPE_FIGURES,
    ];

    public function store(Submission $submission, UploadedFile $file, string $type): array
    {
        if (!in_array($type, self::ALLOWED_TYPES, true)) {
            throw new \InvalidArgumentException("Type de fichier inconnu : {$type}");
        }

        $ext = strtolower($file->getClientOriginalExtension());
        $filename = (string) Str::uuid() . ($ext ? ".{$ext}" : '');
        $relativePath = "{$submission->id}/{$type}/{$filename}";

        Storage::disk('submissions')->putFileAs(
            "{$submission->id}/{$type}",
            $file,
            $filename
        );

        return [
            'path' => $relativePath,
            'original_filename' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ];
    }

    public function download(Submission $submission, string $relativePath): StreamedResponse
    {
        $this->assertBelongsTo($submission, $relativePath);

        if (!Storage::disk('submissions')->exists($relativePath)) {
            abort(404);
        }

        return Storage::disk('submissions')->download($relativePath);
    }

    public function delete(Submission $submission, string $relativePath): void
    {
        $this->assertBelongsTo($submission, $relativePath);
        Storage::disk('submissions')->delete($relativePath);
    }

    private function assertBelongsTo(Submission $submission, string $relativePath): void
    {
        if (str_contains($relativePath, '..') || !str_starts_with($relativePath, "{$submission->id}/")) {
            abort(403, 'Chemin de fichier invalide.');
        }
    }
}
