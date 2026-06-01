<?php

namespace App\Livewire\FluxAdmin\Pages\Judopay;

use App\Helpers\JudopayMit;
use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\Customer;
use App\Models\JudopayMitQueue;
use App\Models\JudopayOnboarding;
use Illuminate\Support\Facades\Log;
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

    public function addToQueue(int $ngnMitQueueId): void
    {
        $userId = backpack_user()?->id;

        if (! $userId) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Authentication required.');

            return;
        }

        try {
            $result = JudopayMit::addToLiveChamber($ngnMitQueueId, $userId);
            $this->dispatch('flux-admin:toast',
                type: $result['success'] ? 'success' : 'error',
                message: $result['message'] ?? ($result['success'] ? 'Added to queue.' : 'Failed to add to queue.')
            );
        } catch (\Throwable $e) {
            Log::channel('judopay')->error('addToQueue via Flux Admin failed', ['id' => $ngnMitQueueId, 'error' => $e->getMessage()]);
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Error: '.$e->getMessage());
        }
    }

    public function stopQueue(int $liveQueueId): void
    {
        try {
            $item = JudopayMitQueue::with('ngnMitQueue')->findOrFail($liveQueueId);
            $ngnQueue = $item->ngnMitQueue;

            if ($ngnQueue) {
                $ngnQueue->is_in_live_chamber = false;
                $ngnQueue->live_chamber_item_id = null;
                $ngnQueue->save();
            }

            $item->delete();

            Log::channel('judopay')->info('MIT queue item stopped via Flux Admin', ['live_queue_id' => $liveQueueId]);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Queue item stopped.');
        } catch (\Throwable $e) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Error: '.$e->getMessage());
        }
    }

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
