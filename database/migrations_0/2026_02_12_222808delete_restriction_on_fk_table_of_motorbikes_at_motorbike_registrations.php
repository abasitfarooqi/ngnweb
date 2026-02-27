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
        Schema::table('motorbike_registrations', function (Blueprint $table) {

            $table->dropForeign(['motorbike_id']);

            $table->foreign('motorbike_id')
                ->references('id')->on('motorbikes')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motorbike_registrations', function (Blueprint $table) {
            // $table->dropForeign(['motorbike_id']);
        });
    }
};
