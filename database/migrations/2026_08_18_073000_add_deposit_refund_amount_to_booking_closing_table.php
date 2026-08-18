<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('booking_closing', 'deposit_refund_amount')) {
            return;
        }

        $afterColumn = Schema::hasColumn('booking_closing', 'deposit_return_notes')
            ? 'deposit_return_notes'
            : 'deposit_checked';

        Schema::table('booking_closing', function (Blueprint $table) use ($afterColumn) {
            $table->decimal('deposit_refund_amount', 10, 2)->nullable()->after($afterColumn);
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('booking_closing', 'deposit_refund_amount')) {
            return;
        }

        Schema::table('booking_closing', function (Blueprint $table) {
            $table->dropColumn('deposit_refund_amount');
        });
    }
};
