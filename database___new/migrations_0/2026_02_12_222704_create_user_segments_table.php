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
        Schema::create('user_segments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('club_member_id')->index('user_segments_club_member_id_foreign');
            $table->string('segment_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_segments');
    }
};
