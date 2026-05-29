<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ngn_categories')) {
            Schema::create('ngn_categories', function (Blueprint $table) {
                $table->bigIncrements('id');

                $table->string('name')->unique();
                $table->string('image_url')->nullable();

                $table->timestamps();

                $table->string('slug')->default('');
                $table->text('description')->nullable();        // seeder inserts '' so nullable is safe
                $table->boolean('is_ecommerce')->default(true);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->string('meta_title')->default('');
                $table->text('meta_description')->nullable();

                $table->unsignedBigInteger('super_category_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ngn_categories');
    }
};
