<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('renting_weekly_updates')) {
            Schema::create('renting_weekly_updates', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('booking_id')->constrained('renting_bookings')->cascadeOnDelete();
                $table->foreignId('invoice_id')->nullable()->constrained('booking_invoices')->nullOnDelete();
                $table->longText('note');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['booking_id', 'created_at']);
                $table->index(['invoice_id', 'created_at']);
            });
        }

        if (! Schema::hasTable('renting_weekly_update_logs')) {
            Schema::create('renting_weekly_update_logs', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('renting_weekly_update_id')->nullable()->index();
                $table->string('action', 16);
                $table->json('old_data')->nullable();
                $table->json('new_data')->nullable();
                $table->unsignedBigInteger('changed_by')->nullable()->index();
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('renting_weekly_update_logs');
        Schema::dropIfExists('renting_weekly_updates');
    }
};
