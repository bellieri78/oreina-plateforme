<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $t) {
            $t->foreignId('layout_editor_id')->nullable()->after('editor_id')
              ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $t) {
            $t->dropForeign(['layout_editor_id']);
            $t->dropColumn('layout_editor_id');
        });
    }
};
