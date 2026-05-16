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
        Schema::dropIfExists('chat_messages'); // abandon mur global (assume) - dropIfExists cible, pas un wipe

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_low_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('member_high_id')->constrained('members')->cascadeOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('member_low_read_at')->nullable();
            $table->timestamp('member_high_read_at')->nullable();
            $table->timestamps();
            $table->unique(['member_low_id', 'member_high_id'], 'conversations_pair_unique');
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('members')->cascadeOnDelete();
            $table->text('content');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            $table->index(['conversation_id', 'created_at'], 'chat_messages_conv_created_idx');
        });

        Schema::create('chat_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blocker_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('blocked_id')->constrained('members')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['blocker_id', 'blocked_id'], 'chat_blocks_pair_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_blocks');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('conversations');

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });
    }
};
