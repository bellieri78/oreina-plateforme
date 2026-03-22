<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('status')->default('active')->after('is_active');
            $table->timestamp('membership_expires_at')->nullable()->after('status');
        });

        // Migrate existing is_active data to status
        DB::table('members')
            ->where('is_active', false)
            ->update(['status' => 'inactive']);
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['status', 'membership_expires_at']);
        });
    }
};
