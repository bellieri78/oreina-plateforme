<?php

namespace App\Console\Commands;

use App\Mail\ReviewReminder;
use App\Models\Review;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReviewReminders extends Command
{
    protected $signature = 'reviews:send-reminders';
    protected $description = 'Envoie des relances aux relecteurs en retard';

    public function handle(): int
    {
        $invitationCount = $this->remindPendingInvitations();
        $overdueCount = $this->remindOverdueReviews();

        $this->info("Relances invitation : {$invitationCount}");
        $this->info("Relances relecture : {$overdueCount}");

        return self::SUCCESS;
    }

    private function remindPendingInvitations(): int
    {
        $reviews = Review::where('status', Review::STATUS_INVITED)
            ->where('invited_at', '<', now()->subDays(7))
            ->whereNull('responded_at')
            ->where(fn($q) => $q->whereNull('last_reminder_at')
                ->orWhere('last_reminder_at', '<', now()->subDays(5)))
            ->with(['reviewer', 'submission'])
            ->get();

        foreach ($reviews as $review) {
            if ($review->reviewer) {
                Mail::to($review->reviewer)->queue(new ReviewReminder($review));
                $review->update(['last_reminder_at' => now()]);
            }
        }

        return $reviews->count();
    }

    private function remindOverdueReviews(): int
    {
        $reviews = Review::where('status', Review::STATUS_ACCEPTED)
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->whereNull('completed_at')
            ->where(fn($q) => $q->whereNull('last_reminder_at')
                ->orWhere('last_reminder_at', '<', now()->subDays(5)))
            ->with(['reviewer', 'submission'])
            ->get();

        foreach ($reviews as $review) {
            if ($review->reviewer) {
                Mail::to($review->reviewer)->queue(new ReviewReminder($review));
                $review->update(['last_reminder_at' => now()]);
            }
        }

        return $reviews->count();
    }
}
