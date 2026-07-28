<?php

namespace App\Support;

use App\Models\PcnCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class PcnDashboardData
{
    /** @return array<string, mixed> */
    public static function build(string $listSort = 'desc'): array
    {
        if (! in_array($listSort, ['asc', 'desc'], true)) {
            $listSort = 'desc';
        }

        $totalCases = PcnCase::count();
        $openCases = PcnCase::openCount();
        $closedCases = PcnCase::closed()->whereHas('updates', fn ($q) => $q->where('is_cancled', false))->count();
        $cancelledCases = PcnCase::whereHas('updates', fn ($q) => $q->where('is_cancled', true))->count();
        $appealedCases = PcnCase::open()->whereHas('updates', fn ($q) => $q->where('is_appealed', true))->count();

        $appealedStats = [
            'police' => PcnCase::where('is_police', true)->open()->whereHas('updates', fn ($q) => $q->where('is_appealed', true))->count(),
            'regular' => PcnCase::where('is_police', false)->open()->whereHas('updates', fn ($q) => $q->where('is_appealed', true))->count(),
        ];

        $cancelledStats = [
            'police' => PcnCase::where('is_police', true)->whereHas('updates', fn ($q) => $q->where('is_cancled', true))->count(),
            'regular' => PcnCase::where('is_police', false)->whereHas('updates', fn ($q) => $q->where('is_cancled', true))->count(),
        ];

        $totalFullAmount = PcnCase::open()->sum('full_amount');
        $totalReducedAmount = PcnCase::open()->sum('reduced_amount');

        $policeStats = [
            'police' => PcnCase::where('is_police', true)->count(),
            'regular' => PcnCase::where('is_police', false)->count(),
        ];

        $outstandingAmounts = [
            'police' => PcnCase::where('is_police', true)->open()->sum('full_amount'),
            'regular' => PcnCase::where('is_police', false)->open()->sum('full_amount'),
        ];

        $monthlyStats = PcnCase::select(
            DB::raw('DATE_FORMAT(date_of_contravention, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN isClosed = 1 THEN 1 ELSE 0 END) as closed'),
            DB::raw('SUM(CASE WHEN isClosed = 0 THEN 1 ELSE 0 END) as open')
        )->where('date_of_contravention', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')->orderBy('month')->get();

        $topVehicles = PcnCase::select('motorbike_id', 'customer_id')
            ->selectRaw('COUNT(*) as pcn_count')
            ->open()
            ->whereNotNull('motorbike_id')
            ->groupBy('motorbike_id', 'customer_id')
            ->orderByDesc('pcn_count')
            ->limit(20)
            ->with(['motorbike:id,reg_no', 'customer:id,first_name,last_name'])
            ->get();

        $pcnList = PcnCase::with(['customer:id,first_name,last_name,phone,whatsapp', 'motorbike:id,reg_no'])
            ->open()
            ->orderBy('created_at', $listSort)
            ->get()
            ->map(function ($pcn) {
                $customerName = $pcn->customer ? trim($pcn->customer->first_name.' '.$pcn->customer->last_name) : 'N/A';
                $phone = $pcn->customer ? ($pcn->customer->whatsapp ?: $pcn->customer->phone ?: '') : '';
                $phone = preg_replace('/\s+|^0/', '', (string) $phone);
                $phone = preg_replace('/^(\+44)+/', '', $phone);
                $phone = preg_replace('/^44/', '', $phone);
                $phone = $phone !== '' ? '+44'.$phone : '';
                $message = "Dear {$customerName}, this is a reminder regarding Penalty Charge Notice {$pcn->pcn_number} for vehicle ".($pcn->motorbike?->reg_no ?? 'N/A').". The outstanding amount of £{$pcn->reduced_amount} remains unpaid. Please make payment promptly to avoid further increases. If you have already paid, contact us on 0208 314 1498 or WhatsApp 07951790568. NGN Motors.";

                return (object) [
                    'id' => $pcn->id,
                    'pcn_number' => $pcn->pcn_number,
                    'customer_name' => $customerName,
                    'reg_no' => $pcn->motorbike?->reg_no ?? 'N/A',
                    'amount' => $pcn->reduced_amount,
                    'is_whatsapp_sent' => $pcn->is_whatsapp_sent,
                    'whatsapp_last_reminder_sent_at' => $pcn->whatsapp_last_reminder_sent_at
                        ? Carbon::parse($pcn->whatsapp_last_reminder_sent_at)->format('d/m/Y H:i') : 'N/A',
                    'whatsapp_url' => $phone !== '' ? 'https://wa.me/'.$phone.'?text='.urlencode($message) : '#',
                ];
            });

        return compact(
            'totalCases', 'openCases', 'closedCases', 'cancelledCases', 'appealedCases',
            'appealedStats', 'cancelledStats',
            'totalFullAmount', 'totalReducedAmount', 'policeStats', 'outstandingAmounts',
            'monthlyStats', 'topVehicles', 'pcnList'
        );
    }
}
