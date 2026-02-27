<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motorbikes_repair', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->default(1);
            $table->unsignedBigInteger('user_id')->default(93);

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });

        DB::table('motorbikes_repair')->update([
            'branch_id' => 1,
            'user_id' => 93,
        ]);

        Schema::table('motorbikes_repair', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->default(null)->change();
            $table->unsignedBigInteger('user_id')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('motorbikes_repair', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['branch_id', 'user_id']);
        });
    }

    //

};
