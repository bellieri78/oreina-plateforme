<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Champs configurables pour les documents officiels (attestation, reçu) :
     * signature du président, ville d'émission, pied de page.
     */
    private array $keys = [
        'association_president',
        'association_city',
        'association_address',
        'association_website',
    ];

    public function up(): void
    {
        $now = now();

        $settings = [
            ['group' => 'association', 'key' => 'association_president', 'value' => '', 'type' => 'string', 'description' => 'Nom du président(e) — signature des attestations et reçus'],
            ['group' => 'association', 'key' => 'association_city', 'value' => 'Paris', 'type' => 'string', 'description' => "Ville d'émission des documents officiels (« Fait à … »)"],
            ['group' => 'association', 'key' => 'association_address', 'value' => '', 'type' => 'string', 'description' => 'Adresse du siège social (pied de page des documents)'],
            ['group' => 'association', 'key' => 'association_website', 'value' => 'www.oreina.org', 'type' => 'string', 'description' => 'Site web (pied de page des documents)'],
        ];

        foreach ($settings as $setting) {
            $setting['created_at'] = $now;
            $setting['updated_at'] = $now;
            DB::table('settings')->insertOrIgnore($setting);
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', $this->keys)->delete();
    }
};
