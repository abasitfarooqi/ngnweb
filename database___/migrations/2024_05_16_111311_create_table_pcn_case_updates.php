<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pcn_case_updates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id');
            $table->foreign('case_id')->references('id')->on('pcn_cases')->onDelete('cascade');
            $table->dateTime('update_date');
            $table->boolean('is_appealed')->default(false)->nullable();
            $table->boolean('is_paid_by_owner')->default(false)->nullable();
            $table->boolean('is_paid_by_keeper')->default(false)->nullable();
            $table->decimal('additional_fee', 10, 2)->nullable()->default(0);
            $table->string('picture_url')->nullable();
            $table->text('note');
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pcn_case_updates');
    }
};
