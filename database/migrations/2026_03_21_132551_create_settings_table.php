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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general');
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        $settings = [
            // General
            ['group' => 'general', 'key' => 'site_name', 'value' => 'OREINA', 'type' => 'string', 'description' => 'Nom du site'],
            ['group' => 'general', 'key' => 'site_description', 'value' => 'Les Lepidopteres de France', 'type' => 'string', 'description' => 'Description du site'],
            ['group' => 'general', 'key' => 'contact_email', 'value' => 'contact@oreina.org', 'type' => 'string', 'description' => 'Email de contact principal'],
            ['group' => 'general', 'key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'description' => 'Mode maintenance active'],

            // Journal
            ['group' => 'journal', 'key' => 'journal_name', 'value' => 'Chersotis', 'type' => 'string', 'description' => 'Nom de la revue scientifique'],
            ['group' => 'journal', 'key' => 'journal_issn', 'value' => '', 'type' => 'string', 'description' => 'ISSN de la revue'],
            ['group' => 'journal', 'key' => 'review_deadline_days', 'value' => '30', 'type' => 'integer', 'description' => 'Delai par defaut pour les evaluations (jours)'],
            ['group' => 'journal', 'key' => 'max_reviewers_per_submission', 'value' => '3', 'type' => 'integer', 'description' => 'Nombre max de reviewers par soumission'],

            // Emails
            ['group' => 'emails', 'key' => 'email_from_name', 'value' => 'OREINA', 'type' => 'string', 'description' => 'Nom expediteur emails'],
            ['group' => 'emails', 'key' => 'email_from_address', 'value' => 'noreply@oreina.org', 'type' => 'string', 'description' => 'Adresse expediteur emails'],
            ['group' => 'emails', 'key' => 'send_review_reminders', 'value' => '1', 'type' => 'boolean', 'description' => 'Envoyer rappels automatiques aux reviewers'],
            ['group' => 'emails', 'key' => 'reminder_days_before', 'value' => '7', 'type' => 'integer', 'description' => 'Jours avant echeance pour rappel'],

            // Memberships
            ['group' => 'memberships', 'key' => 'membership_year_start_month', 'value' => '1', 'type' => 'integer', 'description' => 'Mois de debut de l\'annee d\'adhesion'],
            ['group' => 'memberships', 'key' => 'auto_renew_reminder_days', 'value' => '30', 'type' => 'integer', 'description' => 'Jours avant expiration pour rappel renouvellement'],
        ];

        foreach ($settings as $setting) {
            $setting['created_at'] = now();
            $setting['updated_at'] = now();
            \DB::table('settings')->insert($setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
