<?php

namespace App\Livewire\Site\Bikes;

use App\Models\Motorcycle;
use App\Support\UsedMotorbikeListing;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;

class UsedIndex extends Component
{
    private const PER_PAGE = 12;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: 'default')]
    public string $sort = 'default';

    #[Url(except: 'available')]
    public string $availability = 'available';

    #[Url(except: '')]
    public string $minPrice = '';

    #[Url(except: '')]
    public string $maxPrice = '';

    public int $loadedPages = 1;

    public function updated($property): void
    {
        if (in_array($property, ['search', 'sort', 'availability', 'minPrice', 'maxPrice'], true)) {
            $this->loadedPages = 1;
        }
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->sort = 'default';
        $this->availability = 'available';
        $this->minPrice = '';
        $this->maxPrice = '';
        $this->loadedPages = 1;
    }

    public function loadMore(): void
    {
        $this->loadedPages++;
    }

    public function render()
    {
        $query = UsedMotorbikeListing::query(
            $this->search,
            $this->sort,
            $this->availability,
            $this->minPrice,
            $this->maxPrice,
        );

        $total = (clone $query)->count();
        $limit = self::PER_PAGE * $this->loadedPages;

        /** @var Collection<int, mixed> $motorbikes */
        $motorbikes = $query->limit($limit)->get();
        $hasMore = $total > $motorbikes->count();

        $latestMotorcycles = Motorcycle::query()
            ->where('availability', 'for sale')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('livewire.site.bikes.used-index', compact('motorbikes', 'latestMotorcycles', 'total', 'hasMore'))
            ->layout('components.layouts.public', [
                'title' => 'Used Motorbike For Sale - NGN - Motorcycle Rentals, Repairs, Accessories in Catford, Tooting, UK',
                'description' => 'Browse used motorcycles for sale with full details, enquiry and finance links.',
            ]);
    }
}
