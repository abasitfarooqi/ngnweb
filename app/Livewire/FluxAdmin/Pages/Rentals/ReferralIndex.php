<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\Customer;
use App\Models\RentingReferral;
use App\Services\Renting\RentingReferralService;
use App\Support\RentingReferralAccess;
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

    public string $newReferrerSearch = '';

    public ?int $newReferrerId = null;

    public string $newName = '';

    public string $newPhone = '';

    public string $newEmail = '';

    public function mount(): void
    {
        $this->authorizeModule('see-menu-rentals');
        $this->sortField = 'created_at';
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function createReferral(RentingReferralService $service): void
    {
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
        $sortField = in_array($this->sortField, ['id', 'created_at', 'status', 'source'], true)
            ? $this->sortField
            : 'created_at';

        $rows = RentingReferral::query()
            ->with(['referrer', 'referred', 'ledger'])
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
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
            ->orderBy($sortField, $this->sortDirection === 'asc' ? 'asc' : 'desc')
            ->paginate($this->perPage);

        $referrerChoices = [];
        if (strlen(trim($this->newReferrerSearch)) >= 2) {
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
            'referrerChoices' => $referrerChoices,
            'canCreate' => RentingReferralAccess::canView(),
        ]);
    }
}
