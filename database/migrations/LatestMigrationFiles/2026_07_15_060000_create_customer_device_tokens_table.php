<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('customer_device_tokens')) {
            return;
        }

        Schema::create('customer_device_tokens', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('customer_auth_id');
            $table->string('token', 255);
            $table->string('provider', 20)->default('expo');
            $table->string('platform', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique(['customer_auth_id', 'token']);
            $table->index('token');
        });
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
