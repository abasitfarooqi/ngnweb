<?php

namespace App\Livewire\Site\SpareParts;

use App\Models\SpAssemblyPart;
use App\Models\SpPart;
use App\Services\CartService;
use App\Support\SpareParts\SparePartsCatalogue;
use Livewire\Component;

class Index extends Component
{
    public string $partNumberSearch = '';
    public string $selectedManufacturer = '';
    public string $selectedModel = '';
    public string $selectedYear = '';
    public string $selectedCountry = '';
    public string $selectedColour = '';
    public string $selectedAssembly = '';
    public string $assemblySearch = '';
    public string $catalogueSearch = '';
    public string $catalogueManufacturer = '';
    public string $catalogueModel = '';
    public string $catalogueCategory = '';
    public int $catalogueLimit = 24;

    /** @var array<string, mixed>|null */
    public ?array $partLookup = null;

    protected CartService $cart;

    public function boot(CartService $cart): void
    {
        $this->cart = $cart;
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
        if (empty($part['sp_part_id']) || empty($part['sp_assembly_id'])) {
            return;
        }

        $fitment = [
            'manufacturer' => $this->selectedManufacturer,
            'model' => $this->selectedModel,
            'year' => $this->selectedYear,
            'country' => $this->selectedCountry,
            'colour' => $this->selectedColour,
            'assembly' => $this->selectedAssembly,
        ];

        $this->cart->addSparePart(
            (int) ($part['sp_part_id'] ?? 0),
            (int) ($part['sp_assembly_id'] ?? 0),
            (string) ($part['part_number'] ?? ''),
            (float) ($part['price_gbp_inc_vat'] ?? 0),
            1,
            $fitment
        );
        $this->dispatch('cart-updated', count: $this->cart->count());
    }

    public function addCataloguePartToBasket(int $spPartId): void
    {
        $line = SpAssemblyPart::query()
            ->with(['assembly.fitment.model.make', 'part'])
            ->where('part_id', $spPartId)
            ->orderBy('sort_order')
            ->first();

        if (! $line || ! $line->part || ! $line->assembly) {
            return;
        }

        $fitment = $line->assembly->fitment;
        $model = $fitment?->model;
        $make = $model?->make;
        $this->cart->addSparePart(
            $line->part->id,
            $line->assembly->id,
            $line->part->part_number,
            (float) ($line->price_override ?? $line->part->price_gbp_inc_vat ?? 0),
            1,
            [
                'manufacturer' => (string) ($make?->slug ?? ''),
                'model' => (string) ($model?->slug ?? ''),
                'year' => (string) ($fitment?->year ?? ''),
                'country' => (string) ($fitment?->country_slug ?? ''),
                'colour' => (string) ($fitment?->colour_slug ?? ''),
                'assembly' => (string) ($line->assembly->slug ?? ''),
            ]
        );

        $this->dispatch('cart-updated', count: $this->cart->count());
    }

    public function loadMoreCatalogue(): void
    {
        $this->catalogueLimit += 24;
    }

    public function clearCatalogueFilters(): void
    {
        $this->catalogueSearch = '';
        $this->catalogueManufacturer = '';
        $this->catalogueModel = '';
        $this->catalogueCategory = '';
        $this->catalogueLimit = 24;
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
        return $this->cart->count();
    }

    public function getCatalogueManufacturerOptionsProperty(): array
    {
        return app(SparePartsCatalogue::class)->manufacturers();
    }

    public function getCatalogueModelOptionsProperty(): array
    {
        if ($this->catalogueManufacturer === '') {
            return [];
        }

        return app(SparePartsCatalogue::class)->models($this->catalogueManufacturer);
    }

    public function getCatalogueCategoryOptionsProperty(): array
    {
        return SpAssemblyPart::query()
            ->join('sp_assemblies', 'sp_assembly_parts.assembly_id', '=', 'sp_assemblies.id')
            ->selectRaw('sp_assemblies.slug, sp_assemblies.name, COUNT(*) as lines_count')
            ->groupBy('sp_assemblies.slug', 'sp_assemblies.name')
            ->orderBy('sp_assemblies.name')
            ->limit(120)
            ->get()
            ->map(fn ($row) => [
                'slug' => (string) $row->slug,
                'name' => (string) $row->name,
                'count' => (int) $row->lines_count,
            ])
            ->all();
    }

    public function getCatalogueCardsProperty(): array
    {
        $query = SpPart::query()
            ->where('is_active', true)
            ->with(['assemblyParts.assembly.fitment.model.make', 'assemblyParts.assembly'])
            ->whereHas('assemblyParts');

        if ($this->catalogueSearch !== '') {
            $searchText = trim($this->catalogueSearch);
            $needle = '%'.str_replace(' ', '', $searchText).'%';
            $query->where(function ($q) use ($needle, $searchText) {
                $q->where('part_number', 'like', $needle)
                    ->orWhere('name', 'like', '%'.$searchText.'%');
            });
        }

        if ($this->catalogueManufacturer !== '') {
            $manufacturer = $this->catalogueManufacturer;
            $query->whereHas('assemblyParts.assembly.fitment.model.make', function ($q) use ($manufacturer) {
                $q->where('slug', $manufacturer);
            });
        }

        if ($this->catalogueModel !== '') {
            $model = $this->catalogueModel;
            $query->whereHas('assemblyParts.assembly.fitment.model', function ($q) use ($model) {
                $q->where('slug', $model);
            });
        }

        if ($this->catalogueCategory !== '') {
            $category = $this->catalogueCategory;
            $query->whereHas('assemblyParts.assembly', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        return $query
            ->orderBy('part_number')
            ->limit($this->catalogueLimit)
            ->get()
            ->map(function (SpPart $part): array {
                $line = $part->assemblyParts->first();
                $assembly = $line?->assembly;
                $fitment = $assembly?->fitment;
                $model = $fitment?->model;
                $make = $model?->make;

                return [
                    'id' => (int) $part->id,
                    'part_number' => (string) $part->part_number,
                    'name' => (string) $part->name,
                    'price' => (float) ($line?->price_override ?? $part->price_gbp_inc_vat ?? 0),
                    'stock' => (string) ($line?->stock_override ?? $part->stock_status ?? 'NOT IN STOCK'),
                    'image' => (string) ($assembly?->image_url ?? ''),
                    'manufacturer_name' => (string) ($make?->name ?? ''),
                    'model_name' => (string) ($model?->name ?? ''),
                    'category_name' => (string) ($assembly?->name ?? ''),
                    'assembly_path' => ($make && $model && $fitment && $assembly)
                        ? route('spareparts.assembly', [
                            'manufacturer' => $make->slug,
                            'model' => $model->slug,
                            'year' => $fitment->year,
                            'country' => $fitment->country_slug,
                            'colour' => $fitment->colour_slug,
                            'assembly' => $assembly->slug,
                        ])
                        : null,
                ];
            })
            ->all();
    }

    public function render()
    {
        return view('livewire.site.spareparts.index')
            ->layout('components.layouts.public', [
                'title' => 'Find Your Bike Part | NGN Spareparts',
                'description' => 'Search by part number or browse by manufacturer, model, year, country, colour and assembly.',
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
