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
        $tables = ['customer_documents', 'customer_agreements', 'customer_contracts'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'sent_private')) {
                    $table->boolean('sent_private')->default(false)->after('file_path');
                }
            });
        }
    }

    public function down(): void
    {
        $tables = ['customer_documents', 'customer_agreements', 'customer_contracts'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'sent_private')) {
                    $table->dropColumn('sent_private');
                }
            });
        }
    }
};
