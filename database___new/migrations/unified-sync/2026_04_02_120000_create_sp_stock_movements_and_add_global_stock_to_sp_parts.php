<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sp_parts', function (Blueprint $table) {
            $table->decimal('global_stock', 10, 2)->default(0)->after('price_gbp_inc_vat');
        });

        Schema::create('sp_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sp_part_id')->constrained('sp_parts')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->dateTime('transaction_date')->nullable();
            $table->decimal('in', 10, 2)->default(0);
            $table->decimal('out', 10, 2)->default(0);
            $table->string('transaction_type')->default('adjustment');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ref_doc_no')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->index(['sp_part_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sp_stock_movements');

        Schema::table('sp_parts', function (Blueprint $table) {
            $table->dropColumn('global_stock');
        });
    }
};
