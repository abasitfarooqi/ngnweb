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
        Schema::create('rentals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('make', 70);
            $table->string('model', 70);
            $table->string('engine', 70);
            $table->string('year', 70);
            $table->string('colour', 70);
            $table->bigInteger('user_id')->nullable();
            $table->binary('signature')->nullable();
            $table->bigInteger('motorcycle_id')->nullable();
            $table->string('registration')->nullable();
            $table->decimal('deposit')->nullable();
            $table->decimal('price')->nullable();
            $table->date('created_at')->nullable();
            $table->date('updated_at')->nullable();
            $table->string('auth_user')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
