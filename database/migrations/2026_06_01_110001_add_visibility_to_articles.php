<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL 9.6 compatible : raw SQL.
        DB::statement("ALTER TABLE articles ADD COLUMN visibility VARCHAR(255) NOT NULL DEFAULT 'public'");
        DB::statement("ALTER TABLE articles ADD COLUMN audience_roles JSONB NULL");
        DB::statement("CREATE INDEX articles_visibility_published_at_index ON articles (visibility, published_at)");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS articles_visibility_published_at_index");
        Schema::table('articles', function ($table) {
            $table->dropColumn(['visibility', 'audience_roles']);
        });
    }
};
