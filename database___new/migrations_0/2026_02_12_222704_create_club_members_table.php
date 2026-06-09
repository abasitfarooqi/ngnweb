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
        Schema::create('club_members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->unsignedBigInteger('user_id')->nullable()->index('club_members_user_id_foreign')->comment('Last user who updated this record');
            $table->string('make', 50)->nullable();
            $table->string('model', 50)->nullable();
            $table->string('year', 4)->nullable();
            $table->string('vrm', 12)->nullable();
            $table->date('dob_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('tc_agreed')->default(true);
            $table->string('passkey', 10)->nullable();
            $table->timestamps();
            $table->boolean('email_sent')->default(false);
            $table->unsignedBigInteger('ngn_partner_id')->nullable()->index('club_members_ngn_partner_id_foreign');
            $table->boolean('is_partner')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_members');
    }
};
