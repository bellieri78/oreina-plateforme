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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained()->onDelete('set null');
            $table->string('donor_email');
            $table->string('donor_name');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable();  // helloasso, virement, cheque
            $table->string('payment_reference')->nullable();
            $table->string('campaign')->nullable();        // Campagne de don spécifique
            $table->date('donation_date');
            $table->boolean('tax_receipt_sent')->default(false);
            $table->string('tax_receipt_number')->nullable();
            $table->timestamp('tax_receipt_sent_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('donation_date');
            $table->index('campaign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
