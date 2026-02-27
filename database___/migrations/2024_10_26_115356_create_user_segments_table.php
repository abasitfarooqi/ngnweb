<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSegmentsTable extends Migration
{
    public function up()
    {
        Schema::create('user_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_member_id')->constrained('club_members')->onDelete('cascade');
            $table->string('segment_type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_segments');
    }
}
