<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('motorbikes_sale')) {
            return;
        }

        if (! Schema::hasColumn('motorbikes_sale', 'is_rented')) {
            Schema::table('motorbikes_sale', function (Blueprint $table) {
                $table->boolean('is_rented')->default(false)->after('is_sold');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('motorbikes_sale') && Schema::hasColumn('motorbikes_sale', 'is_rented')) {
            Schema::table('motorbikes_sale', function (Blueprint $table) {
                $table->dropColumn('is_rented');
            });
        }
    }
};
