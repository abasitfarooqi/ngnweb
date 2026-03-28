<?php

namespace App\Livewire\Site\Shop;

use App\Support\SpareParts\SparePartsCatalogue;
use Livewire\Component;

class SpareParts extends Component
{
    public string $partNumberSearch = '';

    public string $selectedManufacturer = '';

    public string $selectedModel = '';

    public string $selectedYear = '';

    public string $selectedCountry = '';

    public string $selectedColour = '';

    public string $selectedAssembly = '';

    public string $assemblySearch = '';

    /** @var array<string, mixed>|null */
    public ?array $partLookup = null;

    /** @var array<int, array<string, mixed>> */
    public array $basket = [];

    public function mount(): void
    {
        $this->basket = session('spareparts.basket', []);
    }

    public function updatedSelectedManufacturer(): void
    {
        $this->selectedModel = '';
        $this->selectedYear = '';
        $this->selectedCountry = '';
        $this->selectedColour = '';
        $this->selectedAssembly = '';
    }

    public function updatedSelectedModel(): void
    {
        $this->selectedYear = '';
        $this->selectedCountry = '';
        $this->selectedColour = '';
        $this->selectedAssembly = '';
    }

    public function updatedSelectedYear(): void
    {
        $this->selectedCountry = '';
        $this->selectedColour = '';
        $this->selectedAssembly = '';
    }

    public function updatedSelectedCountry(): void
    {
        $this->selectedColour = '';
        $this->selectedAssembly = '';
    }

    public function updatedSelectedColour(): void
    {
        $this->selectedAssembly = '';
    }

    public function searchPartNumber(): void
    {
        $catalogue = app(SparePartsCatalogue::class);
        $this->partLookup = $catalogue->findPart($this->partNumberSearch);
    }

    public function clearPartNumberSearch(): void
    {
        $this->partNumberSearch = '';
        $this->partLookup = null;
    }

    public function selectAssembly(string $assemblySlug): void
    {
        $this->selectedAssembly = $assemblySlug;
    }

    public function addToBasket(string $partNumber): void
    {
        $part = $this->findPartInCurrentAssembly($partNumber);
        if ($part === null) {
            return;
        }

        $key = $part['part_number'];
        $existing = $this->basket[$key] ?? null;

        if ($existing !== null) {
            $this->basket[$key]['quantity'] = (int) $existing['quantity'] + 1;
        } else {
            $this->basket[$key] = [
                'part_number' => $part['part_number'],
                'name' => $part['name'],
                'price_gbp_inc_vat' => $part['price_gbp_inc_vat'],
                'quantity' => 1,
            ];
        }

        session(['spareparts.basket' => $this->basket]);
    }

    public function clearBasket(): void
    {
        $this->basket = [];
        session()->forget('spareparts.basket');
    }

    public function getManufacturerOptionsProperty(): array
    {
        return app(SparePartsCatalogue::class)->manufacturers();
    }

    public function getModelOptionsProperty(): array
    {
        if ($this->selectedManufacturer === '') {
            return [];
        }

        return app(SparePartsCatalogue::class)->models($this->selectedManufacturer);
    }

    public function getYearOptionsProperty(): array
    {
        if ($this->selectedManufacturer === '' || $this->selectedModel === '') {
            return [];
        }

        return app(SparePartsCatalogue::class)->years($this->selectedManufacturer, $this->selectedModel);
    }

    public function getCountryOptionsProperty(): array
    {
        if ($this->selectedManufacturer === '' || $this->selectedModel === '' || $this->selectedYear === '') {
            return [];
        }

        return app(SparePartsCatalogue::class)->countries($this->selectedManufacturer, $this->selectedModel, $this->selectedYear);
    }

    public function getColourOptionsProperty(): array
    {
        if ($this->selectedManufacturer === '' || $this->selectedModel === '' || $this->selectedYear === '' || $this->selectedCountry === '') {
            return [];
        }

        return app(SparePartsCatalogue::class)->colours($this->selectedManufacturer, $this->selectedModel, $this->selectedYear, $this->selectedCountry);
    }

    public function getAssemblyOptionsProperty(): array
    {
        if (
            $this->selectedManufacturer === ''
            || $this->selectedModel === ''
            || $this->selectedYear === ''
            || $this->selectedCountry === ''
            || $this->selectedColour === ''
        ) {
            return [];
        }

        $assemblies = app(SparePartsCatalogue::class)->assemblies(
            $this->selectedManufacturer,
            $this->selectedModel,
            $this->selectedYear,
            $this->selectedCountry,
            $this->selectedColour
        );

        if ($this->assemblySearch === '') {
            return $assemblies;
        }

        $needle = mb_strtolower($this->assemblySearch);

        return array_values(array_filter($assemblies, function (array $assembly) use ($needle): bool {
            return str_contains(mb_strtolower($assembly['name']), $needle);
        }));
    }

    public function getAssemblyPartsProperty(): array
    {
        if (
            $this->selectedManufacturer === ''
            || $this->selectedModel === ''
            || $this->selectedYear === ''
            || $this->selectedCountry === ''
            || $this->selectedColour === ''
            || $this->selectedAssembly === ''
        ) {
            return [];
        }

        return app(SparePartsCatalogue::class)->parts(
            $this->selectedManufacturer,
            $this->selectedModel,
            $this->selectedYear,
            $this->selectedCountry,
            $this->selectedColour,
            $this->selectedAssembly
        );
    }

    public function getBasketCountProperty(): int
    {
        $count = 0;
        foreach ($this->basket as $item) {
            $count += (int) ($item['quantity'] ?? 0);
        }

        return $count;
    }

    public function render()
    {
        return view('livewire.site.shop.spare-parts')
            ->layout('components.layouts.public', [
                'title' => 'Spare Parts Finder | NGN Motors',
                'description' => 'Find bike parts by part number or browse by manufacturer, model, year, country, colour and assembly.',
            ]);
    }

    /** @return array<string, mixed>|null */
    private function findPartInCurrentAssembly(string $partNumber): ?array
    {
        $needle = app(SparePartsCatalogue::class)->normalisePartNumber($partNumber);
        foreach ($this->assemblyParts as $part) {
            if (($part['part_number'] ?? '') === $needle) {
                return $part;
            }
        }

        return null;
    }
}
