<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Table des structures (antennes, groupes locaux, etc.)
        Schema::create('structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('structures')->onDelete('set null');
            $table->string('code')->unique(); // Ex: NAT, REG-ARA, DEP-69
            $table->string('name'); // Ex: "Groupe Auvergne-Rhone-Alpes"
            $table->enum('type', ['national', 'regional', 'departemental', 'local'])->default('local');
            $table->text('description')->nullable();

            // Localisation
            $table->string('departement_code', 3)->nullable(); // 01, 2A, 69, etc.
            $table->string('region')->nullable();

            // Contact
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('city')->nullable();

            // Responsable
            $table->foreignId('responsable_id')->nullable()->constrained('members')->onDelete('set null');

            // Metadata
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('type');
            $table->index('parent_id');
            $table->index('departement_code');
        });

        // Table pivot membres-structures
        Schema::create('member_structure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('structure_id')->constrained()->onDelete('cascade');
            $table->string('role')->nullable(); // responsable, membre, correspondant, etc.
            $table->date('joined_at')->nullable();
            $table->date('left_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['member_id', 'structure_id']);
            $table->index('structure_id');
        });

        // Inserer la structure nationale par defaut
        DB::table('structures')->insert([
            'code' => 'NAT',
            'name' => 'OREINA National',
            'type' => 'national',
            'description' => 'Structure nationale de l\'association OREINA',
            'is_active' => true,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('member_structure');
        Schema::dropIfExists('structures');
    }
};
