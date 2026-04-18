<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('submissions')
            ->where('status', 'draft')
            ->update(['status' => 'submitted', 'submitted_at' => DB::raw('COALESCE(submitted_at, NOW())')]);
    }

    public function down(): void
    {
        // Pas de rollback : le statut draft n'existe plus dans l'enum.
    }
};
