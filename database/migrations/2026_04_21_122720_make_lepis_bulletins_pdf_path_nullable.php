<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // SQL brut pour rester compatible PostgreSQL 9.6 (la syntaxe
        // générée par ->change() embarque DROP IDENTITY IF EXISTS qui
        // n'existe qu'à partir de PG 10).
        DB::statement('ALTER TABLE lepis_bulletins ALTER COLUMN pdf_path DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE lepis_bulletins ALTER COLUMN pdf_path SET NOT NULL');
    }
};
