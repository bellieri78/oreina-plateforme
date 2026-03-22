<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des consentements actuels
        Schema::create('consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->enum('type', [
                'newsletter',
                'email_marketing',
                'data_storage',
                'photo_usage',
                'data_sharing',
            ]);
            $table->boolean('status')->default(false); // true = consenti, false = refuse
            $table->timestamp('consent_date');
            $table->enum('method', [
                'web_form',
                'paper',
                'email',
                'phone',
                'brevo_webhook',
                'admin',
            ])->default('admin');
            $table->enum('source', [
                'brevo',
                'formulaire_inscription',
                'formulaire_contact',
                'admin',
                'import',
                'helloasso',
            ])->default('admin');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Un membre ne peut avoir qu'un consentement actif par type
            $table->unique(['member_id', 'type']);
            $table->index(['member_id']);
            $table->index(['type', 'status']);
        });

        // Historique des changements de consentement
        Schema::create('consent_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->enum('type', [
                'newsletter',
                'email_marketing',
                'data_storage',
                'photo_usage',
                'data_sharing',
            ]);
            $table->boolean('old_status')->nullable();
            $table->boolean('new_status');
            $table->enum('method', [
                'web_form',
                'paper',
                'email',
                'phone',
                'brevo_webhook',
                'admin',
            ])->default('admin');
            $table->enum('source', [
                'brevo',
                'formulaire_inscription',
                'formulaire_contact',
                'admin',
                'import',
                'helloasso',
            ])->default('admin');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['member_id']);
            $table->index(['type']);
            $table->index(['changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consent_history');
        Schema::dropIfExists('consents');
    }
};
