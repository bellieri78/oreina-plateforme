<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Type de contact
            $table->enum('contact_type', ['individuel', 'collectivite', 'association', 'entreprise', 'autre'])
                ->default('individuel')
                ->after('id');

            // Civilite
            $table->string('civilite', 10)->nullable()->after('contact_type');

            // Telephones separes
            $table->string('telephone_fixe', 20)->nullable()->after('phone');
            $table->string('mobile', 20)->nullable()->after('telephone_fixe');

            // Coordonnees GPS
            $table->decimal('latitude', 10, 8)->nullable()->after('country');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');

            // Foyer/Famille
            $table->foreignId('foyer_titulaire_id')->nullable()->after('user_id')
                ->constrained('members')->nullOnDelete();

            // Referent (salarie responsable)
            $table->foreignId('referent_id')->nullable()->after('foyer_titulaire_id')
                ->constrained('users')->nullOnDelete();

            // Organisation parente (pour les individus lies a une structure)
            $table->foreignId('organisation_id')->nullable()->after('referent_id')
                ->constrained('members')->nullOnDelete();
            $table->string('fonction_dans_organisation')->nullable()->after('organisation_id');

            // Photo
            $table->string('photo_path')->nullable()->after('interests');

            // Anonymisation RGPD
            $table->boolean('anonymise')->default(false)->after('is_active');
            $table->timestamp('date_anonymisation')->nullable()->after('anonymise');

            // Suivi creation/modification
            $table->foreignId('created_by')->nullable()->after('joined_at')
                ->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->after('created_by')
                ->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->after('deleted_at')
                ->constrained('users')->nullOnDelete();

            // Index pour recherches
            $table->index(['contact_type']);
            $table->index(['postal_code']);
            $table->index(['city']);
            $table->index(['foyer_titulaire_id']);
            $table->index(['referent_id']);
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['foyer_titulaire_id']);
            $table->dropForeign(['referent_id']);
            $table->dropForeign(['organisation_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);

            $table->dropColumn([
                'contact_type',
                'civilite',
                'telephone_fixe',
                'mobile',
                'latitude',
                'longitude',
                'foyer_titulaire_id',
                'referent_id',
                'organisation_id',
                'fonction_dans_organisation',
                'photo_path',
                'anonymise',
                'date_anonymisation',
                'created_by',
                'updated_by',
                'deleted_by',
            ]);
        });
    }
};
