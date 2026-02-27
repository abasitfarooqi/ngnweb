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
        Schema::table('purchase_request_items', function (Blueprint $table) {
            $table->foreign(['bike_model_id'])->references(['id'])->on('bike_models')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['brand_name_id'])->references(['id'])->on('makes')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['pr_id'])->references(['id'])->on('purchase_requests')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_request_items', function (Blueprint $table) {
            $table->dropForeign('purchase_request_items_bike_model_id_foreign');
            $table->dropForeign('purchase_request_items_brand_name_id_foreign');
            $table->dropForeign('purchase_request_items_created_by_foreign');
            $table->dropForeign('purchase_request_items_pr_id_foreign');
        });
    }
};
