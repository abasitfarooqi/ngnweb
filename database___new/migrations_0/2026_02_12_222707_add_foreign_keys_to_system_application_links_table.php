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
        Schema::table('system_application_links', function (Blueprint $table) {
            $table->foreign(['system_application_id'])->references(['id'])->on('system_applications')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_application_links', function (Blueprint $table) {
            $table->dropForeign('system_application_links_system_application_id_foreign');
        });
    }
};
