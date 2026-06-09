<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motorbike_annual_compliance', function (Blueprint $table) {
            $table->string('road_tax_status', 100)->change();
            $table->string('insurance_status', 100)->change();
            $table->string('mot_status', 100)->change();
            if (! Schema::hasColumn('motorbike_annual_compliance', 'tax_due_date')) {
                $table->date('tax_due_date')->nullable();
            }
            if (! Schema::hasColumn('motorbike_annual_compliance', 'insurance_due_date')) {
                $table->date('insurance_due_date')->nullable();
            }
            if (! Schema::hasColumn('motorbike_annual_compliance', 'mot_due_date')) {
                $table->date('mot_due_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('motorbike_annual_compliance', function (Blueprint $table) {
            $table->string('road_tax_status')->change();
            $table->string('insurance_status')->change();
            $table->string('mot_status')->change();
            if (Schema::hasColumn('motorbike_annual_compliance', 'tax_due_date')) {
                $table->dropColumn('tax_due_date');
            }
            if (Schema::hasColumn('motorbike_annual_compliance', 'insurance_due_date')) {
                $table->dropColumn('insurance_due_date');
            }
            if (Schema::hasColumn('motorbike_annual_compliance', 'mot_due_date')) {
                $table->dropColumn('mot_due_date');
            }
        });
    }
};
