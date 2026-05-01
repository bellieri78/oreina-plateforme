<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Raw SQL for PostgreSQL 9.6 compatibility (no ->change() on Laravel 12).
        DB::statement("ALTER TABLE memberships ADD COLUMN lepis_format VARCHAR(10) NULL");
        DB::statement("ALTER TABLE memberships ADD CONSTRAINT memberships_lepis_format_check CHECK (lepis_format IS NULL OR lepis_format IN ('paper', 'digital'))");

        // Backfill: legacy memberships default to 'paper' (no surprise for current paper subscribers).
        DB::statement("UPDATE memberships SET lepis_format = 'paper' WHERE lepis_format IS NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE memberships DROP CONSTRAINT IF EXISTS memberships_lepis_format_check");
        Schema::table('memberships', function ($table) {
            $table->dropColumn('lepis_format');
        });
    }
};
