<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('communication_replies')) {
            return;
        }

        Schema::create('communication_replies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('communication_id')->constrained('communications')->cascadeOnDelete();
            $table->string('author_type');
            $table->unsignedBigInteger('author_id')->nullable()->index();
            $table->text('body');
            $table->timestamps();

            $table->index(['communication_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communication_replies');
    }
};
