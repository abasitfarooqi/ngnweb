<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_issuance_items', function (Blueprint $table) {
            $table->text('notes', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('booking_issuance_items', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
