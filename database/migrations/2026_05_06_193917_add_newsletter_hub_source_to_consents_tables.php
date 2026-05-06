<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ajoute 'newsletter_hub' aux colonnes source des tables consents et consent_history.
     * Compatible PostgreSQL 9.6+ (pas de ->change(), on modifie la contrainte CHECK manuellement).
     */
    public function up(): void
    {
        // consents.source
        DB::statement("ALTER TABLE consents DROP CONSTRAINT IF EXISTS consents_source_check");
        DB::statement("ALTER TABLE consents ADD CONSTRAINT consents_source_check CHECK (source IN ('brevo','formulaire_inscription','formulaire_contact','newsletter_hub','admin','import','helloasso'))");

        // consent_history.source
        DB::statement("ALTER TABLE consent_history DROP CONSTRAINT IF EXISTS consent_history_source_check");
        DB::statement("ALTER TABLE consent_history ADD CONSTRAINT consent_history_source_check CHECK (source IN ('brevo','formulaire_inscription','formulaire_contact','newsletter_hub','admin','import','helloasso'))");
    }

    public function down(): void
    {
        // Rétablir les contraintes sans newsletter_hub
        DB::statement("ALTER TABLE consents DROP CONSTRAINT IF EXISTS consents_source_check");
        DB::statement("ALTER TABLE consents ADD CONSTRAINT consents_source_check CHECK (source IN ('brevo','formulaire_inscription','formulaire_contact','admin','import','helloasso'))");

        DB::statement("ALTER TABLE consent_history DROP CONSTRAINT IF EXISTS consent_history_source_check");
        DB::statement("ALTER TABLE consent_history ADD CONSTRAINT consent_history_source_check CHECK (source IN ('brevo','formulaire_inscription','formulaire_contact','admin','import','helloasso'))");
    }
};
