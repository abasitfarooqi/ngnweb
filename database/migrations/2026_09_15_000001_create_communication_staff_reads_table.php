<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communication_staff_reads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('communication_id')->constrained('communications')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->index();
            $table->timestamp('opened_at');
            $table->timestamps();
            $table->unique(['communication_id', 'user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('communication_staff_reads'); }
};
