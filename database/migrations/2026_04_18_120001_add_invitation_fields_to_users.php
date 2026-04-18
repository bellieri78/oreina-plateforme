<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('invited_at')->nullable()->after('remember_token');
            $table->timestamp('claimed_at')->nullable()->after('invited_at');
            $table->foreignId('invited_by_user_id')->nullable()
                  ->after('claimed_at')
                  ->constrained('users')
                  ->onDelete('set null');
            $table->index('invited_at');
            $table->index('claimed_at');
        });

        // Le password peut être null pour les users ghost : on rend la colonne nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['invited_by_user_id']);
            $table->dropIndex(['invited_at']);
            $table->dropIndex(['claimed_at']);
            $table->dropColumn(['invited_at', 'claimed_at', 'invited_by_user_id']);
        });
    }
};
