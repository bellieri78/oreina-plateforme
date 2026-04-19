<?php

namespace App\Services;

use App\Models\ArticleEvent;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ArticleMetricsService
{
    private const DEDUP_WINDOW_HOURS = 24;
    private const CACHE_TTL = 3600; // 1h

    public function recordView(Submission $submission, Request $request): void
    {
        $this->record($submission, ArticleEvent::TYPE_VIEW, $request);
    }

    public function recordPdfDownload(Submission $submission, Request $request): void
    {
        $this->record($submission, ArticleEvent::TYPE_PDF_DOWNLOAD, $request);
    }

    public function recordShare(Submission $submission, Request $request, string $network): void
    {
        if (!in_array($network, ArticleEvent::NETWORKS, true)) {
            throw new \InvalidArgumentException("Unknown share network: {$network}");
        }
        $this->record($submission, ArticleEvent::TYPE_SHARE, $request, $network);
    }

    public function getMetrics(Submission $submission): array
    {
        return Cache::remember(
            $this->cacheKey($submission->id),
            self::CACHE_TTL,
            fn () => [
                'views' => ArticleEvent::where('submission_id', $submission->id)
                    ->where('event_type', ArticleEvent::TYPE_VIEW)
                    ->count(),
                'pdf_downloads' => ArticleEvent::where('submission_id', $submission->id)
                    ->where('event_type', ArticleEvent::TYPE_PDF_DOWNLOAD)
                    ->count(),
                'shares' => ArticleEvent::where('submission_id', $submission->id)
                    ->where('event_type', ArticleEvent::TYPE_SHARE)
                    ->count(),
                'citations' => (int) $submission->citation_count,
            ]
        );
    }

    private function record(Submission $submission, string $eventType, Request $request, ?string $network = null): void
    {
        $hashedIp = $this->hashIp($request->ip() ?? '0.0.0.0');
        $cookieId = $request->cookie('oreina_visitor');

        if ($this->alreadyRecorded($submission->id, $eventType, $hashedIp, $cookieId, $network)) {
            return;
        }

        ArticleEvent::create([
            'submission_id' => $submission->id,
            'event_type' => $eventType,
            'hashed_ip' => $hashedIp,
            'cookie_id' => $cookieId,
            'network' => $network,
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'occurred_at' => now(),
        ]);

        Cache::forget($this->cacheKey($submission->id));
    }

    private function alreadyRecorded(
        int $submissionId,
        string $eventType,
        string $hashedIp,
        ?string $cookieId,
        ?string $network = null
    ): bool {
        $since = now()->subHours(self::DEDUP_WINDOW_HOURS);

        $query = ArticleEvent::where('submission_id', $submissionId)
            ->where('event_type', $eventType)
            ->where('occurred_at', '>=', $since);

        // For share events, different networks are different dedup buckets
        if ($eventType === ArticleEvent::TYPE_SHARE && $network !== null) {
            $query->where('network', $network);
        }

        if ($cookieId) {
            return (clone $query)->where(function ($q) use ($hashedIp, $cookieId) {
                $q->where('hashed_ip', $hashedIp)->orWhere('cookie_id', $cookieId);
            })->exists();
        }

        return $query->where('hashed_ip', $hashedIp)->exists();
    }

    private function hashIp(string $ip): string
    {
        return hash('sha256', $ip . config('app.key'));
    }

    private function cacheKey(int $submissionId): string
    {
        return "article_metrics:{$submissionId}";
    }
}
