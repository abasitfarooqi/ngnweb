<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('document_types', 'is_motorbike')) {
            return;
        }
        Schema::table('document_types', function (Blueprint $table) {
            $table->boolean('is_motorbike')->default(false)->nullable(false);
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('document_types', 'is_motorbike')) {
            return;
        }
        Schema::table('document_types', function (Blueprint $table) {
            $table->dropColumn('is_motorbike');
        });
    }
};
