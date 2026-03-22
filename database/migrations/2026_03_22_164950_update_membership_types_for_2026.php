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
        Schema::table('membership_types', function (Blueprint $table) {
            $table->boolean('is_legacy')->default(false)->after('is_active');
            $table->date('valid_from')->nullable()->after('is_legacy');
            $table->date('valid_until')->nullable()->after('valid_from');
            $table->boolean('for_foreign')->default(false)->after('valid_until');
            $table->boolean('for_organization')->default(false)->after('for_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_types', function (Blueprint $table) {
            $table->dropColumn([
                'is_legacy',
                'valid_from',
                'valid_until',
                'for_foreign',
                'for_organization',
            ]);
        });
    }
};
