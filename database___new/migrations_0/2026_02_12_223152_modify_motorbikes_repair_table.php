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
        Schema::table('motorbikes_repair', function (Blueprint $table) {
            //
            $table->dateTime('returned_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motorbikes_repair', function (Blueprint $table) {
            $table->date('returned_date')->nullable(false)->change();
        });
    }
};
