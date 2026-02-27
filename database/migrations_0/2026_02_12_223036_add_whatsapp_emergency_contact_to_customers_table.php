<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('emergency_contact')->nullable()
                ->comment('Name of the emergency contact person');
            $table->string('whatsapp')->nullable()
                ->comment('Whatsapp number');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('emergency_contact');
            $table->dropColumn('whatsapp');
        });
    }
};
