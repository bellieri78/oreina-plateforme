<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Backfill: copier les rares phone-only vers mobile (4 cas en prod selon vérification)
        DB::statement("UPDATE members SET mobile = phone WHERE phone IS NOT NULL AND mobile IS NULL");

        // Drop column (PG 9.6 compat — DB::statement brut)
        DB::statement("ALTER TABLE members DROP COLUMN phone");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE members ADD COLUMN phone VARCHAR(255) NULL");
        // Note: down() ne re-renseigne pas les valeurs (perte irréversible côté rollback).
        // Acceptable car mobile contient toute l'info utile.
    }
};
