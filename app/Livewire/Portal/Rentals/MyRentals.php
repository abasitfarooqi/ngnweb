<?php

namespace App\Livewire\Portal\Rentals;

use App\Models\BookingClosing;
use App\Models\BookingInvoice;
use App\Models\BookingIssuanceItem;
use App\Models\CustomerAgreement;
use App\Models\MotorbikeMaintenanceLog;
use App\Models\MotorbikeRepair;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MyRentals extends Component
{
    public bool $showPaymentHistory = false;

    public bool $showExtendModal = false;

    public bool $showReturnModal = false;

    public ?int $selectedBooking = null;

    public int $extendWeeks = 4;

    public string $returnNotice = '';

    public function showPayments(int $bookingId): void
    {
        $this->selectedBooking = $bookingId;
        $this->showPaymentHistory = true;
    }

    public function closePayments(): void
    {
        $this->showPaymentHistory = false;
        $this->selectedBooking = null;
    }

    public function openExtendModal(int $bookingId): void
    {
        $this->selectedBooking = $bookingId;
        $this->showExtendModal = true;
    }

    public function closeExtendModal(): void
    {
        $this->showExtendModal = false;
        $this->selectedBooking = null;
    }

    public function openReturnModal(int $bookingId): void
    {
        $this->selectedBooking = $bookingId;
        $this->showReturnModal = true;
    }

    public function closeReturnModal(): void
    {
        $this->showReturnModal = false;
        $this->selectedBooking = null;
    }

    public function extendRental(): void
    {
        $this->validate(['extendWeeks' => 'required|integer|min:1|max:52']);
        session()->flash('success', 'Extension request submitted for Booking #'.$this->selectedBooking.'. We will confirm shortly.');
        $this->closeExtendModal();
    }

    public function submitReturnNotice(): void
    {
        $this->validate(['returnNotice' => 'required|string|min:10']);
        session()->flash('success', 'Return notice submitted for Booking #'.$this->selectedBooking.'. We will be in touch.');
        $this->closeReturnModal();
    }

    public function downloadInvoice(int $invoiceId)
    {
        $customer = Auth::guard('customer')->user()?->customer;
        if (! $customer) {
            abort(403);
        }

        $invoice = BookingInvoice::query()
            ->with(['booking.rentingBookingItems.motorbike'])
            ->where('id', $invoiceId)
            ->whereHas('booking', fn ($q) => $q->where('customer_id', $customer->id))
            ->firstOrFail();

        $pdf = \PDF::loadView('portal.pdf.rental-invoice', [
            'invoice' => $invoice,
            'booking' => $invoice->booking,
            'customer' => $customer,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'rental-invoice-'.$invoice->id.'.pdf'
        );
    }

    public function downloadRepairReport(int $repairId)
    {
        $customerAuth = Auth::guard('customer')->user();
        $customerEmail = trim((string) ($customerAuth?->email ?? ''));
        if ($customerEmail === '') {
            abort(403);
        }

        $repair = MotorbikeRepair::query()
            ->with(['motorbike', 'branch', 'updates.services', 'observations'])
            ->where('id', $repairId)
            ->where(function ($q) use ($customerEmail): void {
                $q->whereRaw('LOWER(email) = ?', [strtolower($customerEmail)]);
            })
            ->firstOrFail();

        $pdf = \PDF::loadView('livewire.agreements.pdf.templates.repair_invoice', compact('repair'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        $regNo = $repair->motorbike?->reg_no ?: 'report';

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'repair-report-'.$regNo.'-'.$repair->id.'.pdf'
        );
    }

    public function render()
    {
        $customerAuth = Auth::guard('customer')->user();
        $profile = $customerAuth?->customer;
        $bookings = $profile
            ? $profile->rentingBookings()
                ->with(['rentingBookingItems.motorbike'])
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        $invoiceRowsByBooking = collect();
        $invoiceDisplayByBooking = collect();
        $invoicePortalMetaByBooking = collect();
        $invoiceBalancesByBooking = collect();
        $agreementsByBooking = collect();
        $otherChargesByBooking = collect();
        $closingByBooking = collect();
        $issuanceByBooking = collect();
        $maintenanceByBooking = collect();
        $serviceVideosByBooking = collect();

        if ($bookings->isNotEmpty() && $profile) {
            $bookingIds = $bookings->pluck('id')->all();

            $invoiceRowsByBooking = BookingInvoice::query()
                ->whereIn('booking_id', $bookingIds)
                ->where('is_posted', true)
                ->where('amount', '>', 0)
                ->with([
                    'transactions' => fn ($q) => $q->orderByDesc('created_at')->with([
                        'user:id,first_name,last_name',
                        'paymentMethod:id,title',
                    ]),
                ])
                ->orderBy('invoice_date')
                ->orderBy('id')
                ->get()
                ->groupBy('booking_id');

            // Monday–Sunday week (UK-style): show invoices through this Sunday only while rental is active.
            $currentWeekEndDay = Carbon::now()->startOfWeek(Carbon::MONDAY)->addDays(6)->startOfDay();

            foreach ($bookings as $booking) {
                $bid = $booking->id;
                $items = $booking->rentingBookingItems;
                $isEnded = $items->isNotEmpty() && $items->every(fn ($i) => ! empty($i->end_date));
                $rentalEndDay = null;
                if ($isEnded) {
                    $rentalEndDay = $items
                        ->map(fn ($i) => $i->end_date ? Carbon::parse($i->end_date)->startOfDay() : null)
                        ->filter()
                        ->max();
                }

                $all = $invoiceRowsByBooking->get($bid, collect());

                $visible = $all->filter(function (BookingInvoice $inv) use ($isEnded, $currentWeekEndDay): bool {
                    if ($isEnded) {
                        return true;
                    }
                    if (! $inv->invoice_date) {
                        return true;
                    }

                    return ! $inv->invoice_date->startOfDay()->gt($currentWeekEndDay);
                });

                $meta = [];
                foreach ($visible as $inv) {
                    $postRental = $isEnded && $rentalEndDay && $inv->invoice_date
                        && $inv->invoice_date->startOfDay()->gt($rentalEndDay);
                    $paidFromTxns = (float) $inv->transactions->sum('amount');
                    $paidDisplay = $inv->is_paid ? (float) $inv->amount : $paidFromTxns;
                    $rawLeft = max(0.0, (float) $inv->amount - $paidDisplay);
                    $meta[$inv->id] = [
                        'post_rental' => $postRental,
                        'display_left_to_pay' => $postRental ? 0.0 : $rawLeft,
                    ];
                }

                $unpaidForBal = $visible->filter(function (BookingInvoice $inv) use ($meta): bool {
                    if ($meta[$inv->id]['post_rental'] ?? false) {
                        return false;
                    }

                    return ! $inv->is_paid;
                });

                $unpaidTotal = (float) $unpaidForBal->sum(function (BookingInvoice $i): float {
                    $paid = (float) $i->transactions->sum('amount');

                    return max(0.0, (float) $i->amount - $paid);
                });

                $next = $unpaidForBal->sortBy(fn (BookingInvoice $i) => $i->invoice_date?->timestamp ?? PHP_INT_MAX)->first();

                $invoiceDisplayByBooking->put($bid, $visible->values());
                $invoicePortalMetaByBooking->put($bid, $meta);
                $invoiceBalancesByBooking->put($bid, [
                    'unpaid_total' => $unpaidTotal,
                    'unpaid_count' => $unpaidForBal->count(),
                    'next_due' => $next?->invoice_date,
                ]);
            }

            $agreementsByBooking = CustomerAgreement::query()
                ->where('customer_id', $profile->id)
                ->whereIn('booking_id', $bookingIds)
                ->orderByDesc('id')
                ->get()
                ->groupBy('booking_id');

            $otherChargesByBooking = collect(
                DB::table('renting_other_charges')
                    ->whereIn('booking_id', $bookingIds)
                    ->orderBy('id')
                    ->get()
            )->groupBy('booking_id');

            $closingByBooking = BookingClosing::query()
                ->whereIn('booking_id', $bookingIds)
                ->get()
                ->keyBy('booking_id');

            $issuanceByBooking = collect();
            $issuanceItemIds = $bookings->flatMap(fn ($b) => $b->rentingBookingItems->pluck('id'))->filter()->unique()->values()->all();
            if ($issuanceItemIds !== []) {
                $issuanceRows = BookingIssuanceItem::query()
                    ->whereIn('booking_item_id', $issuanceItemIds)
                    ->with(['issuedBy:id,first_name,last_name', 'bookingItem.motorbike:id,reg_no'])
                    ->orderByDesc('created_at')
                    ->get();
                foreach ($issuanceRows as $iss) {
                    $bid = $iss->bookingItem?->booking_id;
                    if (! $bid) {
                        continue;
                    }
                    if (! $issuanceByBooking->has($bid)) {
                        $issuanceByBooking->put($bid, collect());
                    }
                    $issuanceByBooking->get($bid)->push($iss);
                }
            }

            $maintenanceByBooking = MotorbikeMaintenanceLog::query()
                ->whereIn('booking_id', $bookingIds)
                ->with(['user:id,first_name,last_name', 'motorbike:id,reg_no'])
                ->orderByDesc('serviced_at')
                ->get()
                ->groupBy('booking_id');

            // Portal: service workshop videos are not shown to customers (query omitted).
            $serviceVideosByBooking = collect();
        }

        $pcnByBooking = collect();
        $pcnDetailsByBooking = collect();
        if ($bookings->isNotEmpty()) {
            $bookingItemIds = $bookings
                ->flatMap(fn ($booking) => $booking->rentingBookingItems->pluck('id'))
                ->filter()
                ->values()
                ->all();

            if (! empty($bookingItemIds)) {
                $pcnUpdateSummary = DB::table('pcn_case_updates as pcu')
                    ->selectRaw('
                        pcu.case_id,
                        MAX(CASE WHEN pcu.is_appealed = 1 THEN 1 ELSE 0 END) as is_appealed,
                        MAX(CASE WHEN pcu.is_transferred = 1 THEN 1 ELSE 0 END) as is_transferred,
                        MAX(CASE WHEN pcu.is_cancled = 1 THEN 1 ELSE 0 END) as is_cancelled,
                        MAX(CASE WHEN pcu.is_paid_by_keeper = 1 THEN 1 ELSE 0 END) as is_paid_by_keeper,
                        MAX(CASE WHEN pcu.is_paid_by_owner = 1 THEN 1 ELSE 0 END) as is_paid_by_owner,
                        MAX(pcu.update_date) as last_update_at
                    ')
                    ->groupBy('pcu.case_id');

                $pcnRows = DB::table('renting_booking_items as rbi')
                    ->join('renting_bookings as rb', 'rb.id', '=', 'rbi.booking_id')
                    ->join('motorbikes as m', 'm.id', '=', 'rbi.motorbike_id')
                    ->leftJoin('pcn_cases as pc', function ($join): void {
                        $join->on('pc.motorbike_id', '=', 'rbi.motorbike_id')
                            ->whereRaw('DATE(pc.date_of_contravention) >= DATE(COALESCE(rbi.start_date, rb.start_date))')
                            ->whereRaw('DATE(pc.date_of_contravention) <= DATE(COALESCE(rbi.end_date, CURDATE()))');
                    })
                    ->leftJoinSub($pcnUpdateSummary, 'pcu_summary', function ($join): void {
                        $join->on('pcu_summary.case_id', '=', 'pc.id');
                    })
                    ->whereIn('rbi.id', $bookingItemIds)
                    ->whereNotNull('pc.id')
                    ->selectRaw(
                        '
                        rbi.booking_id as booking_id,
                        rbi.id as booking_item_id,
                        m.reg_no as reg_no,
                        pc.id as pcn_case_id,
                        pc.pcn_number as pcn_number,
                        pc.date_of_contravention as date_of_contravention,
                        pc.time_of_contravention as time_of_contravention,
                        pc.full_amount as full_amount,
                        pc.reduced_amount as reduced_amount,
                        pc.isClosed as is_closed,
                        pc.is_police as is_police,
                        COALESCE(pcu_summary.is_appealed, 0) as is_appealed,
                        COALESCE(pcu_summary.is_transferred, 0) as is_transferred,
                        COALESCE(pcu_summary.is_cancelled, 0) as is_cancelled,
                        COALESCE(pcu_summary.is_paid_by_keeper, 0) as is_paid_by_keeper,
                        COALESCE(pcu_summary.is_paid_by_owner, 0) as is_paid_by_owner,
                        pcu_summary.last_update_at as last_update_at
                    '
                    )
                    ->orderByDesc('pc.date_of_contravention')
                    ->orderByDesc('pc.id')
                    ->get();

                $pcnDetailsByBooking = $pcnRows
                    ->groupBy('booking_id')
                    ->map(function ($rows) {
                        return $rows->map(function ($row) {
                            $amount = (float) ($row->reduced_amount ?: $row->full_amount ?: 0);
                            $isClosed = (bool) ($row->is_closed ?? false);
                            $isCancelled = (bool) ($row->is_cancelled ?? false);
                            $isTransferred = (bool) ($row->is_transferred ?? false);
                            $isAppealed = (bool) ($row->is_appealed ?? false);
                            $paidByKeeper = (bool) ($row->is_paid_by_keeper ?? false);
                            $paidByOwner = (bool) ($row->is_paid_by_owner ?? false);

                            $statusLabel = $isCancelled
                                ? 'Cancelled'
                                : ($isClosed ? 'Closed' : 'Open');
                            $statusTone = $isCancelled
                                ? 'text-gray-500'
                                : ($isClosed ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400');

                            $paidByLabel = $isTransferred
                                ? 'Transferred'
                                : ($paidByKeeper ? 'Paid by hirer' : ($paidByOwner ? 'Paid by NGN' : ($isClosed ? 'Closed' : 'Unpaid')));

                            return (object) [
                                'pcn_case_id' => (int) $row->pcn_case_id,
                                'pcn_number' => (string) ($row->pcn_number ?: 'N/A'),
                                'reg_no' => (string) ($row->reg_no ?: 'N/A'),
                                'date_of_contravention' => $row->date_of_contravention,
                                'time_of_contravention' => $row->time_of_contravention,
                                'amount' => $amount,
                                'is_closed' => $isClosed,
                                'is_cancelled' => $isCancelled,
                                'is_appealed' => $isAppealed,
                                'is_transferred' => $isTransferred,
                                'is_paid_by_keeper' => $paidByKeeper,
                                'is_paid_by_owner' => $paidByOwner,
                                'status_label' => $statusLabel,
                                'status_tone' => $statusTone,
                                'paid_by_label' => $paidByLabel,
                                'last_update_at' => $row->last_update_at,
                            ];
                        })->values();
                    });

                $pcnByBooking = $pcnDetailsByBooking->map(function ($rows) {
                    $total = (float) $rows->sum('amount');
                    $paid = (float) $rows->filter(function ($row) {
                        return $row->is_closed || $row->is_paid_by_keeper || $row->is_paid_by_owner;
                    })->sum('amount');

                    return (object) [
                        'total' => $total,
                        'paid' => $paid,
                        'open_count' => (int) $rows->where('is_closed', false)->where('is_cancelled', false)->count(),
                        'appealed_count' => (int) $rows->where('is_appealed', true)->count(),
                        'total_count' => (int) $rows->count(),
                    ];
                });
            }
        }

        $repairReportsByBooking = collect();
        if ($bookings->isNotEmpty() && $customerAuth?->email) {
            $regNosByBooking = $bookings->mapWithKeys(function ($booking) {
                $regNos = $booking->rentingBookingItems
                    ->map(fn ($item) => strtoupper(trim((string) ($item->motorbike?->reg_no ?? ''))))
                    ->filter()
                    ->values()
                    ->all();

                return [$booking->id => $regNos];
            });

            $allRegNos = $regNosByBooking->flatten()->unique()->values();
            if ($allRegNos->isNotEmpty()) {
                $repairs = MotorbikeRepair::query()
                    ->with(['motorbike'])
                    ->whereRaw('LOWER(email) = ?', [strtolower($customerAuth->email)])
                    ->latest('id')
                    ->get();

                $repairReportsByBooking = $bookings->mapWithKeys(function ($booking) use ($repairs, $regNosByBooking) {
                    $regs = collect($regNosByBooking->get($booking->id, []));
                    $rows = $repairs->filter(function ($repair) use ($regs) {
                        $reg = strtoupper(trim((string) ($repair->motorbike?->reg_no ?? '')));

                        return $reg !== '' && $regs->contains($reg);
                    })->values();

                    return [$booking->id => $rows];
                });
            }
        }

        $paymentHistory = collect();
        if ($this->showPaymentHistory && $this->selectedBooking) {
            try {
                $paymentHistory = \App\Models\RentingTransaction::where('booking_id', $this->selectedBooking)
                    ->with(['transactionType', 'paymentMethod'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            } catch (\Exception $e) {
                $paymentHistory = collect();
            }
        }

        return view('livewire.portal.rentals.my-rentals', compact(
            'bookings',
            'paymentHistory',
            'pcnByBooking',
            'pcnDetailsByBooking',
            'repairReportsByBooking',
            'invoiceDisplayByBooking',
            'invoicePortalMetaByBooking',
            'invoiceBalancesByBooking',
            'agreementsByBooking',
            'otherChargesByBooking',
            'closingByBooking',
            'issuanceByBooking',
            'maintenanceByBooking',
            'serviceVideosByBooking',
        ))
            ->layout('components.layouts.portal', ['title' => 'My Rentals | My Account']);
    }
}
