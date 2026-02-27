<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ec_payment_methods')) {
            Schema::create('ec_payment_methods', function (Blueprint $table) {
                $table->bigIncrements('id');

                $table->string('title');
                $table->string('slug');
                $table->string('logo')->default('-');
                $table->string('link_url')->default('-');
                $table->text('instructions')->nullable();
                $table->boolean('is_enabled')->default(false);

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ec_payment_methods');
    }
};
