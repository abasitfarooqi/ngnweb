<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('finance_applications', 'motorbike_price')) {
            return;
        }
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->decimal('motorbike_price', 10, 2)->default(0.00);
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('finance_applications', 'motorbike_price')) {
            return;
        }
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->dropColumn(['motorbike_price']);
        });
    }
};
