<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! config('database.connections.remote_source')) {
            return;
        }
        DB::connection('remote_source')->statement("CREATE VIEW `vw_monthly_rental_revenue` AS select date_format(`bi`.`invoice_date`,'%Y-%m') AS `revenue_month`,sum(`bi`.`amount`) AS `total_revenue` from `nqfkhvtysa`.`booking_invoices` `bi` where `bi`.`is_paid` = 1 group by date_format(`bi`.`invoice_date`,'%Y-%m') order by date_format(`bi`.`invoice_date`,'%Y-%m') desc");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! config('database.connections.remote_source')) {
            return;
        }
        DB::connection('remote_source')->statement("DROP VIEW IF EXISTS `vw_monthly_rental_revenue`");
    }
};
