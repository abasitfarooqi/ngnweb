<?php

namespace App\Livewire\Site;

use App\Models\Branch;
use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class Header extends Component
{
    public $branches;

    public $selectedBranch;

    public int $cartCount = 0;

    public function mount(CartService $cartService)
    {
        $this->branches = Branch::orderBy('name')->get();

        if ($this->branches->isEmpty() && config('site.branches')) {
            $this->branches = collect(config('site.branches'))->map(fn ($b, $key) => (object) [
                'id' => $key,
                'name' => $b['name'] ?? ucfirst($key),
                'phone' => $b['phone'] ?? '',
                'whatsapp' => $b['whatsapp'] ?? '',
                'address' => $b['address'] ?? '',
                'email' => $b['email'] ?? '',
                'map' => $b['map'] ?? '',
                'whatsapp_link' => $b['whatsapp_link'] ?? '',
            ]);
        }

        $this->selectedBranch = session('selected_branch_id') ?? $this->branches->first()?->id ?? null;
        $this->cartCount = $cartService->count();
    }

    public function selectBranch($branchId)
    {
        $this->selectedBranch = $branchId;
        session(['selected_branch_id' => $branchId]);
        cookie()->queue('selected_branch_id', $branchId, 525600);
        $this->dispatch('branch-changed', branchId: $branchId);
    }

    #[On('cart-updated')]
    public function refreshCartCount(?int $count = null): void
    {
        $this->cartCount = max(0, (int) ($count ?? app(CartService::class)->count()));
    }

    public function render()
    {
        return view('livewire.site.header');
    }
}
