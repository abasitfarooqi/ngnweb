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
        Schema::create('collection_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('rule');
            $table->string('operator');
            $table->string('value');
            $table->unsignedBigInteger('collection_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_rules');
    }
};
