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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');

            // Invitation status
            $table->string('status')->default('invited'); // invited, accepted, declined, completed, expired
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Review content
            $table->string('recommendation')->nullable(); // accept, minor_revision, major_revision, reject
            $table->text('comments_to_editor')->nullable();   // Commentaires confidentiels pour l'éditeur
            $table->text('comments_to_author')->nullable();   // Commentaires pour l'auteur

            // Evaluation criteria (1-5 scale)
            $table->unsignedTinyInteger('score_originality')->nullable();
            $table->unsignedTinyInteger('score_methodology')->nullable();
            $table->unsignedTinyInteger('score_clarity')->nullable();
            $table->unsignedTinyInteger('score_significance')->nullable();
            $table->unsignedTinyInteger('score_references')->nullable();

            // Optional file attachment (annotated manuscript)
            $table->string('review_file')->nullable();

            $table->timestamps();

            $table->index(['submission_id', 'status']);
            $table->unique(['submission_id', 'reviewer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
