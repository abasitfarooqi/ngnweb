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
        Schema::create('motorbikes_cat_b', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('dop');
            $table->unsignedBigInteger('motorbike_id')->unique();
            $table->text('notes');
            $table->timestamps();
            $table->unsignedBigInteger('branch_id')->nullable()->index('motorbikes_cat_b_branch_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorbikes_cat_b');
    }
};
