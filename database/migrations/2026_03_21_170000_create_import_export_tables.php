<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Import/Export templates
        Schema::create('import_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // members, memberships, donations
            $table->json('mapping'); // column mappings
            $table->json('options')->nullable(); // additional options
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Import history
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // members, memberships, donations
            $table->string('filename');
            $table->integer('total_rows')->default(0);
            $table->integer('imported_rows')->default(0);
            $table->integer('updated_rows')->default(0);
            $table->integer('skipped_rows')->default(0);
            $table->integer('error_rows')->default(0);
            $table->json('errors')->nullable(); // detailed errors
            $table->json('options')->nullable(); // options used
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('import_templates')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Export templates
        Schema::create('export_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // members, memberships, donations
            $table->json('columns'); // columns to export
            $table->json('filters')->nullable(); // pre-defined filters
            $table->json('options')->nullable(); // format options
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Export history
        Schema::create('export_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // members, memberships, donations
            $table->string('filename');
            $table->integer('total_rows')->default(0);
            $table->json('filters')->nullable(); // filters applied
            $table->json('columns')->nullable(); // columns exported
            $table->string('format')->default('csv'); // csv, xlsx
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('export_templates')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_logs');
        Schema::dropIfExists('export_templates');
        Schema::dropIfExists('import_logs');
        Schema::dropIfExists('import_templates');
    }
};
