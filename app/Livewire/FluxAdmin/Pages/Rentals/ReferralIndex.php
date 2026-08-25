<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\Customer;
use App\Models\RentingFreeWeekAward;
use App\Models\RentingReferral;
use App\Services\Renting\RentingReferralService;
use App\Support\RentingReferralAccess;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Rental referrals — Flux Admin')]
class ReferralIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    #[Url(history: true)]
    public string $status = '';

    #[Url(as: 'view', history: true)]
    public string $view = 'programme';

    public ?int $expandedAwardId = null;

    public string $newReferrerSearch = '';

    public ?int $newReferrerId = null;

    public string $newName = '';

    public string $newPhone = '';

    public string $newEmail = '';

    public function mount(): void
    {
        $this->authorizeModule('see-menu-rentals');
        $this->sortField = 'created_at';
        $this->view = $this->normaliseView($this->view);
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function setView(string $view): void
    {
        $this->view = $this->normaliseView($view);
        $this->status = '';
        $this->expandedAwardId = null;
        $this->resetPage();
    }

    public function toggleAward(int $id): void
    {
        $this->expandedAwardId = $this->expandedAwardId === $id ? null : $id;
    }

    public function createReferral(RentingReferralService $service): void
    {
        if (! self::staffAddFormEnabled()) {
            return;
        }

        if (! RentingReferralAccess::canView()) {
            abort(403);
        }

        $this->validate([
            'newReferrerId' => 'required|integer|exists:customers,id',
            'newName' => 'required|string|max:120',
            'newPhone' => 'required|string|max:20',
            'newEmail' => 'nullable|email',
        ]);

        $referrer = Customer::query()->findOrFail((int) $this->newReferrerId);

        try {
            $service->create($referrer, [
                'name' => $this->newName,
                'phone' => $this->newPhone,
                'email' => $this->newEmail !== '' ? $this->newEmail : null,
            ], RentingReferral::SOURCE_ADMIN, auth()->id());
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $this->addError('newName', $e->getMessage());

            return;
        }

        $this->reset(['newReferrerId', 'newReferrerSearch', 'newName', 'newPhone', 'newEmail']);
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Referral recorded.');
    }

    public function render(RentingReferralService $service)
    {
        $this->view = $this->normaliseView($this->view);
        $showingAwards = $this->view !== 'programme';

        $sortField = in_array($this->sortField, $showingAwards ? ['id', 'created_at', 'amount', 'source'] : ['id', 'created_at', 'status', 'source'], true)
            ? $this->sortField
            : 'created_at';
        $direction = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        $rows = $showingAwards
            ? $this->awardRows($sortField, $direction)
            : $this->programmeRows($sortField, $direction);

        $referrerChoices = [];
        if (self::staffAddFormEnabled() && strlen(trim($this->newReferrerSearch)) >= 2) {
            $term = '%'.trim($this->newReferrerSearch).'%';
            $referrerChoices = Customer::query()
                ->where(function ($q) use ($term) {
                    $q->where('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                })
                ->orderBy('first_name')
                ->limit(12)
                ->get();
        }

        return view('flux-admin.pages.rentals.referral-index', [
            'rows' => $rows,
            'metrics' => $service->bossMetrics(),
            'freeWeekMetrics' => $service->freeWeekMetrics(),
            'showingAwards' => $showingAwards,
            'referrerChoices' => $referrerChoices,
            'canCreate' => self::staffAddFormEnabled() && RentingReferralAccess::canView(),
        ]);
    }

    private function programmeRows(string $sortField, string $direction)
    {
        return RentingReferral::query()
            ->with(['referrer', 'referred', 'ledger'])
            ->when($this->status === 'redeemed', function ($q) {
                $q->whereHas('ledger', function ($ledger) {
                    $ledger->where('direction', 'debit')->where('status', 'redeemed');
                });
            })
            ->when($this->status === 'in_progress', fn ($q) => $q->whereIn('status', [
                RentingReferral::STATUS_SUBMITTED,
                RentingReferral::STATUS_MATCHED,
                RentingReferral::STATUS_QUALIFYING,
            ]))
            ->when($this->status === 'refused', fn ($q) => $q->whereIn('status', [
                RentingReferral::STATUS_REJECTED,
                RentingReferral::STATUS_CANCELLED,
            ]))
            ->when($this->status !== '' && ! in_array($this->status, ['redeemed', 'in_progress', 'refused'], true), function ($q) {
                $q->where('status', $this->status);
                if ($this->status === 'approved') {
                    $q->whereDoesntHave('ledger', function ($ledger) {
                        $ledger->where('direction', 'debit')->where('status', 'redeemed');
                    });
                }
            })
            ->when($this->search !== '', function ($q) {
                $term = '%'.trim($this->search).'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('referral_code', 'like', $term)
                        ->orWhere('submitted_name', 'like', $term)
                        ->orWhere('submitted_phone', 'like', $term)
                        ->orWhere('submitted_email', 'like', $term)
                        ->orWhereHas('referrer', function ($customer) use ($term) {
                            $customer->where('first_name', 'like', $term)
                                ->orWhere('last_name', 'like', $term)
                                ->orWhere('email', 'like', $term)
                                ->orWhere('phone', 'like', $term);
                        });
                });
            })
            ->orderBy($sortField, $direction)
            ->paginate($this->perPage);
    }

    private function awardRows(string $sortField, string $direction)
    {
        if (! Schema::hasTable('renting_free_week_awards')) {
            return RentingFreeWeekAward::query()->whereRaw('0 = 1')->paginate($this->perPage);
        }

        return RentingFreeWeekAward::query()
            ->with(['hirer', 'selectedReferrer', 'referral', 'appliedBy', 'awardedTransaction', 'awardedInvoice'])
            ->when($this->view === 'direct', fn ($q) => $q->where('source', RentingFreeWeekAward::SOURCE_DIRECT))
            ->when($this->view === 'all', fn ($q) => $q->whereIn('source', [
                RentingFreeWeekAward::SOURCE_PROGRAMME,
                RentingFreeWeekAward::SOURCE_DIRECT,
            ]))
            ->when($this->status === 'redeemed', fn ($q) => $q->whereHas('awardedInvoice', fn ($invoice) => $invoice->where('is_paid', true)))
            ->when($this->status === 'reversed', fn ($q) => $q->whereHas('awardedInvoice', fn ($invoice) => $invoice->where('is_paid', false)))
            ->when($this->search !== '', function ($q) {
                $term = trim($this->search);
                $like = '%'.$term.'%';
                $q->where(function ($inner) use ($term, $like) {
                    if (ctype_digit($term)) {
                        $id = (int) $term;
                        $inner->where('id', $id)
                            ->orWhere('awarded_invoice_id', $id)
                            ->orWhere('awarded_booking_id', $id)
                            ->orWhere('awarded_transaction_id', $id)
                            ->orWhere('hirer_customer_id', $id)
                            ->orWhere('selected_referrer_customer_id', $id)
                            ->orWhere('referral_id', $id);
                    }
                    $inner->orWhere('staff_proof', 'like', $like)
                        ->orWhereHas('hirer', function ($customer) use ($like) {
                            $customer->where('first_name', 'like', $like)
                                ->orWhere('last_name', 'like', $like)
                                ->orWhere('email', 'like', $like)
                                ->orWhere('phone', 'like', $like);
                        })
                        ->orWhereHas('selectedReferrer', function ($customer) use ($like) {
                            $customer->where('first_name', 'like', $like)
                                ->orWhere('last_name', 'like', $like)
                                ->orWhere('email', 'like', $like)
                                ->orWhere('phone', 'like', $like);
                        })
                        ->orWhereHas('referral', fn ($referral) => $referral->where('referral_code', 'like', $like));
                });
            })
            ->orderBy($sortField, $direction)
            ->paginate($this->perPage);
    }

    private function normaliseView(string $view): string
    {
        return in_array($view, ['programme', 'direct', 'all'], true) ? $view : 'programme';
    }

    private static function staffAddFormEnabled(): bool
    {
        return false;
    }
}
