<?php

namespace App\Console\Commands;

use App\Models\LepisBulletin;
use App\Services\LepisBulletinRecipientSnapshotter;
use Illuminate\Console\Command;

class LepisBackfillRecipientsCommand extends Command
{
    protected $signature = 'lepis:backfill-recipients';

    protected $description = 'Snapshot recipients for every Lepis bulletin already in members or public state.';

    public function handle(LepisBulletinRecipientSnapshotter $snapshotter): int
    {
        $bulletins = LepisBulletin::query()
            ->whereIn('status', [LepisBulletin::STATUS_MEMBERS, LepisBulletin::STATUS_PUBLIC])
            ->orderBy('year')->orderBy('quarter')
            ->get();

        $this->info("Backfilling " . $bulletins->count() . " bulletin(s).");

        foreach ($bulletins as $bulletin) {
            $result = $snapshotter->snapshot($bulletin);
            $this->line("  - {$bulletin->title} ({$bulletin->year} {$bulletin->quarter}): paper={$result->paperCount} digital={$result->digitalCount} skipped=" . count($result->skipped));
        }

        $this->info('Done.');
        return self::SUCCESS;
    }
}
