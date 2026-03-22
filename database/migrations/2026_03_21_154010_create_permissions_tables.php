<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Table des permissions disponibles
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('module'); // members, memberships, donations, articles, etc.
            $table->string('action'); // view, create, edit, delete, export, import
            $table->string('name'); // Nom lisible
            $table->string('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['module', 'action']);
            $table->index('module');
        });

        // Table pivot user_permissions
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'permission_id']);
        });

        // Inserer les permissions par defaut
        $this->seedPermissions();
    }

    private function seedPermissions(): void
    {
        $modules = [
            'members' => [
                'label' => 'Contacts',
                'actions' => [
                    'view' => 'Voir les contacts',
                    'create' => 'Creer des contacts',
                    'edit' => 'Modifier les contacts',
                    'delete' => 'Supprimer les contacts',
                    'export' => 'Exporter les contacts',
                    'import' => 'Importer des contacts',
                ],
            ],
            'memberships' => [
                'label' => 'Adhesions',
                'actions' => [
                    'view' => 'Voir les adhesions',
                    'create' => 'Creer des adhesions',
                    'edit' => 'Modifier les adhesions',
                    'delete' => 'Supprimer les adhesions',
                    'export' => 'Exporter les adhesions',
                ],
            ],
            'donations' => [
                'label' => 'Dons',
                'actions' => [
                    'view' => 'Voir les dons',
                    'create' => 'Creer des dons',
                    'edit' => 'Modifier les dons',
                    'delete' => 'Supprimer les dons',
                    'export' => 'Exporter les dons',
                    'receipt' => 'Generer les recus fiscaux',
                ],
            ],
            'articles' => [
                'label' => 'Articles',
                'actions' => [
                    'view' => 'Voir les articles',
                    'create' => 'Creer des articles',
                    'edit' => 'Modifier les articles',
                    'delete' => 'Supprimer les articles',
                    'publish' => 'Publier les articles',
                ],
            ],
            'events' => [
                'label' => 'Evenements',
                'actions' => [
                    'view' => 'Voir les evenements',
                    'create' => 'Creer des evenements',
                    'edit' => 'Modifier les evenements',
                    'delete' => 'Supprimer les evenements',
                    'publish' => 'Publier les evenements',
                ],
            ],
            'journal' => [
                'label' => 'Revue scientifique',
                'actions' => [
                    'view' => 'Voir les numeros',
                    'create' => 'Creer des numeros',
                    'edit' => 'Modifier les numeros',
                    'delete' => 'Supprimer les numeros',
                    'publish' => 'Publier les numeros',
                ],
            ],
            'submissions' => [
                'label' => 'Soumissions',
                'actions' => [
                    'view' => 'Voir les soumissions',
                    'create' => 'Creer des soumissions',
                    'edit' => 'Modifier les soumissions',
                    'delete' => 'Supprimer les soumissions',
                    'assign' => 'Assigner des reviewers',
                    'decide' => 'Decider du statut',
                ],
            ],
            'reviews' => [
                'label' => 'Reviews',
                'actions' => [
                    'view' => 'Voir les reviews',
                    'create' => 'Creer des reviews',
                    'edit' => 'Modifier les reviews',
                    'delete' => 'Supprimer les reviews',
                ],
            ],
            'users' => [
                'label' => 'Utilisateurs',
                'actions' => [
                    'view' => 'Voir les utilisateurs',
                    'create' => 'Creer des utilisateurs',
                    'edit' => 'Modifier les utilisateurs',
                    'delete' => 'Supprimer les utilisateurs',
                    'permissions' => 'Gerer les permissions',
                ],
            ],
            'settings' => [
                'label' => 'Parametres',
                'actions' => [
                    'view' => 'Voir les parametres',
                    'edit' => 'Modifier les parametres',
                    'statistics' => 'Voir les statistiques',
                ],
            ],
            'rgpd' => [
                'label' => 'RGPD',
                'actions' => [
                    'view' => 'Voir le dashboard RGPD',
                    'process' => 'Traiter les alertes',
                    'anonymize' => 'Anonymiser les contacts',
                    'settings' => 'Modifier les parametres RGPD',
                ],
            ],
            'map' => [
                'label' => 'Carte',
                'actions' => [
                    'view' => 'Voir la carte',
                    'geocode' => 'Geocoder les contacts',
                    'export' => 'Exporter par rayon',
                ],
            ],
        ];

        $sortOrder = 0;
        $now = now();

        foreach ($modules as $module => $config) {
            foreach ($config['actions'] as $action => $description) {
                DB::table('permissions')->insert([
                    'module' => $module,
                    'action' => $action,
                    'name' => $config['label'] . ' - ' . $description,
                    'description' => $description,
                    'sort_order' => $sortOrder++,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('permissions');
    }
};
