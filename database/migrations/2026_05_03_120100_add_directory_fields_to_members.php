<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // PostgreSQL 9.6 compatible : raw SQL, pas de ->change().
        DB::statement("ALTER TABLE members ADD COLUMN directory_opt_in BOOLEAN NOT NULL DEFAULT FALSE");
        DB::statement("ALTER TABLE members ADD COLUMN directory_phone_visible BOOLEAN NOT NULL DEFAULT FALSE");
        DB::statement("ALTER TABLE members ADD COLUMN directory_groups JSONB NULL");
        DB::statement("ALTER TABLE members ADD COLUMN directory_opt_in_at TIMESTAMP NULL");
        DB::statement("ALTER TABLE members ADD COLUMN directory_opt_in_source VARCHAR(50) NULL");

        DB::statement("CREATE INDEX members_directory_opt_in_idx ON members (directory_opt_in) WHERE directory_opt_in = TRUE");
        DB::statement("CREATE INDEX members_directory_groups_gin_idx ON members USING GIN (directory_groups)");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS members_directory_groups_gin_idx");
        DB::statement("DROP INDEX IF EXISTS members_directory_opt_in_idx");
        Schema::table('members', function ($table) {
            $table->dropColumn([
                'directory_opt_in',
                'directory_phone_visible',
                'directory_groups',
                'directory_opt_in_at',
                'directory_opt_in_source',
            ]);
        });
    }
};
