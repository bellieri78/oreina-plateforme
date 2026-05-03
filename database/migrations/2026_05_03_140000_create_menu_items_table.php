<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->string('location', 20);
            $table->string('label', 255);
            $table->string('url', 500);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('open_in_new_tab')->default(false);
            $table->timestamps();

            $table->index(['location', 'is_active', 'sort_order'], 'menu_items_location_active_order_idx');
            $table->index('parent_id', 'menu_items_parent_idx');
        });

        // CHECK constraint via raw SQL (PG 9.6 compat)
        DB::statement("ALTER TABLE menu_items ADD CONSTRAINT menu_items_location_check CHECK (location IN ('header', 'footer'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
