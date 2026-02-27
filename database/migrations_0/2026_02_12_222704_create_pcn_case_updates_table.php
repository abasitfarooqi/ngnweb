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
        Schema::create('pcn_case_updates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('case_id')->index('pcn_case_updates_case_id_foreign');
            $table->dateTime('update_date');
            $table->boolean('is_appealed')->nullable()->default(false);
            $table->boolean('is_paid_by_owner')->nullable()->default(false);
            $table->boolean('is_paid_by_keeper')->nullable()->default(false);
            $table->decimal('additional_fee', 10)->nullable()->default(0);
            $table->string('picture_url')->nullable();
            $table->text('note');
            $table->boolean('is_transferred')->default(false);
            $table->unsignedBigInteger('user_id')->index('pcn_case_updates_user_id_foreign');
            $table->timestamps();
            $table->boolean('is_cancled')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pcn_case_updates');
    }
};
