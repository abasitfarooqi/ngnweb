<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFeedbackTable extends Migration
{
    public function up()
    {
        Schema::create('user_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_member_id')->constrained('club_members')->onDelete('cascade');
            $table->text('feedback_text');
            $table->timestamp('submitted_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_feedback');
    }
}
