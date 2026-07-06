<?php

namespace App\Livewire\Site\Bikes;

use App\Models\Motorcycle;
use App\Support\UsedMotorbikeListing;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'filter', except: 'all')]
    public string $filterType = 'all';

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

    public function setFilter(string $type): void
    {
        if (! in_array($type, ['all', 'new', 'used'], true)) {
            return;
        }

        $this->filterType = $type;
        $this->resetPage();
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'sort', 'availability', 'minPrice', 'maxPrice'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->sort = 'default';
        $this->availability = 'available';
        $this->minPrice = '';
        $this->maxPrice = '';
        $this->resetPage();
    }

    public function render()
    {
        $newBikes = collect();
        if (in_array($this->filterType, ['all', 'new'], true)) {
            try {
                $q = Motorcycle::where('availability', 'for sale');

                if ($this->search !== '' && $this->filterType === 'new') {
                    $term = '%'.$this->search.'%';
                    $q->where(function ($inner) use ($term) {
                        $inner->where('make', 'like', $term)
                            ->orWhere('model', 'like', $term);
                    });
                }

                $newBikes = $q->orderByDesc('created_at')->get();
            } catch (\Exception $e) {
                $newBikes = collect();
            }
        }

        $usedBikes = null;
        if (in_array($this->filterType, ['all', 'used'], true)) {
            try {
                $usedBikes = UsedMotorbikeListing::query(
                    $this->search,
                    $this->sort,
                    $this->availability,
                    $this->minPrice,
                    $this->maxPrice,
                )->paginate(12);
            } catch (\Exception $e) {
                $usedBikes = null;
            }
        }

        return view('livewire.site.bikes.index', compact('newBikes', 'usedBikes'))
            ->layout('components.layouts.public', [
                'title' => 'Used Motorcycles For Sale London | New Bikes | NGN Motors',
                'description' => 'Quality used motorcycles for sale in London. Payment plans available.',
            ]);
    }
}
