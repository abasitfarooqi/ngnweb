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
        Schema::table('ngn_careers', function (Blueprint $table) {
            $table->string('salary')->change(); // Change salary from decimal to varchar
            $table->boolean('is_active')->default(0); // Add is_active column (1 = active, 0 = inactive)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_careers', function (Blueprint $table) {
            $table->decimal('salary', 10, 2)->change(); // Revert salary back to decimal
            $table->dropColumn('is_active'); // Remove is_active column
        });
    }
};
