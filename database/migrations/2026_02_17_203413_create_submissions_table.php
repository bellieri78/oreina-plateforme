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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('journal_issue_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('abstract');
            $table->string('manuscript_file');           // Fichier PDF soumis
            $table->json('co_authors')->nullable();      // Liste des co-auteurs
            $table->string('keywords')->nullable();
            $table->string('status')->default('submitted'); // submitted, desk_review, in_review, revision, accepted, rejected, published
            $table->foreignId('editor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('editor_notes')->nullable();
            $table->string('decision')->nullable();       // accept, minor_revision, major_revision, reject
            $table->timestamp('decision_at')->nullable();
            $table->string('doi')->nullable();
            $table->integer('start_page')->nullable();
            $table->integer('end_page')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('author_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
