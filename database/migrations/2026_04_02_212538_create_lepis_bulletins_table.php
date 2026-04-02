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
        Schema::create('lepis_bulletins', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('issue_number');
            $table->string('quarter', 2); // Q1, Q2, Q3, Q4
            $table->integer('year');
            $table->string('pdf_path');
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lepis_bulletins');
    }
};
