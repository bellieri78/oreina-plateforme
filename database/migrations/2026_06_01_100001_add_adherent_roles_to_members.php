<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL 9.6 compatible : raw SQL, pas de Schema builder positionnel.
        DB::statement("ALTER TABLE members ADD COLUMN adherent_roles JSONB NULL");
    }

    public function down(): void
    {
        Schema::table('members', function ($table) {
            $table->dropColumn('adherent_roles');
        });
    }
};
