<?php

namespace App\Livewire\FluxAdmin\Pages\Judopay;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\JudopayMitPaymentSession;
use App\Models\JudopaySubscription;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('MIT dashboard — Flux Admin')]
class MitDashboard extends Component
{
    use WithAuthorization, WithPagination;

    #[Url(as: 'status')]
    public string $status = '';

    public function mount(): void
    {
        $this->authorizeModule('see-judopay');
    }

    public function updatingStatus(): void { $this->resetPage(); }

    public function render()
    {
        $stats = [
            'active_subs' => JudopaySubscription::where('status', 'active')->whereNotNull('card_token')->count(),
            'sessions_total' => JudopayMitPaymentSession::count(),
            'sessions_success' => JudopayMitPaymentSession::where('status', 'success')->count(),
            'sessions_failed' => JudopayMitPaymentSession::where('status', 'failed')->count(),
            'sessions_pending' => JudopayMitPaymentSession::whereIn('status', ['pending', 'processing'])->count(),
        ];

        $rows = JudopayMitPaymentSession::with(['subscription.judopayOnboarding.onboardable', 'subscription.subscribable'])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('flux-admin.pages.judopay.mit-dashboard', compact('stats', 'rows'));
    }
}
