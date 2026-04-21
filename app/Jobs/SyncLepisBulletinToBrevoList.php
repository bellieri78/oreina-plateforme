<?php

namespace App\Jobs;

use App\Models\LepisBulletin;
use App\Models\Member;
use App\Services\BrevoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncLepisBulletinToBrevoList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 300, 900];

    public function __construct(public LepisBulletin $bulletin) {}

    public function handle(BrevoService $brevo): void
    {
        $listName = "Lepis {$this->bulletin->year} {$this->bulletin->quarter}";

        // 1. Create the Brevo list
        $listResult = $brevo->createList($listName, config('brevo.folder_id_lepis', 1));
        if (! ($listResult['success'] ?? false)) {
            throw new \RuntimeException('Brevo createList failed: ' . ($listResult['error'] ?? 'unknown'));
        }

        $listId = (int) ($listResult['data']['id'] ?? 0);
        if ($listId === 0) {
            throw new \RuntimeException('Brevo createList returned no id');
        }

        // 2. Fetch current members with a valid email
        $members = Member::query()
            ->whereHas('memberships', function ($q) {
                $q->where('status', 'active')->where('end_date', '>=', now());
            })
            ->whereNotNull('email')
            ->get();

        // 3. Import them into the new list
        $importResult = $brevo->importContacts($members, $listId);
        if (! ($importResult['success'] ?? false)) {
            throw new \RuntimeException('Brevo importContacts failed: ' . ($importResult['error'] ?? 'unknown'));
        }

        // 4. Persist sync state on the bulletin
        $this->bulletin->update([
            'brevo_list_id' => $listId,
            'brevo_list_name' => $listName,
            'brevo_synced_at' => now(),
            'brevo_sync_failed' => false,
        ]);

        Log::channel('daily')->info('Lepis bulletin synced to Brevo', [
            'bulletin_id' => $this->bulletin->id,
            'list_id' => $listId,
            'count' => $importResult['count'] ?? 0,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $this->bulletin->update(['brevo_sync_failed' => true]);

        Log::channel('daily')->error('Lepis bulletin Brevo sync failed permanently', [
            'bulletin_id' => $this->bulletin->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
