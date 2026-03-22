<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Services\BrevoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncContactsToBrevo extends Command
{
    protected $signature = 'brevo:sync
                            {--list= : Specific list to sync (members, newsletter, donors, all)}
                            {--dry-run : Run without making API calls}
                            {--force : Force sync even if Brevo is not configured}';

    protected $description = 'Synchronize contacts to Brevo email marketing platform';

    protected BrevoService $brevo;

    public function __construct(BrevoService $brevo)
    {
        parent::__construct();
        $this->brevo = $brevo;
    }

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $listOption = $this->option('list');

        $this->info('Starting Brevo synchronization...');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No API calls will be made');
        }

        if (!$this->brevo->isConfigured() && !$this->option('force')) {
            $this->error('Brevo is not configured. Set BREVO_API_KEY in .env');
            return Command::FAILURE;
        }

        $results = [
            'synced' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        // Determine which lists to sync
        $listsToSync = $this->getListsToSync($listOption);

        foreach ($listsToSync as $listName => $listId) {
            $this->newLine();
            $this->info("Syncing list: {$listName}");

            $members = $this->getMembersForList($listName);
            $this->info("  Found {$members->count()} contacts");

            if ($members->isEmpty()) {
                $this->line('  No contacts to sync');
                continue;
            }

            // Process in batches
            $batchSize = config('brevo.sync.batch_size', 100);
            $batches = $members->chunk($batchSize);

            $bar = $this->output->createProgressBar($batches->count());
            $bar->start();

            foreach ($batches as $batch) {
                if ($isDryRun) {
                    $results['synced'] += $batch->count();
                } else {
                    $result = $this->brevo->importContacts($batch, $listId);

                    if ($result['success']) {
                        $results['synced'] += $batch->count();
                    } else {
                        $results['failed'] += $batch->count();
                        $this->newLine();
                        $this->error("  Batch failed: {$result['error']}");
                    }
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
        }

        // Summary
        $this->newLine();
        $this->info('Synchronization complete!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Synced', $results['synced']],
                ['Failed', $results['failed']],
                ['Skipped', $results['skipped']],
            ]
        );

        Log::channel('daily')->info('Brevo sync completed', [
            'results' => $results,
            'dry_run' => $isDryRun,
        ]);

        return Command::SUCCESS;
    }

    protected function getListsToSync(?string $listOption): array
    {
        $configuredLists = config('brevo.lists', []);
        $lists = [];

        if ($listOption === 'all' || !$listOption) {
            // Sync all configured lists
            foreach ($configuredLists as $name => $id) {
                if ($id) {
                    $lists[$name] = (int) $id;
                }
            }
        } else {
            // Sync specific list
            if (isset($configuredLists[$listOption]) && $configuredLists[$listOption]) {
                $lists[$listOption] = (int) $configuredLists[$listOption];
            } else {
                $this->warn("List '{$listOption}' not configured in config/brevo.php");
            }
        }

        if (empty($lists)) {
            $this->warn('No lists configured. Syncing without list assignment.');
            $lists['all_contacts'] = null;
        }

        return $lists;
    }

    protected function getMembersForList(string $listName): \Illuminate\Support\Collection
    {
        $query = Member::query();

        // Filter based on list type
        switch ($listName) {
            case 'members':
                // Active members only
                $query->whereHas('memberships', function ($q) {
                    $q->where('status', 'active')
                        ->where('end_date', '>=', now());
                });
                break;

            case 'newsletter':
                // Newsletter subscribers
                $query->where('newsletter_subscribed', true);
                break;

            case 'donors':
                // Members who have made donations
                $query->whereHas('donations');
                break;

            case 'all_contacts':
            default:
                // All contacts (optionally filter inactive)
                if (!config('brevo.sync.include_inactive', false)) {
                    $query->where(function ($q) {
                        $q->where('is_active', true)
                            ->orWhere('status', 'active');
                    });
                }
                break;
        }

        // Only contacts with valid email
        $query->whereNotNull('email')
            ->where('email', '!=', '');

        return $query->get();
    }
}
