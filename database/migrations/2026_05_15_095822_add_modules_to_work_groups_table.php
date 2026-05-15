<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('work_groups', function (Blueprint $table) {
            $table->boolean('has_resources')->default(true);
            $table->boolean('has_collaborative_space')->default(false);
            $table->string('collaborative_space_url')->nullable();
            $table->boolean('has_forum')->default(false);
            $table->string('join_policy')->default('open');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_groups', function (Blueprint $table) {
            $table->dropColumn(['has_resources', 'has_collaborative_space', 'collaborative_space_url', 'has_forum', 'join_policy']);
        });
    }
};
