<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_internal')->default(true);
            $table->timestamps();
        });

        DB::table('vehicle_profiles')->insert([
            ['name' => 'Internal', 'is_internal' => true],
            ['name' => 'External', 'is_internal' => false],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_profiles');
    }
};
