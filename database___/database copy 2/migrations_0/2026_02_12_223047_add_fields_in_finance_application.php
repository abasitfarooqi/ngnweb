<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->decimal('motorbike_price', 10, 2)->default(0.00);
        });
    }

    public function down(): void
    {
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->dropColumn(['motorbike_price']);
        });
    }
};
