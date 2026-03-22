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
        Schema::table('donations', function (Blueprint $table) {
            $table->string('donor_address')->nullable()->after('donor_name');
            $table->string('donor_postal_code')->nullable()->after('donor_address');
            $table->string('donor_city')->nullable()->after('donor_postal_code');
            $table->string('tax_receipt_file')->nullable()->after('tax_receipt_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['donor_address', 'donor_postal_code', 'donor_city', 'tax_receipt_file']);
        });
    }
};
