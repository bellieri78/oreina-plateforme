<?php

namespace App\Jobs;

use App\Models\LepisBulletin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncLepisBulletinToBrevoList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public LepisBulletin $bulletin) {}

    public function handle(): void
    {
        // Real implementation lands in Task 6.
    }
}
