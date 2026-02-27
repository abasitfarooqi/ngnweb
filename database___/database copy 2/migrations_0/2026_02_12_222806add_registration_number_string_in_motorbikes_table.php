<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motorbikes', function (Blueprint $table) {
            if (! Schema::hasColumn('motorbikes', 'reg_no')) {
                $table->string('reg_no')->nullable();
                $table->date('date_of_last_v5c_issuance')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('motorbikes', function (Blueprint $table) {
            $table->dropColumn('reg_no');
            $table->dropColumn('date_of_last_v5c_issuance');
        });
    }
};
