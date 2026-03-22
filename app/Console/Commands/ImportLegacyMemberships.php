<?php

namespace App\Console\Commands;

use App\Services\LegacyMembershipImportService;
use Illuminate\Console\Command;

class ImportLegacyMemberships extends Command
{
    protected $signature = 'memberships:import-legacy
                            {--old-file= : Chemin vers le fichier CSV ancien (2008-2022)}
                            {--new-file= : Chemin vers le fichier CSV récent (2023-2026)}
                            {--dry-run : Simuler sans importer}';

    protected $description = 'Importe les adhésions historiques depuis les fichiers CSV';

    public function handle(): int
    {
        $oldFile = $this->option('old-file') ?? base_path('docs/fichier_adherents_oreina_old.csv');
        $newFile = $this->option('new-file') ?? base_path('docs/fichier_adherents_oreina.csv');
        $dryRun = $this->option('dry-run');

        // Validate files exist
        if (!file_exists($oldFile)) {
            $this->error("Fichier ancien introuvable: {$oldFile}");
            return self::FAILURE;
        }

        if (!file_exists($newFile)) {
            $this->error("Fichier récent introuvable: {$newFile}");
            return self::FAILURE;
        }

        $this->info('Import des adhésions historiques OREINA');
        $this->info('======================================');
        $this->newLine();
        $this->info("Fichier ancien (2008-2022): {$oldFile}");
        $this->info("Fichier récent (2023-2026): {$newFile}");
        $this->newLine();

        if ($dryRun) {
            $this->warn('MODE DRY-RUN: Aucune donnée ne sera créée');
            $this->newLine();
        }

        $service = new LegacyMembershipImportService();

        $this->info('Règles d\'import:');
        $this->line('  - Adhésion = 5€ (avant 2026)');
        $this->line('  - Reste du montant = achat magazine');
        $this->line('  - Valeur "S" = ignorée');
        $this->line('  - À partir de 2026: nouveaux types d\'adhésion');
        $this->newLine();

        $this->info('Fusion et traitement des fichiers...');

        $progressBar = $this->output->createProgressBar();
        $progressBar->start();

        try {
            $result = $service->runImport($oldFile, $newFile, $dryRun);
            $progressBar->finish();
            $this->newLine(2);

            // Display stats
            $this->info('Résultats:');
            $this->table(
                ['Métrique', 'Valeur'],
                [
                    ['Total lignes traitées', $result['stats']['rows_processed']],
                    ['Lignes ignorées', $result['stats']['rows_skipped']],
                    ['Membres créés', $result['stats']['members_created']],
                    ['Membres mis à jour', $result['stats']['members_updated']],
                    ['Adhésions créées', $result['stats']['memberships_created']],
                    ['Achats magazine créés', $result['stats']['purchases_created']],
                ]
            );

            // Display errors if any
            if (!empty($result['errors'])) {
                $this->newLine();
                $this->error('Erreurs rencontrées: ' . count($result['errors']));
                foreach (array_slice($result['errors'], 0, 10) as $error) {
                    $this->line("  - {$error['row']}: {$error['error']}");
                }
                if (count($result['errors']) > 10) {
                    $this->line('  ... et ' . (count($result['errors']) - 10) . ' autres erreurs');
                }
            }

            if ($dryRun) {
                $this->newLine();
                $this->warn('Pour exécuter l\'import réel, relancez sans --dry-run');
            } else {
                $this->newLine();
                $this->info('Import terminé avec succès !');
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $progressBar->finish();
            $this->newLine(2);
            $this->error('Erreur lors de l\'import: ' . $e->getMessage());
            $this->line($e->getTraceAsString());
            return self::FAILURE;
        }
    }
}
