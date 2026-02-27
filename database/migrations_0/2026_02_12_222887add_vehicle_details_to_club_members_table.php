<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('club_members', function (Blueprint $table) {
            $table->string('make', 50)->nullable()->after('phone');
            $table->string('model', 50)->nullable()->after('make');
            $table->string('year', 4)->nullable()->after('model');
            $table->string('vrm', 12)->nullable()->after('year');
            $table->foreignId('ngn_partner_id')->nullable()->constrained('ngn_partners');
            $table->boolean('is_partner')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('club_members', function (Blueprint $table) {
            $table->dropForeign(['ngn_partner_id']);
            $table->dropColumn([
                'make',
                'model',
                'year',
                'vrm',
                'ngn_partner_id',
                'is_partner',
            ]);
        });
    }
};
