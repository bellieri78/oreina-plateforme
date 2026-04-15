<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_transitions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $t->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('action', 64);
            $t->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('from_status')->nullable();
            $t->string('to_status')->nullable();
            $t->text('notes')->nullable();
            $t->timestamps();
            $t->index(['submission_id', 'created_at']);
            $t->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_transitions');
    }
};
