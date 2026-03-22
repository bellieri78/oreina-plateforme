<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partnership_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6B7280'); // Couleur hex pour badges
            $table->string('icon')->nullable(); // Heroicon name
            $table->boolean('is_auto_calculated')->default(false); // ADHERENT, ANCIEN_ADHERENT
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Types de partenariat par defaut
        DB::table('partnership_types')->insert([
            // Types auto-calcules (ne peuvent pas etre assignes manuellement)
            [
                'code' => 'ADHERENT',
                'name' => 'Adherent',
                'description' => 'Adhesion payee pour l\'annee en cours',
                'color' => '#10B981',
                'icon' => 'heroicon-o-check-badge',
                'is_auto_calculated' => true,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ANCIEN_ADHERENT',
                'name' => 'Ancien adherent',
                'description' => 'Adhesion passee mais pas pour l\'annee en cours',
                'color' => '#F59E0B',
                'icon' => 'heroicon-o-clock',
                'is_auto_calculated' => true,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Types manuels
            [
                'code' => 'BENEVOLE',
                'name' => 'Benevole',
                'description' => 'Benevole regulier',
                'color' => '#8B5CF6',
                'icon' => 'heroicon-o-hand-raised',
                'is_auto_calculated' => false,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'BENEVOLE_PONCTUEL',
                'name' => 'Benevole ponctuel',
                'description' => 'Participation occasionnelle',
                'color' => '#A78BFA',
                'icon' => 'heroicon-o-hand-raised',
                'is_auto_calculated' => false,
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DONATEUR',
                'name' => 'Donateur',
                'description' => 'A fait un don',
                'color' => '#EC4899',
                'icon' => 'heroicon-o-heart',
                'is_auto_calculated' => false,
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'PARTENAIRE',
                'name' => 'Partenaire',
                'description' => 'Partenaire institutionnel',
                'color' => '#3B82F6',
                'icon' => 'heroicon-o-building-office',
                'is_auto_calculated' => false,
                'is_active' => true,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'FOURNISSEUR',
                'name' => 'Fournisseur',
                'description' => 'Fournisseur de services ou produits',
                'color' => '#6B7280',
                'icon' => 'heroicon-o-truck',
                'is_auto_calculated' => false,
                'is_active' => true,
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ELU',
                'name' => 'Elu',
                'description' => 'Elu local',
                'color' => '#EF4444',
                'icon' => 'heroicon-o-star',
                'is_auto_calculated' => false,
                'is_active' => true,
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('partnership_types');
    }
};
