<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('branches', 'address')) {
            return;
        }
        Schema::table('branches', function (Blueprint $table) {
            $table->string('address')->nullable()->after('name'); // Adding the address column after 'name'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('branches', 'address')) {
            return;
        }
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('address'); // Dropping the address column in case of rollback
        });
    }
};
