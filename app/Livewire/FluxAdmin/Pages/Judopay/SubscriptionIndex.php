<?php

namespace App\Livewire\FluxAdmin\Pages\Judopay;

use App\Helpers\JudopayAuthorizationHelper;
use App\Helpers\JudopaySmsHelper;
use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\JudopayCitAccess;
use App\Models\JudopayCitPaymentSession;
use App\Models\JudopaySubscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Judopay subscriptions — Flux Admin')]
class SubscriptionIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public bool $showBillingForm = false;

    public int $billingSubscriptionId = 0;

    public string $billingFrequency = 'weekly';

    public ?int $billingDay = null;

    public bool $showAmountForm = false;

    public int $amountSubscriptionId = 0;

    public string $newAmount = '';

    public bool $showAuthForm = false;

    public int $authSubscriptionId = 0;

    public int $authCustomerId = 0;

    public string $authCustomerEmail = '';

    public string $authCustomerName = '';

    public string $authExpiresInHours = '24';

    public string $generatedAuthLink = '';

    public string $filterSubscribableType = '';

    public string $filterCustomerName = '';

    public function updatingFilterSubscribableType(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCustomerName(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'judopay-subscriptions';
        $this->sortField = 'date';
    }

    protected function formModel(): string { return JudopaySubscription::class; }

    protected function formRules(): array
    {
        return [
            'formData.judopay_onboarding_id' => ['nullable', 'integer'],
            'formData.billing_frequency'      => ['required', 'in:weekly,monthly,annually'],
            'formData.billing_day'            => ['nullable', 'integer'],
            'formData.amount'                 => ['nullable', 'numeric'],
            'formData.start_date'             => ['nullable', 'date'],
            'formData.end_date'               => ['nullable', 'date'],
            'formData.status'                 => ['required', 'in:active,paused,completed,cancelled,inactive'],
            'formData.consumer_reference'     => ['nullable', 'string', 'max:255'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['status' => 'active', 'billing_frequency' => 'weekly'];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(JudopaySubscription::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Subscription saved.');
    }

    public function closeSubscription(int $id): void
    {
        $subscription = JudopaySubscription::findOrFail($id);

        if (in_array($subscription->status, ['completed', 'cancelled', 'inactive'])) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Subscription is already closed or cancelled.');

            return;
        }

        $subscription->status = 'completed';
        $subscription->save();

        Log::channel('judopay')->info('Subscription closed via Flux Admin', [
            'subscription_id' => $id,
            'closed_by'       => backpack_user()?->id,
            'closed_at'       => now(),
        ]);

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Subscription closed.');
    }

    public function fireMit(int $id): void
    {
        $userId = backpack_user()?->id;

        if (! $userId) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Authentication required.');

            return;
        }

        try {
            $result = \App\Helpers\JudopayMit::fireDirectMit($id, $userId);
            $this->dispatch('flux-admin:toast',
                type: $result['success'] ? 'success' : 'error',
                message: $result['message'] ?? 'MIT action completed.'
            );
        } catch (\Throwable $e) {
            Log::channel('judopay')->error('Flux Admin MIT fire failed', ['subscription_id' => $id, 'error' => $e->getMessage()]);
            $this->dispatch('flux-admin:toast', type: 'error', message: 'MIT fire failed: '.$e->getMessage());
        }
    }

    public function openBillingForm(int $id): void
    {
        $subscription = JudopaySubscription::findOrFail($id);
        $this->billingSubscriptionId = $id;
        $this->billingFrequency = $subscription->billing_frequency ?? 'weekly';
        $this->billingDay = $subscription->billing_day;
        $this->showBillingForm = true;
    }

    public function saveBillingDay(): void
    {
        $this->validate([
            'billingFrequency'  => ['required', 'in:weekly,monthly'],
            'billingDay'        => ['nullable', 'integer', 'in:1,15,28'],
        ]);

        $subscription = JudopaySubscription::findOrFail($this->billingSubscriptionId);
        $subscription->billing_frequency = $this->billingFrequency;
        $subscription->billing_day = $this->billingFrequency === 'weekly' ? 6 : $this->billingDay;
        $subscription->save();

        $this->showBillingForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Billing day updated.');
    }

    public function openAmountForm(int $id): void
    {
        $subscription = JudopaySubscription::findOrFail($id);
        $this->amountSubscriptionId = $id;
        $this->newAmount = (string) $subscription->amount;
        $this->showAmountForm = true;
    }

    public function saveAmount(): void
    {
        $this->validate(['newAmount' => ['required', 'numeric', 'min:0.01']]);

        $subscription = JudopaySubscription::findOrFail($this->amountSubscriptionId);
        $oldAmount = $subscription->amount;
        $subscription->amount = $this->newAmount;
        $subscription->save();

        Log::channel('judopay')->info('Amount updated via Flux Admin', [
            'subscription_id' => $this->amountSubscriptionId,
            'old_amount' => $oldAmount,
            'new_amount' => $subscription->amount,
            'updated_by' => backpack_user()?->id,
        ]);

        $this->showAmountForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Amount updated from £'.number_format($oldAmount, 2).' to £'.number_format((float) $this->newAmount, 2).'.');
    }

    public function killPreviousLinks(int $id): void
    {
        $cancelledSessions = JudopayCitPaymentSession::where('subscription_id', $id)
            ->where('is_active', true)
            ->where('status', 'created')
            ->update(['status' => 'cancelled', 'is_active' => false, 'failure_reason' => 'Cancelled by admin via Flux Admin']);

        $cancelledAccesses = JudopayCitAccess::where('subscription_id', $id)
            ->where('expires_at', '>', now())
            ->delete();

        Log::channel('judopay')->info('Killed previous authorization links via Flux Admin', [
            'subscription_id' => $id,
            'cancelled_sessions' => $cancelledSessions,
            'cancelled_accesses' => $cancelledAccesses,
        ]);

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Killed '.($cancelledSessions + $cancelledAccesses).' previous links/sessions.');
    }

    public function openAuthForm(int $id): void
    {
        $subscription = JudopaySubscription::with('judopayOnboarding.onboardable')->findOrFail($id);
        $customer = $subscription->judopayOnboarding?->onboardable;

        $this->authSubscriptionId = $id;
        $this->authCustomerId = $customer?->id ?? 0;
        $this->authCustomerEmail = $customer?->email ?? '';
        $this->authCustomerName = $customer ? trim($customer->first_name.' '.$customer->last_name) : '';
        $this->authExpiresInHours = '24';
        $this->generatedAuthLink = '';
        $this->showAuthForm = true;
    }

    public function generateAuthAccess(): void
    {
        $this->validate([
            'authCustomerId'     => ['required', 'integer', 'exists:customers,id'],
            'authSubscriptionId' => ['required', 'integer', 'exists:judopay_subscriptions,id'],
            'authExpiresInHours' => ['required', 'integer', 'min:1', 'max:168'],
        ]);

        try {
            $result = JudopayAuthorizationHelper::generateAuthorizationLink(
                $this->authCustomerId,
                $this->authSubscriptionId,
                (int) $this->authExpiresInHours,
                [
                    'customer_email' => $this->authCustomerEmail,
                    'customer_name'  => $this->authCustomerName,
                ]
            );

            if ($result['success']) {
                $this->generatedAuthLink = $result['url'];
                $this->dispatch('flux-admin:toast', type: 'success', message: 'Auth link generated.');
            } else {
                $this->dispatch('flux-admin:toast', type: 'error', message: $result['message'] ?? 'Failed to generate link.');
            }
        } catch (\Throwable $e) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Error: '.$e->getMessage());
        }
    }

    public function sendAuthEmail(int $id): void
    {
        $subscription = JudopaySubscription::with('judopayOnboarding.onboardable')->findOrFail($id);
        $customer = $subscription->judopayOnboarding?->onboardable;

        if (! $customer?->email) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Customer has no email address.');

            return;
        }

        $access = JudopayCitAccess::where('subscription_id', $id)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $access) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'No active authorization link found. Generate one first.');

            return;
        }

        $authUrl = route('judopay.consent-form', [
            'customer_id'     => $customer->id,
            'passcode'        => $access->passcode,
            'subscription_id' => $id,
        ]);

        try {
            $result = JudopaySmsHelper::sendAuthorizationLink(
                $authUrl,
                $customer->email,
                trim($customer->first_name.' '.$customer->last_name),
                'recurring payment authorization',
                '24 hours',
                (string) $id
            );

            $this->dispatch('flux-admin:toast',
                type: $result['success'] ? 'success' : 'error',
                message: $result['success'] ? 'Authorization email sent to '.$customer->email : 'Failed: '.($result['message'] ?? 'unknown')
            );
        } catch (\Throwable $e) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Error: '.$e->getMessage());
        }
    }

    public function render()
    {
        $rows = $this->baseQuery()->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        return view('flux-admin.pages.judopay.subscriptions-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return JudopaySubscription::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('consumer_reference', 'like', "%{$v}%")->orWhere('card_last_four', 'like', "%{$v}%")->orWhere('receipt_id', 'like', "%{$v}%")->orWhere('auth_code', 'like', "%{$v}%")))
            ->when($this->filter('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($this->filter('billing_frequency'), fn ($q, $v) => $q->where('billing_frequency', $v))
            ->when($this->filterSubscribableType !== '', fn ($q) => $q->where('subscribable_type', $this->filterSubscribableType))
            ->when($this->filterCustomerName !== '', function ($q) {
                $name = '%'.$this->filterCustomerName.'%';
                $q->where(function ($q) use ($name) {
                    $q->whereHasMorph('subscribable', [\App\Models\RentingBooking::class], fn ($q) => $q->whereHas('customer', fn ($q) => $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$name])))
                      ->orWhereHasMorph('subscribable', [\App\Models\FinanceApplication::class], fn ($q) => $q->whereHas('customer', fn ($q) => $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$name])));
                });
            });
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'Date' => fn ($r) => $r->date ? \Carbon\Carbon::parse($r->date)->format('Y-m-d') : '',
            'Consumer' => 'consumer_reference', 'Card' => 'card_last_four',
            'Billing frequency' => 'billing_frequency', 'Billing day' => 'billing_day', 'Amount' => 'amount',
            'Start' => fn ($r) => $r->start_date ? \Carbon\Carbon::parse($r->start_date)->format('Y-m-d') : '',
            'End' => fn ($r) => $r->end_date ? \Carbon\Carbon::parse($r->end_date)->format('Y-m-d') : '',
            'Status' => 'status',
        ];
    }
}
