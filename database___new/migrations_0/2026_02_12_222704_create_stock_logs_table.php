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
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description');
            $table->string('color')->nullable();
            $table->text('picture')->nullable();
            $table->integer('qty');
            $table->timestamps();
            $table->unsignedBigInteger('branch_id')->default(1)->index('stock_logs_branch_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('stock_logs_user_id_foreign');
            $table->string('sku')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
    }
};
