<?php

namespace App\Livewire\Site\Rentals;

use App\Http\Controllers\MailController;
use App\Models\Motorbike;
use App\Models\ServiceBooking;
use Livewire\Component;

class BikeModel extends Component
{
    public $modelSlug;

    public $make;

    public $model;

    /** @var array{weekly_base: float, hero_image: string, tagline: string} */
    public array $pageMeta = [];

    public $bikes;

    public $selectedBike;

    public $selectedPricing;

    public string $selectedPeriod = 'weekly';

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $message = '';

    public bool $privacy = false;

    public function mount(?string $slug = null)
    {
        $slug = $slug ?? (string) request()->route('slug', '');
        $this->modelSlug = $slug;

        $mapping = [
            'honda-forza-125' => ['Honda', 'Forza 125'],
            'honda-pcx-125' => ['Honda', 'PCX 125'],
            'honda-sh-125' => ['Honda', 'SH 125'],
            'honda-vision-125' => ['Honda', 'Vision 125'],
            'yamaha-nmax-125' => ['Yamaha', 'NMAX 125'],
            'yamaha-xmax-125' => ['Yamaha', 'XMAX 125'],
        ];

        if (isset($mapping[$slug])) {
            [$this->make, $this->model] = $mapping[$slug];

            $this->pageMeta = match ($slug) {
                'honda-forza-125' => [
                    'weekly_base' => 100.0,
                    'hero_image' => 'img/rentals/honda-forza-125.jpg',
                    'tagline' => 'For this or similar model, prices start from…',
                ],
                'honda-pcx-125' => [
                    'weekly_base' => 75.0,
                    'hero_image' => 'img/rentals/honda-pcx-125.jpg',
                    'tagline' => 'For this or similar model, prices start from…',
                ],
                'honda-sh-125' => [
                    'weekly_base' => 75.0,
                    'hero_image' => 'img/rentals/honda-sh-125.jpg',
                    'tagline' => 'For this or similar model, prices start from…',
                ],
                'honda-vision-125' => [
                    'weekly_base' => 70.0,
                    'hero_image' => 'img/rentals/honda-vision-125.jpg',
                    'tagline' => 'For this or similar model, prices start from…',
                ],
                'yamaha-nmax-125' => [
                    'weekly_base' => 75.0,
                    'hero_image' => 'img/rentals/yamaha-nmax-125.jpg',
                    'tagline' => 'For this or similar model, prices start from…',
                ],
                'yamaha-xmax-125' => [
                    'weekly_base' => 100.0,
                    'hero_image' => 'img/rentals/yamaha-xmax-125.jpg',
                    'tagline' => 'For this or similar model, prices start from…',
                ],
                default => [
                    'weekly_base' => 80.0,
                    'hero_image' => '',
                    'tagline' => '',
                ],
            };

            $this->bikes = Motorbike::where('make', 'like', '%'.$this->make.'%')
                ->where('model', 'like', '%'.str_replace(' ', '%', $this->model).'%')
                ->whereHas('rentingPricings')
                ->with(['currentRentingPricing', 'images', 'branch'])
                ->get();

            $this->selectedBike = $this->bikes->first();
            $this->selectedPricing = $this->selectedBike?->currentRentingPricing;

            $this->message = sprintf(
                'I want to rent %s %s. Please share next steps.',
                $this->make,
                $this->model
            );
        } else {
            abort(404);
        }

        $customer = auth('customer')->user();
        if ($customer?->email) {
            $this->email = (string) $customer->email;
        }
        if ($customer?->customer?->phone) {
            $this->phone = (string) $customer->customer->phone;
        }
        if ($customer?->customer?->full_name) {
            $this->name = (string) $customer->customer->full_name;
        }
    }

    public function selectBike(int $bikeId): void
    {
        $found = $this->bikes->firstWhere('id', $bikeId);
        if (! $found) {
            return;
        }
        $this->selectedBike = $found;
        $this->selectedPricing = $found->currentRentingPricing;
        $this->selectedPeriod = 'weekly';
    }

    public function setPeriod(string $period): void
    {
        $this->selectedPeriod = $period;
    }

    public function calculatePrice(): float
    {
        $weekly = $this->selectedPricing
            ? (float) $this->selectedPricing->weekly_price
            : (float) ($this->pageMeta['weekly_base'] ?? 80);

        return match ($this->selectedPeriod) {
            'daily' => round($weekly / 6, 2),
            'monthly' => round($weekly * 4 * 0.9, 2),
            default => $weekly,
        };
    }

    public function submitEnquiry(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'min:2'],
            'email' => ['nullable', 'email'],
            'phone' => ['required', 'string', 'min:7'],
            'message' => ['required', 'string', 'min:5'],
            'privacy' => ['accepted'],
        ]);

        $booking = ServiceBooking::query()->create([
            'service_type' => 'Motorcycle rental enquiry',
            'description' => implode(' | ', array_filter([
                'Source: livewire.site.rentals.bike-model',
                'Model page: '.$this->modelSlug,
                'Bike line: '.$this->make.' '.$this->model,
                $this->selectedBike ? 'Fleet bike ID: '.(string) $this->selectedBike->id : null,
                'Period: '.$this->selectedPeriod,
                'Quoted GBP: '.number_format($this->calculatePrice(), 2, '.', ''),
                'Customer message: '.trim($this->message),
            ])),
            'requires_schedule' => false,
            'booking_date' => null,
            'booking_time' => null,
            'status' => 'Pending',
            'fullname' => trim($this->name),
            'phone' => trim($this->phone),
            'reg_no' => 'Model enquiry',
            'email' => trim($this->email) ?: null,
        ]);

        app(MailController::class)->sendBookingConfirmation($booking);
        session()->flash('enquiry_success', 'Rental enquiry sent. Our team will contact you shortly.');
        $this->privacy = false;
    }

    public function render()
    {
        $currentPrice = $this->calculatePrice();

        return view('livewire.site.rentals.bike-model')
            ->with(['currentPrice' => $currentPrice])
            ->layout('components.layouts.public', [
                'title' => $this->make.' '.$this->model.' Rental London | NGN Motors',
                'description' => 'Rent '.$this->make.' '.$this->model.' in London from NGN Motors. Flexible daily, weekly & monthly rates.',
            ]);
    }
}
