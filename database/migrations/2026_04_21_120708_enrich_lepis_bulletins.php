<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Ajout des nouvelles colonnes
        Schema::table('lepis_bulletins', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('year');
            $table->timestamp('published_to_members_at')->nullable()->after('status');
            $table->timestamp('published_public_at')->nullable()->after('published_to_members_at');
            $table->text('summary')->nullable()->after('title');
            $table->string('cover_image')->nullable()->after('summary');
            $table->string('announcement_subject')->nullable()->after('cover_image');
            $table->text('announcement_body')->nullable()->after('announcement_subject');
            $table->unsignedBigInteger('brevo_list_id')->nullable();
            $table->string('brevo_list_name')->nullable();
            $table->timestamp('brevo_synced_at')->nullable();
            $table->boolean('brevo_sync_failed')->default(false);
        });

        // 2. Migration des données existantes depuis is_published
        DB::table('lepis_bulletins')
            ->where('is_published', true)
            ->update([
                'status' => 'members',
                'published_to_members_at' => DB::raw('COALESCE(published_at, created_at)'),
            ]);

        // 3. Drop des colonnes obsolètes
        Schema::table('lepis_bulletins', function (Blueprint $table) {
            $table->dropColumn(['is_published', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::table('lepis_bulletins', function (Blueprint $table) {
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
        });

        DB::table('lepis_bulletins')
            ->whereIn('status', ['members', 'public'])
            ->update([
                'is_published' => true,
                'published_at' => DB::raw('COALESCE(published_to_members_at, created_at)'),
            ]);

        Schema::table('lepis_bulletins', function (Blueprint $table) {
            $table->dropColumn([
                'status', 'published_to_members_at', 'published_public_at',
                'summary', 'cover_image', 'announcement_subject', 'announcement_body',
                'brevo_list_id', 'brevo_list_name', 'brevo_synced_at', 'brevo_sync_failed',
            ]);
        });
    }
};
