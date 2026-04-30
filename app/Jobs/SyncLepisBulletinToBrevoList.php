<?php

namespace App\Jobs;

use App\Models\LepisBulletin;
use App\Models\LepisBulletinRecipient;
use App\Services\BrevoService;
use App\Services\LepisBulletinRecipientSnapshotter;
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
        // 1. Snapshot all recipients (paper + digital) for this bulletin first.
        $snapshotter = app(LepisBulletinRecipientSnapshotter::class);
        $snapshotResult = $snapshotter->snapshot($this->bulletin);

        $listName = "Lepis {$this->bulletin->year} {$this->bulletin->quarter}";

        // 2. Create the Brevo list
        $listResult = $brevo->createList($listName, config('brevo.folder_id_lepis', 1));
        if (! ($listResult['success'] ?? false)) {
            throw new \RuntimeException('Brevo createList failed: ' . ($listResult['error'] ?? 'unknown'));
        }

        $listId = (int) ($listResult['data']['id'] ?? 0);
        if ($listId === 0) {
            throw new \RuntimeException('Brevo createList returned no id');
        }

        // 3. Fetch only DIGITAL recipients of this bulletin (from the snapshot table).
        $digitalRecipients = $this->bulletin->recipients()
            ->where('format', LepisBulletinRecipient::FORMAT_DIGITAL)
            ->with('member')
            ->get();
        $members = $digitalRecipients->pluck('member')->filter();

        // 4. Import them into the new list
        $importResult = $brevo->importContacts($members, $listId);
        if (! ($importResult['success'] ?? false)) {
            throw new \RuntimeException('Brevo importContacts failed: ' . ($importResult['error'] ?? 'unknown'));
        }

        // 5. Persist sync state on the bulletin
        $this->bulletin->update([
            'brevo_list_id' => $listId,
            'brevo_list_name' => $listName,
            'brevo_synced_at' => now(),
            'brevo_sync_failed' => false,
        ]);

        // 6. Tag digital recipients with the Brevo list id for traceability.
        $this->bulletin->recipients()
            ->where('format', LepisBulletinRecipient::FORMAT_DIGITAL)
            ->update(['brevo_list_id' => $listId]);

        Log::channel('daily')->info('Lepis bulletin synced to Brevo', [
            'bulletin_id' => $this->bulletin->id,
            'list_id' => $listId,
            'count' => $importResult['count'] ?? 0,
            'paper_snapshotted' => $snapshotResult->paperCount,
            'digital_snapshotted' => $snapshotResult->digitalCount,
            'skipped' => count($snapshotResult->skipped),
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
