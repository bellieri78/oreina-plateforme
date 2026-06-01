<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL 9.6 compatible : raw SQL, pas de Schema builder positionnel.
        DB::statement("ALTER TABLE events ADD COLUMN visibility VARCHAR(255) NOT NULL DEFAULT 'public'");
        DB::statement("ALTER TABLE events ADD COLUMN audience_roles JSONB NULL");
        DB::statement("ALTER TABLE events ADD COLUMN work_group_id BIGINT NULL");
        DB::statement("ALTER TABLE events ADD COLUMN meeting_url VARCHAR(255) NULL");
        DB::statement("ALTER TABLE events ADD CONSTRAINT events_work_group_id_foreign FOREIGN KEY (work_group_id) REFERENCES work_groups(id) ON DELETE SET NULL");
        DB::statement("CREATE INDEX events_visibility_start_date_index ON events (visibility, start_date)");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS events_visibility_start_date_index");
        DB::statement("ALTER TABLE events DROP CONSTRAINT IF EXISTS events_work_group_id_foreign");
        Schema::table('events', function ($table) {
            $table->dropColumn(['visibility', 'audience_roles', 'work_group_id', 'meeting_url']);
        });
    }
};
