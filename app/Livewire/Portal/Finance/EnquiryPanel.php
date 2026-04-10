<?php

namespace App\Livewire\Portal\Finance;

use App\Http\Controllers\MailController;
use App\Models\Motorbike;
use App\Models\Motorcycle;
use App\Models\ServiceBooking as ServiceBookingModel;
use App\Models\SupportConversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class EnquiryPanel extends Component
{
    public const CONTRACT_SALE = 'sale_instalments';

    public const CONTRACT_SUBSCRIPTION = 'subscription_12m';

    /** @var array<string, string> */
    public const SUBSCRIPTION_GROUPS_TEXT = [
        'A' => 'Group A — £299.99/month',
        'B' => 'Group B — £399.99/month',
        'C' => 'Group C — £549.99/month',
        'D' => 'Group D — £649.99/month',
    ];

    /** @var numeric-string|int|float|string Bike price for illustration (untyped so Livewire hydrates reliably). */
    public $bikePrice = 5000;

    /** @var numeric-string|int|float|string */
    public $deposit = 500;

    /** @var int|string */
    public $termMonths = 12;

    /** @var string sale_instalments|subscription_12m */
    public $financePlan = self::CONTRACT_SALE;

    /** @var string A|B|C|D — subscription monthly fee group */
    public $subscriptionGroup = 'A';

    public $notes = '';

    public ?string $bikeSummaryLine = null;

    public ?int $contextNewId = null;

    public ?int $contextUsedId = null;

    public ?string $contextBikeKind = null;

    public ?string $contextRegHint = null;

    public function mount(): void
    {
        if (request()->filled('prefill_used')) {
            $id = (int) request()->integer('prefill_used');
            $bike = Motorbike::query()
                ->join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
                ->select('motorbikes.*', 'motorbikes_sale.price')
                ->where('motorbikes.id', $id)
                ->where('motorbikes_sale.is_sold', 0)
                ->first();
            if ($bike) {
                $this->contextUsedId = $bike->id;
                $this->contextBikeKind = 'used';
                $price = (float) ($bike->price ?? 0);
                if ($price > 0) {
                    $this->bikePrice = $price;
                    $this->deposit = round(min($price * 0.1, max(0, $price - 100)), 2);
                }
                $masked = $bike->reg_no ? '****'.substr((string) $bike->reg_no, -3) : null;
                $this->bikeSummaryLine = trim(implode(' ', array_filter([$bike->make, $bike->model, (string) $bike->year]))).($masked ? ' · Reg '.$masked : '');
                $this->contextRegHint = (string) ($bike->reg_no ?? '');
            }
        }

        if (request()->filled('prefill_new')) {
            $id = (int) request()->integer('prefill_new');
            $bike = Motorcycle::query()->where('availability', 'for sale')->whereKey($id)->first();
            if ($bike) {
                $this->contextNewId = $bike->id;
                $this->contextBikeKind = 'new';
                $price = (float) ($bike->sale_new_price ?? 0);
                if ($price > 0) {
                    $this->bikePrice = $price;
                    $this->deposit = round(min($price * 0.1, max(0, $price - 100)), 2);
                }
                $this->bikeSummaryLine = trim(implode(' ', array_filter([$bike->make, $bike->model, (string) $bike->year])));
            }
        }

        if (request()->filled('prefill_ebike')) {
            $id = (int) request()->integer('prefill_ebike');
            $bike = Motorbike::query()
                ->join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
                ->select('motorbikes.*', 'motorbikes_sale.price')
                ->where('motorbikes.id', $id)
                ->where('motorbikes.is_ebike', true)
                ->where('motorbikes_sale.is_sold', 0)
                ->first();
            if ($bike) {
                $this->contextUsedId = $bike->id;
                $this->contextBikeKind = 'ebike';
                $price = (float) ($bike->price ?? 0);
                if ($price > 0) {
                    $this->bikePrice = $price;
                    $this->deposit = round(min($price * 0.1, max(0, $price - 100)), 2);
                }
                $masked = $bike->reg_no ? '****'.substr((string) $bike->reg_no, -3) : null;
                $this->bikeSummaryLine = 'E-bike: '.trim(implode(' ', array_filter([$bike->make, $bike->model]))).($masked ? ' · Reg '.$masked : '');
                $this->contextRegHint = (string) ($bike->reg_no ?? '');
            }
        }

        if (request()->filled('prefill_price')) {
            $p = (float) request()->input('prefill_price');
            if ($p > 0) {
                $this->bikePrice = $p;
                $this->deposit = round(min($p * 0.1, max(0, $p - 100)), 2);
            }
        }

        $tm = (int) $this->termMonths;
        $this->termMonths = in_array($tm, [6, 12], true) ? $tm : 12;

        $g = is_string($this->subscriptionGroup) ? strtoupper($this->subscriptionGroup) : 'A';
        $this->subscriptionGroup = array_key_exists($g, self::SUBSCRIPTION_GROUPS_TEXT) ? $g : 'A';

        $this->clampDepositToBikePrice();
    }

    public function updated(string $field): void
    {
        if (in_array($field, ['bikePrice', 'deposit', 'termMonths'], true)) {
            $this->bikePrice = max(0, (float) $this->bikePrice);
            $this->deposit = max(0, (float) $this->deposit);
            $this->clampDepositToBikePrice();
            $this->termMonths = (int) $this->termMonths;
            if (! in_array($this->termMonths, [6, 12], true)) {
                $this->termMonths = 12;
            }
        }
        if ($field === 'subscriptionGroup') {
            $g = is_string($this->subscriptionGroup) ? strtoupper($this->subscriptionGroup) : 'A';
            $this->subscriptionGroup = array_key_exists($g, self::SUBSCRIPTION_GROUPS_TEXT) ? $g : 'A';
        }
    }

    private function clampDepositToBikePrice(): void
    {
        $price = max(0, (float) $this->bikePrice);
        $dep = max(0, (float) $this->deposit);
        if ($dep > $price) {
            $this->deposit = $price;
        }
    }

    public function indicativeMonthly(): ?float
    {
        if ($this->financePlan !== self::CONTRACT_SALE) {
            return null;
        }
        $principal = max(0, $this->bikePrice - $this->deposit);
        $months = max(1, (int) $this->termMonths);
        if ($principal <= 0) {
            return null;
        }

        return round($principal / $months, 2);
    }

    public function submitEnquiry(): void
    {
        if ($this->contextNewId === null && $this->contextUsedId === null) {
            $this->addError(
                'bikeFromListing',
                'Choose a bike from the listings on this page — tap Enquire on finance on a new, used, or e-bike card — then complete this form.'
            );

            return;
        }

        $rules = [
            'bikePrice' => ['required', 'numeric', 'min:500', 'max:100000'],
            'deposit' => ['required', 'numeric', 'min:0', 'lte:bikePrice'],
            'termMonths' => ['required', 'integer', 'in:6,12'],
            'financePlan' => ['required', 'in:'.self::CONTRACT_SALE.','.self::CONTRACT_SUBSCRIPTION],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
        if ($this->financePlan === self::CONTRACT_SUBSCRIPTION) {
            $rules['subscriptionGroup'] = ['required', 'string', Rule::in(array_keys(self::SUBSCRIPTION_GROUPS_TEXT))];
        }
        $this->validate($rules);

        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            abort(403);
        }

        $profile = $customerAuth->customer;
        $resolvedName = trim((string) (($profile?->first_name ?? '').' '.($profile?->last_name ?? '')));
        $resolvedName = $resolvedName !== '' ? $resolvedName : 'Portal customer';
        $resolvedEmail = trim((string) ($customerAuth->email ?? ''));
        $resolvedPhone = trim((string) ($profile?->phone ?? ''));

        $term = (int) $this->termMonths;
        $contractLabel = $this->financePlan === self::CONTRACT_SUBSCRIPTION
            ? '12-month subscription (merged sale + subscription terms — staff prepare contract)'
            : $term.'-month instalment sale agreement (staff prepare contract)';

        $serviceTypeLabel = 'Motorcycle Finance Enquiry';

        $stockLine = match ($this->contextBikeKind) {
            'new' => 'Stock: new motorcycle',
            'used' => 'Stock: used motorcycle',
            'ebike' => 'Stock: e-bike',
            default => null,
        };

        $description = implode(' | ', array_filter([
            'Source: portal.finance.browse',
            $stockLine,
            $this->bikeSummaryLine ? 'Bike: '.$this->bikeSummaryLine : null,
            $this->contextNewId ? 'New stock ID: '.$this->contextNewId : null,
            $this->contextUsedId && $this->contextBikeKind === 'used' ? 'Used motorbike ID: '.$this->contextUsedId : null,
            $this->contextUsedId && $this->contextBikeKind === 'ebike' ? 'E-bike motorbike ID: '.$this->contextUsedId : null,
            'Preferred structure: '.$contractLabel,
            'Illustrative bike price GBP: '.number_format((float) $this->bikePrice, 2, '.', ''),
            'Illustrative deposit GBP: '.number_format((float) $this->deposit, 2, '.', ''),
            'Term (months): '.$this->termMonths,
            $this->financePlan === self::CONTRACT_SALE && ($m = $this->indicativeMonthly()) !== null
                ? 'Indicative monthly (balance ÷ '.(int) $this->termMonths.'): £'.number_format($m, 2, '.', '')
                : null,
            $this->financePlan === self::CONTRACT_SUBSCRIPTION
                ? 'Preferred subscription group: '.(self::SUBSCRIPTION_GROUPS_TEXT[$this->subscriptionGroup] ?? 'Group '.$this->subscriptionGroup)
                : null,
            $this->notes !== '' ? 'Customer notes: '.$this->notes : null,
        ]));

        $conversation = SupportConversation::query()->create([
            'customer_auth_id' => $customerAuth->id,
            'title' => $serviceTypeLabel,
            'topic' => $serviceTypeLabel,
            'status' => 'open',
        ]);

        $regForRow = 'Finance enquiry';
        if ($this->contextRegHint !== null && $this->contextRegHint !== '') {
            $regForRow = strtoupper($this->contextRegHint);
        } elseif ($this->contextUsedId) {
            $regForRow = 'Finance bike #'.$this->contextUsedId;
        } elseif ($this->contextNewId) {
            $regForRow = 'Finance new #'.$this->contextNewId;
        }

        $booking = ServiceBookingModel::query()->create([
            'customer_id' => $customerAuth->customer_id,
            'customer_auth_id' => $customerAuth->id,
            'conversation_id' => $conversation->id,
            'submission_context' => 'authenticated_customer',
            'enquiry_type' => ServiceBookingModel::inferEnquiryType($serviceTypeLabel, $description),
            'service_type' => $serviceTypeLabel,
            'subject' => $this->bikeSummaryLine
                ? 'Finance enquiry — '.$this->bikeSummaryLine
                : $serviceTypeLabel,
            'description' => $description,
            'requires_schedule' => false,
            'booking_date' => null,
            'booking_time' => null,
            'status' => 'Pending',
            'fullname' => $resolvedName,
            'phone' => $resolvedPhone,
            'reg_no' => $regForRow,
            'email' => $resolvedEmail !== '' ? $resolvedEmail : null,
        ]);

        $conversation->forceFill(['service_booking_id' => $booking->id])->save();

        app(MailController::class)->sendBookingConfirmation($booking);

        session()->flash('success', 'Finance enquiry sent. Our team will prepare options and contact you — contracts are not created from this page.');

        $this->reset('notes');
        $this->dispatch('finance-enquiry-sent');
    }

    public function render()
    {
        return view('livewire.portal.finance.enquiry-panel');
    }
}
