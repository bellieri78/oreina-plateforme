<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('color', 7)->default('#6b7280'); // Hex color
            $table->text('description')->nullable();
            $table->string('source')->default('manual'); // manual, brevo, import, etc.
            $table->timestamps();
        });

        Schema::create('member_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->string('source')->default('manual'); // How this tag was assigned
            $table->timestamps();

            $table->unique(['member_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_tag');
        Schema::dropIfExists('tags');
    }
};
