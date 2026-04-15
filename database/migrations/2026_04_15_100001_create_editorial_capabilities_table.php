<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editorial_capabilities', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('capability', 32);
            $t->foreignId('granted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('granted_at');
            $t->timestamps();
            $t->unique(['user_id', 'capability']);
            $t->index('capability');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editorial_capabilities');
    }
};
