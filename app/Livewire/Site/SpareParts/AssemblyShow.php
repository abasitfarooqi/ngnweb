<?php

namespace App\Livewire\Site\SpareParts;

use App\Services\CartService;
use App\Support\SpareParts\SparePartsCatalogue;
use Livewire\Component;

class AssemblyShow extends Component
{
    public string $manufacturer = '';

    public string $model = '';

    public string $year = '';

    public string $country = '';

    public string $colour = '';

    public string $assembly = '';

    public string $assemblySearch = '';

    protected CartService $cart;

    public function boot(CartService $cart): void
    {
        $this->cart = $cart;
    }

    public function mount(
        string $manufacturer,
        string $model,
        string $year,
        string $country,
        string $colour,
        string $assembly
    ): void {
        $this->manufacturer = $manufacturer;
        $this->model = $model;
        $this->year = $year;
        $this->country = $country;
        $this->colour = $colour;
        $this->assembly = $assembly;
    }

    public function addToBasket(string $partNumber): void
    {
        $part = $this->findPart($partNumber);
        if ($part === null || empty($part['sp_part_id']) || empty($part['sp_assembly_id'])) {
            return;
        }

        $fitment = [
            'manufacturer' => $this->manufacturer,
            'model' => $this->model,
            'year' => $this->year,
            'country' => $this->country,
            'colour' => $this->colour,
            'assembly' => $this->assembly,
        ];

        $this->cart->addSparePart(
            (int) $part['sp_part_id'],
            (int) $part['sp_assembly_id'],
            (string) $part['part_number'],
            (float) $part['price_gbp_inc_vat'],
            1,
            $fitment
        );
        $this->dispatch('cart-updated', count: $this->cart->count())->to('site.header');
    }

    public function getAssembliesProperty(): array
    {
        $assemblies = app(SparePartsCatalogue::class)->assemblies(
            $this->manufacturer,
            $this->model,
            $this->year,
            $this->country,
            $this->colour
        );

        if ($this->assemblySearch === '') {
            return $assemblies;
        }
        $needle = mb_strtolower($this->assemblySearch);

        return array_values(array_filter($assemblies, function (array $assembly) use ($needle): bool {
            return str_contains(mb_strtolower($assembly['name']), $needle);
        }));
    }

    public function getPartsProperty(): array
    {
        return app(SparePartsCatalogue::class)->parts(
            $this->manufacturer,
            $this->model,
            $this->year,
            $this->country,
            $this->colour,
            $this->assembly
        );
    }

    public function render()
    {
        $parts = $this->parts;
        if ($this->assembly !== '' && $parts === []) {
            abort(404);
        }

        return view('livewire.site.spareparts.assembly-show', [
            'parts' => $parts,
        ])->layout('components.layouts.public', [
            'title' => strtoupper(str_replace('-', ' ', $this->assembly)).' | Spareparts | NGN Motors',
            'description' => 'Spareparts listing for selected bike fitment and assembly.',
        ]);
    }

    /** @return array<string,mixed>|null */
    private function findPart(string $partNumber): ?array
    {
        $needle = app(SparePartsCatalogue::class)->normalisePartNumber($partNumber);
        foreach ($this->parts as $part) {
            if (($part['part_number'] ?? '') === $needle) {
                return $part;
            }
        }

        return null;
    }
}
