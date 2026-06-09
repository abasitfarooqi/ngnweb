<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motorbikes_repair', function (Blueprint $table) {
            $table->string('fullname');
            $table->string('email');
            $table->string('phone');
        });
    }

    public function down(): void
    {
        Schema::table('motorbikes_repair', function (Blueprint $table) {
            $table->dropColumn(['fullname', 'email', 'phone']);
        });
    }
};
