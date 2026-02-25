<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable(false);
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable(); // nextdue date
            $table->string('state')->default('DRAFT');
            $table->boolean('is_posted')->default(false);
            $table->timestamps();
            $table->decimal('deposit', 10, 2)->default(0.00);
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::drop('finance_applications');
    }
};
