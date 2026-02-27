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
        Schema::create('vehicle_issuances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->nullable()->index('vehicle_issuances_customer_id_foreign');
            $table->dateTime('issue_date');
            $table->unsignedBigInteger('user_id')->index('vehicle_issuances_user_id_foreign');
            $table->unsignedBigInteger('branch_id')->index('vehicle_issuances_branch_id_foreign');
            $table->unsignedBigInteger('motorbike_id')->index('vehicle_issuances_motorbike_id_foreign');
            $table->text('notes')->nullable();
            $table->boolean('is_returned')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_issuances');
    }
};
