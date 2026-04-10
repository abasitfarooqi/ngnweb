<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_bookings', function (Blueprint $table): void {
            if (! Schema::hasColumn('service_bookings', 'conversation_id')) {
                $table->foreignId('conversation_id')->nullable()->after('customer_auth_id');
                $table->foreign('conversation_id')
                    ->references('id')
                    ->on('support_conversations')
                    ->nullOnDelete();
                $table->index('conversation_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table): void {
            if (Schema::hasColumn('service_bookings', 'conversation_id')) {
                $table->dropForeign(['conversation_id']);
                $table->dropIndex(['conversation_id']);
                $table->dropColumn('conversation_id');
            }
        });
    }
};
