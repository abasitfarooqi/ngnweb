<?php

namespace App\Http\Controllers\Admin\Charts;

use App\Models\PcnCase;
use Backpack\CRUD\app\Http\Controllers\ChartController;
use Carbon\Carbon;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use Illuminate\Support\Facades\DB;

class PcnCaseChartController extends ChartController
{
    public function setup()
    {
        $this->chart = new Chart;

        // Get the last 6 months of PCN data
        $data = PcnCase::select(
            DB::raw('DATE_FORMAT(date_of_contravention, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN isClosed = 1 THEN 1 ELSE 0 END) as closed'),
            DB::raw('SUM(CASE WHEN isClosed = 0 THEN 1 ELSE 0 END) as open')
        )
            ->where('date_of_contravention', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $this->chart->labels($data->pluck('month')->toArray());

        $this->chart->dataset('Total Cases', 'line', $data->pluck('total')->toArray())
            ->color('rgb(75, 192, 192)')
            ->backgroundColor('rgba(75, 192, 192, 0.1)');

        $this->chart->dataset('Closed Cases', 'line', $data->pluck('closed')->toArray())
            ->color('rgb(54, 162, 235)')
            ->backgroundColor('rgba(54, 162, 235, 0.1)');

        $this->chart->dataset('Open Cases', 'line', $data->pluck('open')->toArray())
            ->color('rgb(255, 99, 132)')
            ->backgroundColor('rgba(255, 99, 132, 0.1)');

        // Chart customization
        $this->chart->options([
            'scales' => [
                'yAxes' => [[
                    'ticks' => [
                        'beginAtZero' => true,
                    ],
                ]],
            ],
            'title' => [
                'display' => true,
                'text' => 'PCN Cases Trend (Last 6 Months)',
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ]);
    }
}
