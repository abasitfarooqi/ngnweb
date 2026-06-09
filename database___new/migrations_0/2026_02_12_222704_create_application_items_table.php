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
        Schema::create('application_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('application_id')->index('application_items_application_id_foreign');
            $table->unsignedBigInteger('motorbike_id')->index('application_items_motorbike_id_foreign');
            $table->unsignedBigInteger('user_id')->index('application_items_user_id_foreign');
            $table->boolean('is_posted')->default(false);
            $table->timestamps();
            $table->integer('app_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_items');
    }
};
