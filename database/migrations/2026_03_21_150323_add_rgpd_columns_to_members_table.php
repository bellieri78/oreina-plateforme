<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Consentements RGPD
            $table->boolean('consent_communication')->default(false)->after('newsletter_subscribed');
            $table->boolean('consent_image')->default(false)->after('consent_communication');

            // Suivi RGPD
            $table->timestamp('rgpd_reviewed_at')->nullable()->after('date_anonymisation');
            $table->text('rgpd_review_notes')->nullable()->after('rgpd_reviewed_at');
            $table->timestamp('last_interaction_at')->nullable()->after('rgpd_review_notes');
        });

        // Table historique des consentements
        Schema::create('rgpd_consent_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->string('consent_type'); // newsletter, communication, image
            $table->boolean('value');
            $table->string('source')->nullable(); // manual, import, form, api
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index(['member_id', 'consent_type']);
        });

        // Table des revues RGPD
        Schema::create('rgpd_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->string('alert_type'); // no_interaction, not_updated, expired_membership, inactive_donor
            $table->string('action'); // keep, update, contact, anonymize
            $table->text('notes')->nullable();
            $table->date('next_review_date')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index(['member_id', 'created_at']);
        });

        // Table parametres RGPD
        Schema::create('rgpd_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->integer('value');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Inserer les parametres par defaut
        DB::table('rgpd_settings')->insert([
            ['key' => 'retention_no_interaction', 'value' => 36, 'description' => 'Mois sans interaction', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'retention_not_updated', 'value' => 60, 'description' => 'Mois sans mise a jour', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'retention_expired_membership', 'value' => 24, 'description' => 'Mois apres expiration adhesion', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'retention_inactive_donor', 'value' => 48, 'description' => 'Mois sans don', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('rgpd_settings');
        Schema::dropIfExists('rgpd_reviews');
        Schema::dropIfExists('rgpd_consent_history');

        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'consent_communication',
                'consent_image',
                'rgpd_reviewed_at',
                'rgpd_review_notes',
                'last_interaction_at',
            ]);
        });
    }
};
