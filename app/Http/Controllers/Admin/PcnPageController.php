<?php

namespace App\Http\Controllers\Admin;

use App\Models\PcnCase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

/**
 * Class PcnPageController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PcnPageController extends Controller
{
    public function index(Request $request)
    {
        // Get sorting parameters
        $sortField = $request->get('sort_field', 'created_at'); // Default sort by created_at
        $sortOrder = $request->get('sort_order', 'desc'); // Default to descending

        // Validate sorting parameters
        if (! in_array($sortField, ['created_at', 'date_of_contravention'])) {
            $sortField = 'created_at';
        }
        if (! in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        // Get total PCN cases
        $totalCases = PcnCase::count();

        // Get cases by status including cancelled
        $openCases = PcnCase::where('isClosed', false)->count();
        $closedCases = PcnCase::whereHas('updates', function ($query) {
            $query->where('is_cancled', false);
        })->where('isClosed', true)->count();
        $cancelledCases = PcnCase::whereHas('updates', function ($query) {
            $query->where('is_cancled', true);
        })->count();

        // Get appealed cases statistics (only for open cases)
        $appealedCases = PcnCase::where('isClosed', false)
            ->whereHas('updates', function ($query) {
                $query->where('is_appealed', true);
            })->count();

        // Get appealed cases by type (police vs regular) - only for open cases
        $appealedStats = [
            'police' => PcnCase::where('is_police', true)
                ->where('isClosed', false)
                ->whereHas('updates', function ($query) {
                    $query->where('is_appealed', true);
                })->count(),
            'regular' => PcnCase::where('is_police', false)
                ->where('isClosed', false)
                ->whereHas('updates', function ($query) {
                    $query->where('is_appealed', true);
                })->count(),
        ];

        // Get monthly statistics for the last 12 months
        $monthlyStats = PcnCase::select(
            DB::raw('DATE_FORMAT(date_of_contravention, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN isClosed = 1 THEN 1 ELSE 0 END) as closed'),
            DB::raw('SUM(CASE WHEN isClosed = 0 THEN 1 ELSE 0 END) as open')
        )
            ->where('date_of_contravention', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Calculate total amounts for unpaid/open cases only
        $totalFullAmount = PcnCase::where('isClosed', false)->sum('full_amount');
        $totalReducedAmount = PcnCase::where('isClosed', false)->sum('reduced_amount');

        // Get police vs regular PCN stats
        $policeStats = [
            'police' => PcnCase::where('is_police', true)->count(),
            'regular' => PcnCase::where('is_police', false)->count(),
        ];

        // Get outstanding amounts by case type (police vs regular) for open cases
        $outstandingAmounts = [
            'police' => PcnCase::where('is_police', true)
                ->where('isClosed', false)
                ->sum('full_amount'),
            'regular' => PcnCase::where('is_police', false)
                ->where('isClosed', false)
                ->sum('full_amount'),
        ];

        // Get cancelled cases statistics
        $cancelledStats = [
            'police' => PcnCase::where('is_police', true)
                ->whereHas('updates', function ($query) {
                    $query->where('is_cancled', true);
                })->count(),
            'regular' => PcnCase::where('is_police', false)
                ->whereHas('updates', function ($query) {
                    $query->where('is_cancled', true);
                })->count(),
        ];

        // Get top 10 vehicles with most open PCNs
        $topVehicles = PcnCase::select('motorbike_id', 'customer_id')
            ->selectRaw('COUNT(*) as pcn_count')
            ->where('isClosed', false)
            ->whereNotNull('motorbike_id')
            ->groupBy('motorbike_id', 'customer_id')
            ->orderByDesc('pcn_count')
            ->limit(20)
            ->with(['motorbike' => function ($query) {
                $query->select('id', 'reg_no');
            }, 'customer' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            }])
            ->get();

        // Get PCN list for the table
        $pcnList = PcnCase::with(['customer', 'motorbike'])
            ->where('isClosed', false)
            ->orderBy('created_at', $sortOrder)
            ->get()
            ->map(function ($pcnCase) {
                $customerName = $pcnCase->customer ? $pcnCase->customer->first_name.' '.$pcnCase->customer->last_name : 'N/A';
                $regNo = $pcnCase->motorbike ? $pcnCase->motorbike->reg_no : 'N/A';

                // first check for whatsapp number (customer may be null)
                $phoneNumber = $pcnCase->customer
                    ? ($pcnCase->customer->whatsapp ?? $pcnCase->customer->phone ?? '')
                    : '';

                $phoneNumber = preg_replace('/\s+|^0/', '', (string) $phoneNumber);
                $phoneNumber = preg_replace('/^(\+44)+/', '', $phoneNumber);
                $phoneNumber = preg_replace('/^44/', '', $phoneNumber);
                $phoneNumber = $phoneNumber !== '' ? '+44'.$phoneNumber : '';
                $phoneNumber = preg_replace('/\s+/', '', $phoneNumber);

                $fullAmount = $pcnCase->full_amount;
                $reducedAmount = $pcnCase->reduced_amount;

                $amountDue = $reducedAmount;

                $message = "Dear {$customerName}, this is a reminder regarding Penalty Charge Notice {$pcnCase->pcn_number} for vehicle {$regNo}. The outstanding amount of £{$amountDue} remains unpaid. Please ensure payment is made as soon as possible to avoid increases. If you have already paid, please contact us immediately at 0208 314 1498 or WhatsApp us on 07951790568, NGN Motors.";
                $url = $phoneNumber !== '' ? "https://wa.me/{$phoneNumber}?text=".urlencode($message) : '#';

                return [
                    'customer_name' => $customerName,
                    'reg_no' => $regNo,
                    'id' => $pcnCase->id,
                    'pcn_number' => $pcnCase->pcn_number,
                    'amount' => $amountDue,
                    'is_whatsapp_sent' => $pcnCase->is_whatsapp_sent,
                    'whatsapp_last_reminder_sent_at' => $pcnCase->whatsapp_last_reminder_sent_at ? \Carbon\Carbon::parse($pcnCase->whatsapp_last_reminder_sent_at)->format('d/m/Y H:i') : 'N/A',
                    'whatsapp_url' => $url,
                ];
            });

        // ✅ If this is an AJAX request, return only the table body HTML
        if ($request->ajax()) {
            $html = view('livewire.agreements.migrated.admin.partials.pcn_list_body', [
                'pcnList' => $pcnList,
            ])->render();

            return response()->json(['html' => $html]);
        }

        return view('livewire.agreements.migrated.admin.pcn_page', [
            'title' => 'PCN Statistics',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'PCN Statistics' => false,
            ],
            'totalCases' => $totalCases,
            'openCases' => $openCases,
            'closedCases' => $closedCases,
            'cancelledCases' => $cancelledCases,
            'appealedCases' => $appealedCases,
            'appealedStats' => $appealedStats,
            'monthlyStats' => $monthlyStats,
            'totalFullAmount' => $totalFullAmount,
            'totalReducedAmount' => $totalReducedAmount,
            'policeStats' => $policeStats,
            'outstandingAmounts' => $outstandingAmounts,
            'cancelledStats' => $cancelledStats,
            'topVehicles' => $topVehicles,
            'pcnList' => $pcnList,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function sendReminder($id)
    {
        $pcnCase = PcnCase::findOrFail($id);

        // Update the PCN case
        $pcnCase->is_whatsapp_sent = true;
        $pcnCase->whatsapp_last_reminder_sent_at = now();
        $pcnCase->save();

        return redirect()->back()->with('success', 'WhatsApp reminder sent successfully.');
    }
}
