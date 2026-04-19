<?php

namespace App\Services;

use App\Models\Submission;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrossrefCitationService
{
    private const SYNC_TTL_DAYS = 7;

    public function shouldSync(Submission $submission): bool
    {
        if (empty($submission->doi)) {
            return false;
        }
        if ($submission->citation_synced_at === null) {
            return true;
        }
        return $submission->citation_synced_at->lt(now()->subDays(self::SYNC_TTL_DAYS));
    }

    public function sync(Submission $submission): void
    {
        if (!$this->shouldSync($submission)) {
            return;
        }

        try {
            $response = Http::timeout(5)
                ->acceptJson()
                ->get("https://api.crossref.org/works/{$submission->doi}");

            if (!$response->successful()) {
                Log::warning('Crossref citation sync failed', [
                    'submission_id' => $submission->id,
                    'status' => $response->status(),
                ]);
                return;
            }

            $count = (int) data_get($response->json(), 'message.is-referenced-by-count', 0);

            $submission->update([
                'citation_count' => $count,
                'citation_synced_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Crossref citation sync exception', [
                'submission_id' => $submission->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
