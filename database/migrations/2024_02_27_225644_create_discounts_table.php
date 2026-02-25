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
        if (! Schema::hasTable('discounts')) {
            Schema::create('discounts', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->boolean('is_active');
                $table->string('code');
                $table->string('type');
                $table->integer('value');
                $table->string('apply_to');
                $table->string('min_required');
                $table->string('min_required_value')->nullable();
                $table->string('eligibility');
                $table->integer('usage_limit')->nullable();
                $table->boolean('usage_limit_per_user');
                $table->integer('total_use');
                $table->timestamp('start_at');
                $table->timestamp('end_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
