<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\Membership;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateExpiredMemberships extends Command
{
    protected $signature = 'memberships:update-expired
                            {--dry-run : Run without making changes}';

    protected $description = 'Update status of expired memberships and members';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info('Updating expired memberships...');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Find active memberships that have expired
        $expiredMemberships = Membership::where('status', 'active')
            ->where('end_date', '<', now())
            ->get();

        $this->info("Found {$expiredMemberships->count()} expired memberships to update");

        $updatedMemberships = 0;
        $updatedMembers = 0;

        foreach ($expiredMemberships as $membership) {
            if ($isDryRun) {
                $this->line("  [DRY] Would update membership #{$membership->id} to expired");
            } else {
                $membership->update(['status' => 'expired']);
                $this->line("  Updated membership #{$membership->id} to expired");
            }
            $updatedMemberships++;
        }

        // Update member status for those without any active membership
        $this->newLine();
        $this->info('Checking member statuses...');

        $membersToUpdate = Member::where('status', 'active')
            ->whereDoesntHave('memberships', function ($query) {
                $query->where('status', 'active')
                    ->where('end_date', '>=', now());
            })
            ->get();

        $this->info("Found {$membersToUpdate->count()} members to update");

        foreach ($membersToUpdate as $member) {
            if ($isDryRun) {
                $this->line("  [DRY] Would update member #{$member->id} ({$member->email}) to inactive");
            } else {
                $member->update(['status' => 'inactive']);
                $this->line("  Updated member #{$member->id} ({$member->email}) to inactive");
            }
            $updatedMembers++;
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("  Memberships updated: {$updatedMemberships}");
        $this->info("  Members updated: {$updatedMembers}");

        Log::channel('daily')->info('Expired memberships update completed', [
            'memberships_updated' => $updatedMemberships,
            'members_updated' => $updatedMembers,
            'dry_run' => $isDryRun,
        ]);

        return Command::SUCCESS;
    }
}
