<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\Membership;
use App\Notifications\MembershipExpiringNotification;
use App\Notifications\MembershipExpiredNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendMembershipReminders extends Command
{
    protected $signature = 'memberships:send-reminders
                            {--dry-run : Run without sending emails}';

    protected $description = 'Send membership expiration reminders (J-30, J-7, J+15)';

    protected array $reminderDays = [
        30 => 'reminder_30_sent',
        7 => 'reminder_7_sent',
        -15 => 'reminder_expired_sent', // Negative = days after expiration
    ];

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $totalSent = 0;

        $this->info('Starting membership reminder process...');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No emails will be sent');
        }

        // J-30: Expiring in 30 days
        $totalSent += $this->sendExpiringReminders(30, $isDryRun);

        // J-7: Expiring in 7 days
        $totalSent += $this->sendExpiringReminders(7, $isDryRun);

        // J+15: Expired 15 days ago
        $totalSent += $this->sendExpiredReminders(15, $isDryRun);

        $this->newLine();
        $this->info("Total reminders " . ($isDryRun ? 'to send' : 'sent') . ": {$totalSent}");

        Log::channel('daily')->info('Membership reminders processed', [
            'total_sent' => $totalSent,
            'dry_run' => $isDryRun,
        ]);

        return Command::SUCCESS;
    }

    protected function sendExpiringReminders(int $days, bool $isDryRun): int
    {
        $this->newLine();
        $this->info("Processing J-{$days} reminders...");

        $targetDate = now()->addDays($days)->toDateString();

        // Find memberships expiring on the target date that haven't received this reminder
        $memberships = Membership::with('member')
            ->where('status', 'active')
            ->whereDate('end_date', $targetDate)
            ->where(function ($query) use ($days) {
                if ($days === 30) {
                    $query->where('renewal_reminder_sent', false)
                        ->orWhereNull('renewal_reminder_sent');
                } else {
                    // For 7-day reminder, check that we haven't sent it recently
                    $query->where(function ($q) {
                        $q->whereNull('renewal_reminder_sent_at')
                            ->orWhere('renewal_reminder_sent_at', '<', now()->subDays(20));
                    });
                }
            })
            ->get();

        $count = 0;

        foreach ($memberships as $membership) {
            $member = $membership->member;

            if (!$member || !$member->email) {
                $this->warn("  Skipping membership #{$membership->id}: no email");
                continue;
            }

            if ($isDryRun) {
                $this->line("  [DRY] Would send J-{$days} reminder to {$member->email}");
            } else {
                try {
                    $member->notify(new MembershipExpiringNotification($membership, $days));

                    $membership->update([
                        'renewal_reminder_sent' => true,
                        'renewal_reminder_sent_at' => now(),
                    ]);

                    $this->line("  Sent J-{$days} reminder to {$member->email}");
                } catch (\Exception $e) {
                    $this->error("  Failed to send to {$member->email}: {$e->getMessage()}");
                    Log::error('Failed to send membership reminder', [
                        'membership_id' => $membership->id,
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }
            }

            $count++;
        }

        $this->info("  Found {$memberships->count()} memberships, processed {$count}");

        return $count;
    }

    protected function sendExpiredReminders(int $daysSinceExpiration, bool $isDryRun): int
    {
        $this->newLine();
        $this->info("Processing J+{$daysSinceExpiration} reminders (expired)...");

        $targetDate = now()->subDays($daysSinceExpiration)->toDateString();

        // Find memberships that expired on the target date
        $memberships = Membership::with('member')
            ->whereIn('status', ['active', 'expired'])
            ->whereDate('end_date', $targetDate)
            ->get();

        $count = 0;

        foreach ($memberships as $membership) {
            $member = $membership->member;

            if (!$member || !$member->email) {
                $this->warn("  Skipping membership #{$membership->id}: no email");
                continue;
            }

            // Check if member has renewed since
            $hasRenewed = Membership::where('member_id', $member->id)
                ->where('start_date', '>', $membership->end_date)
                ->exists();

            if ($hasRenewed) {
                $this->line("  Skipping {$member->email}: already renewed");
                continue;
            }

            if ($isDryRun) {
                $this->line("  [DRY] Would send expired reminder to {$member->email}");
            } else {
                try {
                    $member->notify(new MembershipExpiredNotification($membership, $daysSinceExpiration));
                    $this->line("  Sent expired reminder to {$member->email}");
                } catch (\Exception $e) {
                    $this->error("  Failed to send to {$member->email}: {$e->getMessage()}");
                    Log::error('Failed to send expired membership reminder', [
                        'membership_id' => $membership->id,
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }
            }

            $count++;
        }

        $this->info("  Found {$memberships->count()} memberships, processed {$count}");

        return $count;
    }
}
