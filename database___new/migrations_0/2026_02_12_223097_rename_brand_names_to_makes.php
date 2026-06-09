<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('brand_names')) {
            Schema::rename('brand_names', 'makes');
        }
    }

    public function down(): void
    {
        Schema::rename('makes', 'brand_names');
    }
};
