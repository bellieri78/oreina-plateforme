<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->timestamp('lepis_decision_at')->nullable()->after('redirected_to_lepis');
            $table->foreignId('lepis_decided_by_user_id')->nullable()
                  ->after('lepis_decision_at')
                  ->constrained('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['lepis_decided_by_user_id']);
            $table->dropColumn(['lepis_decision_at', 'lepis_decided_by_user_id']);
        });
    }
};
