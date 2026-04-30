<?php

namespace App\Livewire\FluxAdmin\Pages\Judopay;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnMitQueue;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Weekly MIT schedule — Flux Admin')]
class WeeklyMitQueueIndex extends Component
{
    use WithAuthorization;

    #[Url(as: 'week')]
    public string $week = '';

    #[Url(as: 'sort')]
    public string $sortMode = '';

    public function mount(): void
    {
        $this->authorizeModule('see-judopay');
        if ($this->week === '') {
            $this->week = Carbon::now()->startOfWeek()->toDateString();
        }
    }

    public function goTo(string $direction): void
    {
        $base = Carbon::parse($this->week);
        $this->week = ($direction === 'prev' ? $base->subWeek() : $base->addWeek())->startOfWeek()->toDateString();
    }

    public function goToday(): void
    {
        $this->week = Carbon::now()->startOfWeek()->toDateString();
    }

    public function render()
    {
        $start = Carbon::parse($this->week)->startOfWeek();
        $end = $start->copy()->endOfWeek();

        $query = NgnMitQueue::with([
            'subscribable.judopayOnboarding.onboardable',
            'subscribable.subscribable',
        ])
            ->whereHas('subscribable')
            ->whereBetween('invoice_date', [$start->toDateString(), $end->toDateString()]);

        if ($this->sortMode === 'success') {
            $query->orderByDesc('cleared')->orderBy('invoice_date');
        } elseif ($this->sortMode === 'decline') {
            $query->orderByRaw("CASE WHEN status = 'sent' AND cleared = 0 THEN 0 ELSE 1 END")->orderBy('invoice_date');
        } else {
            $query->orderBy('invoice_date');
        }

        $items = $query->get();

        $summary = [
            'total' => $items->count(),
            'cleared' => $items->where('cleared', true)->count(),
            'declined' => $items->where('status', 'sent')->where('cleared', false)->count(),
            'pending' => $items->where('status', '!=', 'sent')->count(),
        ];

        return view('flux-admin.pages.judopay.weekly-mit-queue-index', [
            'items' => $items,
            'summary' => $summary,
            'weekStart' => $start,
            'weekEnd' => $end,
        ]);
    }
}
