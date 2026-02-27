<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('motorbikes_sale', 'v5_available')) {
            return;
        }
        Schema::table('motorbikes_sale', function (Blueprint $table) {
            $table->boolean('v5_available')->default(true)->after('note')->nullable(true);
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('motorbikes_sale', 'v5_available')) {
            return;
        }
        Schema::table('motorbikes_sale', function (Blueprint $table) {
            $table->dropColumn('v5_available');
        });
    }
};
