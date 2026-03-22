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
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('membership_type_id')->constrained()->onDelete('restrict');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('amount_paid', 8, 2);
            $table->string('payment_method')->nullable();  // helloasso, virement, cheque, especes
            $table->string('payment_reference')->nullable(); // Référence HelloAsso ou autre
            $table->string('status')->default('active');   // active, expired, cancelled
            $table->boolean('renewal_reminder_sent')->default(false);
            $table->timestamp('renewal_reminder_sent_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
