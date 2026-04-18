<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->timestamp('author_approved_at')->nullable()->after('published_at');
            $table->timestamp('author_approval_requested_at')->nullable()->after('author_approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['author_approved_at', 'author_approval_requested_at']);
        });
    }
};
