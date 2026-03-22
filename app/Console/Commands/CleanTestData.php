<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\Membership;
use App\Models\Donation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanTestData extends Command
{
    protected $signature = 'members:clean-test-data
                            {--dry-run : Simulate without deleting}
                            {--force : Skip confirmation}';

    protected $description = 'Supprime les membres de test (emails @example.com et données de seeders)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        // Find test members
        $testMembers = Member::where(function ($query) {
            $query->where('email', 'like', '%@example.com')
                ->orWhere('email', 'like', '%@example.org')
                ->orWhere('email', 'like', '%@test.com')
                ->orWhere('email', 'like', '%@fake.com');
        })->get();

        $this->info("Membres de test trouvés: {$testMembers->count()}");

        if ($testMembers->isEmpty()) {
            $this->info('Aucun membre de test à supprimer.');
            return self::SUCCESS;
        }

        // Show details
        $this->table(
            ['ID', 'Nom', 'Email', 'Adhésions', 'Dons'],
            $testMembers->map(fn ($m) => [
                $m->id,
                $m->last_name . ' ' . $m->first_name,
                $m->email,
                $m->memberships()->count(),
                $m->donations()->count(),
            ])->toArray()
        );

        if ($dryRun) {
            $this->warn('Mode dry-run: aucune suppression effectuée.');
            return self::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm('Voulez-vous supprimer ces membres de test et leurs données associées ?')) {
            $this->info('Opération annulée.');
            return self::SUCCESS;
        }

        DB::beginTransaction();
        try {
            $memberIds = $testMembers->pluck('id');

            // Delete related data
            $deletedMemberships = Membership::whereIn('member_id', $memberIds)->delete();
            $deletedDonations = Donation::whereIn('member_id', $memberIds)->delete();

            // Delete members (soft delete)
            $deletedMembers = Member::whereIn('id', $memberIds)->delete();

            DB::commit();

            $this->info("Supprimé: {$deletedMembers} membres, {$deletedMemberships} adhésions, {$deletedDonations} dons");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Erreur: " . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
