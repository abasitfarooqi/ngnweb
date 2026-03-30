<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sp_makes', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('source')->default('internal');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sp_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('make_id')->constrained('sp_makes')->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['make_id', 'slug']);
        });

        Schema::create('sp_fitments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_id')->constrained('sp_models')->cascadeOnDelete();
            $table->string('year', 16);
            $table->string('country_slug');
            $table->string('country_name');
            $table->string('colour_slug');
            $table->string('colour_name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['model_id', 'year']);
            $table->unique(['model_id', 'year', 'country_slug', 'colour_slug'], 'sp_fitments_unique_path');
        });

        Schema::create('sp_assemblies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fitment_id')->constrained('sp_fitments')->cascadeOnDelete();
            $table->string('external_id')->nullable();
            $table->string('slug');
            $table->string('name');
            $table->string('image_url')->nullable();
            $table->string('diagram_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['fitment_id', 'sort_order']);
            $table->unique(['fitment_id', 'slug']);
        });

        Schema::create('sp_parts', function (Blueprint $table) {
            $table->id();
            $table->string('part_number')->unique();
            $table->string('name');
            $table->text('note')->nullable();
            $table->string('stock_status')->default('NOT IN STOCK');
            $table->decimal('price_gbp_inc_vat', 10, 2)->default(0);
            $table->json('meta')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sp_assembly_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assembly_id')->constrained('sp_assemblies')->cascadeOnDelete();
            $table->foreignId('part_id')->constrained('sp_parts')->cascadeOnDelete();
            $table->unsignedInteger('qty_used')->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('note_override')->nullable();
            $table->decimal('price_override', 10, 2)->nullable();
            $table->string('stock_override')->nullable();
            $table->timestamps();

            $table->index(['assembly_id', 'sort_order']);
            $table->unique(['assembly_id', 'part_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sp_assembly_parts');
        Schema::dropIfExists('sp_parts');
        Schema::dropIfExists('sp_assemblies');
        Schema::dropIfExists('sp_fitments');
        Schema::dropIfExists('sp_models');
        Schema::dropIfExists('sp_makes');
    }
};
