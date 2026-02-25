<?php

namespace App\Http\Controllers\Admin\Charts;

use Backpack\CRUD\app\Http\Controllers\ChartController;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use Illuminate\Support\Facades\DB;

class WeeklyUsersChartController extends ChartController
{
    public function setup()
    {
        $this->chart = new Chart;

        $statuses = [];
        $counts = [];

        $query = "
            SELECT status, COUNT(id) AS count
            FROM (
                SELECT m.id, 'AVAILABLE' AS status
                FROM motorbikes m
                INNER JOIN renting_pricings rp ON rp.motorbike_id = m.id
                WHERE m.id NOT IN (SELECT motorbike_id FROM renting_booking_items WHERE end_date IS NULL)

                UNION ALL

                SELECT m.id, 'AVAILABLE' AS status
                FROM motorbikes m
                INNER JOIN motorbikes_sale ms ON ms.motorbike_id = m.id AND ms.is_sold = false

                UNION ALL

                SELECT m.id, 'AVAILABLE FOR REPAIR' AS status
                FROM motorbikes m
                INNER JOIN motorbikes_repair mr ON mr.motorbike_id = m.id AND mr.is_returned = false

                UNION ALL

                SELECT m.id, 'CATEGORY B' AS status
                FROM motorbikes m
                INNER JOIN motorbikes_cat_b mcb ON mcb.motorbike_id = m.id

                UNION ALL

                SELECT m.id, 'CLAIM' AS status
                FROM motorbikes m
                INNER JOIN claim_motorbikes cm ON cm.motorbike_id = m.id AND cm.is_returned = false
            ) AS unified
            GROUP BY status
        ";

        $data = DB::select($query);

        foreach ($data as $row) {
            $statuses[] = $row->status;
            $counts[] = $row->count;
        }

        $this->chart->dataset('Status Counts', 'pie', $counts)
            ->backgroundColor([
                'rgb(70, 127, 208)',
                'rgb(77, 189, 116)',
                'rgb(96, 92, 168)',
                'rgb(255, 193, 7)',
                'rgb(255, 87, 34)',
            ]);

        $this->chart->labels($statuses);
        $this->chart->title('In-house Motorbikes');
        $this->chart->displayAxes(true);
        $this->chart->minimalist(false);
    }
}
