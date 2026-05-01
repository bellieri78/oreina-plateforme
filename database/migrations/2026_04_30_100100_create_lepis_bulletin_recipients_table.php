<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lepis_bulletin_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lepis_bulletin_id')->constrained('lepis_bulletins')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('membership_id')->nullable()->constrained('memberships')->nullOnDelete();
            $table->string('format', 10);
            $table->string('email_at_snapshot')->nullable();
            $table->jsonb('postal_address_at_snapshot')->nullable();
            $table->integer('brevo_list_id')->nullable();
            $table->timestamp('included_at')->useCurrent();
            $table->timestamps();

            $table->unique(['lepis_bulletin_id', 'member_id'], 'lepis_recipients_bulletin_member_unique');
            $table->index('member_id', 'lepis_recipients_member_idx');
            $table->index(['lepis_bulletin_id', 'format'], 'lepis_recipients_bulletin_format_idx');
        });

        DB::statement("ALTER TABLE lepis_bulletin_recipients ADD CONSTRAINT lepis_recipients_format_check CHECK (format IN ('paper', 'digital'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('lepis_bulletin_recipients');
    }
};
