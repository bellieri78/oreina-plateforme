<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('submissions')->where('status', 'desk_review')->update(['status' => 'under_initial_review']);
        DB::table('submissions')->where('status', 'in_review')->update(['status' => 'under_peer_review']);
        DB::table('submissions')->where('status', 'revision')->update(['status' => 'revision_after_review']);
    }

    public function down(): void
    {
        DB::table('submissions')->where('status', 'under_initial_review')->update(['status' => 'desk_review']);
        DB::table('submissions')->where('status', 'under_peer_review')->update(['status' => 'in_review']);
        DB::table('submissions')->where('status', 'revision_after_review')->update(['status' => 'revision']);
    }
};
