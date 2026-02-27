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
        if (Schema::hasColumn('customer_documents', 'id_deleted')) {
            return;
        }
        Schema::table('customer_documents', function (Blueprint $table) {
            $table->string('id_deleted')->nullable()->default('0')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('customer_documents', 'id_deleted')) {
            return;
        }
        Schema::table('customer_documents', function (Blueprint $table) {
            $table->dropColumn('id_deleted');
        });
    }
};
