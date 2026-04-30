<?php

namespace App\Livewire\FluxAdmin\Pages\Judopay;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\Customer;
use App\Models\JudopayOnboarding;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Recurring billing — Flux Admin')]
class RecurringIndex extends Component
{
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    public function render()
    {
        $query = JudopayOnboarding::query()
            ->where('onboardable_type', Customer::class)
            ->with([
                'onboardable' => fn ($q) => $q->with([
                    'renting_bookings' => fn ($rentals) => $rentals->where('is_posted', true)
                        ->whereHas('rentingBookingItems', fn ($items) => $items->whereNull('end_date'))
                        ->with(['rentingBookingItems' => fn ($items) => $items->whereNull('end_date')->with('motorbike:id,reg_no,make,model')]),
                    'financeApplications' => fn ($finance) => $finance->where('is_posted', true)
                        ->where(fn ($q) => $q->where('is_cancelled', false)->orWhereNull('is_cancelled'))
                        ->where(fn ($q) => $q->where('log_book_sent', false)->orWhereNull('log_book_sent'))
                        ->with(['application_items.motorbike:id,reg_no,make,model']),
                ]),
                'subscriptions' => fn ($q) => $q->with('subscribable'),
            ])
            ->when($this->filter('onboarded') !== null && $this->filter('onboarded') !== '', fn ($q) => $q->where('is_onboarded', (bool) $this->filter('onboarded')));

        $stats = [
            'total_customers' => (clone $query)->count(),
            'onboarded' => (clone $query)->where('is_onboarded', true)->count(),
            'not_onboarded' => (clone $query)->where('is_onboarded', false)->count(),
        ];

        $onboardings = $query->orderByDesc('updated_at')->paginate($this->perPage);

        if ($this->search) {
            $onboardings->setCollection($onboardings->getCollection()->filter(fn ($o) => $o->onboardable && (stripos($o->onboardable->first_name.' '.$o->onboardable->last_name, $this->search) !== false || stripos((string) $o->onboardable->email, $this->search) !== false))->values());
        }

        return view('flux-admin.pages.judopay.recurring-index', compact('onboardings', 'stats'));
    }
}
