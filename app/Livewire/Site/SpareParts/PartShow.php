<?php

namespace App\Livewire\Site\SpareParts;

use App\Models\SpAssembly;
use App\Models\SpPart;
use App\Services\CartService;
use App\Support\SpareParts\SparePartsCatalogue;
use Livewire\Component;

class PartShow extends Component
{
    public string $partNumber = '';

    protected CartService $cart;

    public function boot(CartService $cart): void
    {
        $this->cart = $cart;
    }

    public function mount(string $partNumber): void
    {
        $this->partNumber = app(SparePartsCatalogue::class)->normalisePartNumber($partNumber);
    }

    public function addToBasket(string $assemblySlug = ''): void
    {
        $part = app(SparePartsCatalogue::class)->findPart($this->partNumber);
        if (! $part) {
            return;
        }

        $spPart = SpPart::query()->where('part_number', $this->partNumber)->first();
        if (! $spPart) {
            return;
        }

        $fitment = collect($part['fitments'] ?? [])->first(function (array $fit) use ($assemblySlug): bool {
            if ($this->partNumber === '') {
                return false;
            }

            return $assemblySlug === '' || ($fit['assembly_slug'] ?? '') === $assemblySlug;
        });

        if (! is_array($fitment)) {
            $fitment = [];
        }

        $spAssemblyId = 0;
        if (($fitment['assembly_slug'] ?? '') !== '') {
            $spAssemblyId = (int) (SpAssembly::query()->where('slug', (string) $fitment['assembly_slug'])->value('id') ?? 0);
        }

        $this->cart->addSparePart(
            $spPart->id,
            $spAssemblyId,
            $spPart->part_number,
            (float) $spPart->price_gbp_inc_vat,
            1,
            $fitment
        );
        $this->dispatch('cart-updated', count: $this->cart->count());
    }

    public function render()
    {
        $part = app(SparePartsCatalogue::class)->findPart($this->partNumber);
        if ($part === null) {
            abort(404);
        }

        return view('livewire.site.spareparts.part-show', compact('part'))
            ->layout('components.layouts.public', [
                'title' => $part['name'].' ('.$part['part_number'].') | NGN Spareparts',
                'description' => 'Part details and bike fitments for '.$part['part_number'].'.',
            ]);
    }
}
