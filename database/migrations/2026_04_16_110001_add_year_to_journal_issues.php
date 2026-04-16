<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('journal_issues', function (Blueprint $t) {
            $t->unsignedSmallInteger('year')->nullable()->after('issue_number');
            $t->index('year');
        });
        DB::table('journal_issues')
            ->whereNotNull('publication_date')
            ->whereNull('year')
            ->update(['year' => DB::raw('EXTRACT(YEAR FROM publication_date)::integer')]);
    }
    public function down(): void {
        Schema::table('journal_issues', function (Blueprint $t) {
            $t->dropIndex(['year']);
            $t->dropColumn('year');
        });
    }
};
