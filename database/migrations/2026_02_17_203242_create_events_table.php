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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('event_type')->nullable();     // sortie, conference, atelier, ag, etc.
            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->string('location_name')->nullable();
            $table->text('location_address')->nullable();
            $table->string('location_city')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('max_participants')->nullable();
            $table->boolean('registration_required')->default(false);
            $table->string('registration_url')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('status')->default('draft');   // draft, published, cancelled
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
