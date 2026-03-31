<?php

namespace App\Livewire\Site;

use App\Models\BlogPost;
use App\Models\Branch;
use App\Models\Motorbike;
use App\Models\Motorcycle;
use Livewire\Component;

class Home extends Component
{
    public $branches;

    /** @var list<array{href: string, img: string, title: string, weekly: int, alt: string}> Same order as legacy home: slide 1 (3), slide 2 (3). */
    public array $homeRentalModels = [];

    public $newBikesForSale = [];

    public $usedBikesForSale = [];

    public $blogPosts = [];

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();

        // Order matches legacy home carousel: slide 1 (3), slide 2 (3).
        $this->homeRentalModels = [
            [
                'href' => route('site.rental.vision125'),
                'img' => 'img/rentals/honda-vision-125.jpg',
                'title' => 'HONDA VISION 125CC',
                'weekly' => 70,
                'alt' => 'HONDA VISION 125CC motorcycle rental in London, Catford, Tooting and Sutton',
            ],
            [
                'href' => route('site.rental.forza125'),
                'img' => 'img/rentals/honda-forza-125.jpg',
                'title' => 'HONDA FORZA 125CC',
                'weekly' => 100,
                'alt' => 'HONDA FORZA 125CC motorcycle rental in London, Catford, Tooting and Sutton',
            ],
            [
                'href' => route('site.rental.pcx125'),
                'img' => 'img/rentals/honda-pcx-125.jpg',
                'title' => 'HONDA PCX 125CC',
                'weekly' => 75,
                'alt' => 'HONDA PCX 125CC motorcycle rental in London, Catford, Tooting and Sutton',
            ],
            [
                'href' => route('site.rental.sh125'),
                'img' => 'img/rentals/honda-sh-125.jpg',
                'title' => 'HONDA SH 125CC',
                'weekly' => 75,
                'alt' => 'HONDA SH 125CC motorcycle rental in London, Catford, Tooting and Sutton',
            ],
            [
                'href' => route('site.rental.nmax125'),
                'img' => 'img/rentals/yamaha-nmax-125.jpg',
                'title' => 'YAMAHA NMAX 125CC',
                'weekly' => 75,
                'alt' => 'YAMAHA NMAX 125CC motorcycle rental in London, Catford, Tooting and Sutton',
            ],
            [
                'href' => route('site.rental.xmax125'),
                'img' => 'img/rentals/yamaha-xmax-125.jpg',
                'title' => 'YAMAHA XMAX 125CC',
                'weekly' => 100,
                'alt' => 'YAMAHA XMAX 125CC motorcycle rental in London, Catford, Tooting and Sutton',
            ],
        ];

        try {
            $this->newBikesForSale = Motorcycle::where('availability', 'for sale')
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get();
        } catch (\Exception $e) {
            $this->newBikesForSale = collect();
        }

        try {
            $this->usedBikesForSale = Motorbike::join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
                ->select('motorbikes.*', 'motorbikes_sale.price', 'motorbikes_sale.image_one')
                ->where('motorbikes_sale.is_sold', 0)
                ->orderBy('motorbikes.created_at', 'desc')
                ->limit(4)
                ->get();
        } catch (\Exception $e) {
            $this->usedBikesForSale = collect();
        }

        try {
            $this->blogPosts = BlogPost::query()
                ->with(['images'])
                ->latest('id')
                ->limit(4)
                ->get();
        } catch (\Exception $e) {
            $this->blogPosts = collect();
        }
    }

    public function render()
    {
        return view('livewire.site.home')
            ->layout('components.layouts.public', [
                'title' => 'NGN Motors - Motorcycle Rentals, MOT, Repairs & Sales in London',
                'description' => 'Expert motorcycle services in London. Rentals from £80/week, MOT testing, repairs, servicing, used bikes, and finance.',
            ]);
    }
}
