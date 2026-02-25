<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ngn_super_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->string('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_ecommerce')->default(true);
            $table->timestamps();
        });

        // insert Others
        DB::table('ngn_super_categories')->insert([
            'name' => 'Others',
            'slug' => 'others',
            'is_active' => true,
            'is_ecommerce' => true,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ngn_super_categories');
    }
};
