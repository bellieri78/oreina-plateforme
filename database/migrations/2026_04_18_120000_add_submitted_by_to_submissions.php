<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->foreignId('submitted_by_user_id')
                  ->nullable()
                  ->after('author_id')
                  ->constrained('users')
                  ->onDelete('set null');
            $table->index('submitted_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['submitted_by_user_id']);
            $table->dropIndex(['submitted_by_user_id']);
            $table->dropColumn('submitted_by_user_id');
        });
    }
};
