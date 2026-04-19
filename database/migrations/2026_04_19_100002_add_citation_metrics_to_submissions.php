<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->unsignedInteger('citation_count')->default(0)->after('doi');
            $table->timestamp('citation_synced_at')->nullable()->after('citation_count');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['citation_count', 'citation_synced_at']);
        });
    }
};
