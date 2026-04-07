<?php

namespace App\Livewire\Site;

use App\Http\Controllers\MailController;
use App\Models\BlogPost;
use App\Models\Motorbike;
use App\Models\ServiceBooking;
use Livewire\Component;

class Home extends Component
{
    /** @var list<array{href: string, img: string, title: string, weekly: int, alt: string}> Same order as legacy home: slide 1 (3), slide 2 (3). */
    public array $homeRentalModels = [];

    public $usedBikesForSale = [];

    public $blogPosts = [];

    public string $contactName = '';

    public string $contactEmail = '';

    public string $contactPhone = '';

    public string $contactSubject = '';

    public string $contactMessage = '';

    public function mount(): void
    {
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
            $this->usedBikesForSale = Motorbike::query()
                ->join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
                ->select(
                    'motorbikes.*',
                    'motorbikes_sale.price',
                    'motorbikes_sale.image_one',
                    'motorbikes_sale.mileage as sale_mileage',
                    'motorbikes_sale.is_sold',
                )
                ->where('motorbikes_sale.is_sold', 0)
                ->orderBy('motorbikes.created_at', 'desc')
                ->limit(5)
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

    public function submitContact(): void
    {
        $data = $this->validate([
            'contactName' => ['required', 'string', 'min:2'],
            'contactEmail' => ['nullable', 'email'],
            'contactPhone' => ['required', 'string', 'min:7'],
            'contactSubject' => ['required', 'string', 'min:3'],
            'contactMessage' => ['required', 'string', 'min:5'],
        ], [], [
            'contactName' => 'name',
            'contactEmail' => 'email',
            'contactPhone' => 'phone',
            'contactSubject' => 'subject',
            'contactMessage' => 'message',
        ]);

        $customerAuth = auth('customer')->user();
        $customerId = $customerAuth?->customer_id;

        $booking = ServiceBooking::query()->create([
            'customer_id' => $customerId,
            'customer_auth_id' => $customerAuth?->id,
            'submission_context' => $customerAuth ? 'authenticated_customer' : 'guest',
            'enquiry_type' => ServiceBooking::inferEnquiryType('Sales enquiry', $data['contactSubject'].' '.$data['contactMessage']),
            'service_type' => 'Sales enquiry',
            'subject' => trim($data['contactSubject']),
            'description' => trim($data['contactSubject']).' | '.trim($data['contactMessage']),
            'requires_schedule' => false,
            'booking_date' => null,
            'booking_time' => null,
            'status' => 'Pending',
            'fullname' => trim($data['contactName']),
            'phone' => trim($data['contactPhone']),
            'reg_no' => 'N/A',
            'email' => trim((string) ($data['contactEmail'] ?? '')) ?: null,
        ]);

        app(MailController::class)->sendBookingConfirmation($booking, internalUseContactSubmission: true);

        $this->reset(['contactName', 'contactEmail', 'contactPhone', 'contactSubject', 'contactMessage']);
        session()->flash('success', 'Your enquiry has been sent successfully.');
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
