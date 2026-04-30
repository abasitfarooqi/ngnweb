<?php

namespace App\Livewire\FluxAdmin\Pages\Pcn;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\PcnCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('PCN statistics — Flux Admin')]
class PcnDashboard extends Component
{
    use WithAuthorization;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-pcn-portal');
    }

    public function sendReminder(int $id): void
    {
        $pcn = PcnCase::findOrFail($id);
        $pcn->is_whatsapp_sent = true;
        $pcn->whatsapp_last_reminder_sent_at = now();
        $pcn->save();

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Reminder recorded.');
    }

    public function render()
    {
        $totalCases = PcnCase::count();
        $openCases = PcnCase::where('isClosed', false)->count();
        $closedCases = PcnCase::whereHas('updates', fn ($q) => $q->where('is_cancled', false))->where('isClosed', true)->count();
        $cancelledCases = PcnCase::whereHas('updates', fn ($q) => $q->where('is_cancled', true))->count();
        $appealedCases = PcnCase::where('isClosed', false)->whereHas('updates', fn ($q) => $q->where('is_appealed', true))->count();

        $totalFullAmount = PcnCase::where('isClosed', false)->sum('full_amount');
        $totalReducedAmount = PcnCase::where('isClosed', false)->sum('reduced_amount');

        $policeStats = [
            'police' => PcnCase::where('is_police', true)->count(),
            'regular' => PcnCase::where('is_police', false)->count(),
        ];

        $outstandingAmounts = [
            'police' => PcnCase::where('is_police', true)->where('isClosed', false)->sum('full_amount'),
            'regular' => PcnCase::where('is_police', false)->where('isClosed', false)->sum('full_amount'),
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
            ->where('isClosed', false)
            ->whereNotNull('motorbike_id')
            ->groupBy('motorbike_id', 'customer_id')
            ->orderByDesc('pcn_count')
            ->limit(20)
            ->with(['motorbike:id,reg_no', 'customer:id,first_name,last_name'])
            ->get();

        $pcnList = PcnCase::with(['customer:id,first_name,last_name,phone,whatsapp', 'motorbike:id,reg_no'])
            ->where('isClosed', false)
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(function ($pcn) {
                $customerName = $pcn->customer ? trim($pcn->customer->first_name.' '.$pcn->customer->last_name) : 'N/A';
                $phone = $pcn->customer ? ($pcn->customer->whatsapp ?? $pcn->customer->phone ?? '') : '';
                $phone = preg_replace('/\s+|^0/', '', (string) $phone);
                $phone = preg_replace('/^(\+44)+/', '', $phone);
                $phone = preg_replace('/^44/', '', $phone);
                $phone = $phone !== '' ? '+44'.$phone : '';
                $message = "Dear {$customerName}, this is a reminder regarding Penalty Charge Notice {$pcn->pcn_number} for vehicle ".($pcn->motorbike?->reg_no ?? 'N/A').". The outstanding amount of £{$pcn->reduced_amount} remains unpaid.";

                return (object) [
                    'id' => $pcn->id,
                    'pcn_number' => $pcn->pcn_number,
                    'customer_name' => $customerName,
                    'reg_no' => $pcn->motorbike?->reg_no ?? 'N/A',
                    'amount' => $pcn->reduced_amount,
                    'is_whatsapp_sent' => $pcn->is_whatsapp_sent,
                    'whatsapp_last_reminder_sent_at' => $pcn->whatsapp_last_reminder_sent_at
                        ? \Carbon\Carbon::parse($pcn->whatsapp_last_reminder_sent_at)->format('d/m/Y H:i') : 'N/A',
                    'whatsapp_url' => $phone !== '' ? 'https://wa.me/'.$phone.'?text='.urlencode($message) : '#',
                ];
            });

        return view('flux-admin.pages.pcn.dashboard', compact(
            'totalCases', 'openCases', 'closedCases', 'cancelledCases', 'appealedCases',
            'totalFullAmount', 'totalReducedAmount', 'policeStats', 'outstandingAmounts',
            'monthlyStats', 'topVehicles', 'pcnList'
        ));
    }
}
