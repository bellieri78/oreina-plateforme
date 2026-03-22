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
        Schema::table('submissions', function (Blueprint $table) {
            // Contenu formaté de l'article (HTML)
            $table->text('content_html')->nullable()->after('abstract');

            // Références bibliographiques (JSON array)
            $table->json('references')->nullable()->after('content_html');

            // Remerciements
            $table->text('acknowledgements')->nullable()->after('references');

            // Affiliations des auteurs (JSON pour stocker les affiliations détaillées)
            $table->json('author_affiliations')->nullable()->after('co_authors');

            // Date de réception et acceptation (pour le PDF)
            $table->date('received_at')->nullable()->after('submitted_at');
            $table->date('accepted_at')->nullable()->after('received_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn([
                'content_html',
                'references',
                'acknowledgements',
                'author_affiliations',
                'received_at',
                'accepted_at',
            ]);
        });
    }
};
