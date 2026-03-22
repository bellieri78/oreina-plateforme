<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Types d'activites de benevolat
        Schema::create('volunteer_activity_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: "Animation", "Stand", "Comptage"
            $table->string('code')->unique(); // Ex: ANIM, STND, CMPT
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#2C5F2D'); // Hex color
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Activites de benevolat
        Schema::create('volunteer_activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('activity_type_id')->constrained('volunteer_activity_types')->onDelete('cascade');
            $table->foreignId('structure_id')->nullable()->constrained()->onDelete('set null');

            // Date et lieu
            $table->date('activity_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->string('city')->nullable();

            // Responsable
            $table->foreignId('organizer_id')->nullable()->constrained('members')->onDelete('set null');

            // Metadata
            $table->enum('status', ['planned', 'ongoing', 'completed', 'cancelled'])->default('planned');
            $table->integer('max_participants')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('activity_date');
            $table->index('status');
        });

        // Table pivot benevoles-activites
        Schema::create('volunteer_participations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('volunteer_activity_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['registered', 'confirmed', 'attended', 'absent', 'cancelled'])->default('registered');
            $table->decimal('hours_worked', 5, 2)->nullable(); // Heures effectuees
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['member_id', 'volunteer_activity_id']);
            $table->index('status');
        });

        // Inserer quelques types d'activites par defaut
        $this->seedActivityTypes();
    }

    private function seedActivityTypes(): void
    {
        $now = now();
        $types = [
            ['code' => 'ANIM', 'name' => 'Animation', 'description' => 'Animation pedagogique ou evenement', 'color' => '#2C5F2D'],
            ['code' => 'STND', 'name' => 'Tenue de stand', 'description' => 'Tenue de stand lors d\'un evenement', 'color' => '#356B8A'],
            ['code' => 'CMPT', 'name' => 'Comptage', 'description' => 'Comptage de papillons sur le terrain', 'color' => '#EDC442'],
            ['code' => 'SORT', 'name' => 'Sortie terrain', 'description' => 'Sortie de prospection ou observation', 'color' => '#85B79D'],
            ['code' => 'ADMI', 'name' => 'Administration', 'description' => 'Taches administratives', 'color' => '#6B7280'],
            ['code' => 'COMM', 'name' => 'Communication', 'description' => 'Communication, reseaux sociaux, redaction', 'color' => '#8B5CF6'],
            ['code' => 'FORM', 'name' => 'Formation', 'description' => 'Formation ou participation a une formation', 'color' => '#F97316'],
            ['code' => 'AUTR', 'name' => 'Autre', 'description' => 'Autre type d\'activite', 'color' => '#9CA3AF'],
        ];

        foreach ($types as $index => $type) {
            DB::table('volunteer_activity_types')->insert([
                'code' => $type['code'],
                'name' => $type['name'],
                'description' => $type['description'],
                'color' => $type['color'],
                'is_active' => true,
                'sort_order' => $index,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_participations');
        Schema::dropIfExists('volunteer_activities');
        Schema::dropIfExists('volunteer_activity_types');
    }
};
