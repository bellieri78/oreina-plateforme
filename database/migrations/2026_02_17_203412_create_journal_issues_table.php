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
        Schema::create('journal_issues', function (Blueprint $table) {
            $table->id();
            $table->integer('volume_number');
            $table->integer('issue_number');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('pdf_file')->nullable();       // PDF complet du numéro
            $table->date('publication_date')->nullable();
            $table->string('status')->default('draft');   // draft, published
            $table->string('doi')->nullable();            // DOI du numéro
            $table->integer('page_count')->nullable();
            $table->timestamps();

            $table->unique(['volume_number', 'issue_number']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_issues');
    }
};
