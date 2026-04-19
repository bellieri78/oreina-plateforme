<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('article_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->string('event_type', 24); // view | pdf_download | share
            $table->string('hashed_ip', 64)->nullable();
            $table->string('cookie_id', 64)->nullable();
            $table->string('network', 32)->nullable(); // twitter|linkedin|mail|copy|native — nul sauf pour share
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();

            $table->index(['submission_id', 'event_type', 'occurred_at']);
            $table->index(['submission_id', 'event_type', 'hashed_ip']);
            $table->index(['submission_id', 'event_type', 'cookie_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_events');
    }
};
