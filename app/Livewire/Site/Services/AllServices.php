<?php

namespace App\Livewire\Site\Services;

use App\Livewire\Site\Contact\ServiceBooking;
use Livewire\Attributes\Url;
use Livewire\Component;

class AllServices extends Component
{
    /** @var array<int, string> */
    public const PANELS = [
        'Repairs',
        'MOT',
        'BookService',
        'Delivery',
        'Sales',
        'Rental',
        'Accident',
        'Finance',
    ];

    #[Url(as: 'service', except: 'Repairs')]
    public string $openPanel = 'Repairs';

    /** Pre-selects the embedded booking form (e.g. full vs basic service). */
    #[Url(as: 'booking', except: '')]
    public string $bookingServiceType = '';

    public function mount(): void
    {
        if (! in_array($this->openPanel, self::PANELS, true)) {
            $this->openPanel = 'Repairs';
        }
        $allowed = ServiceBooking::allowedServiceTypes();
        if ($this->bookingServiceType !== '' && ! in_array($this->bookingServiceType, $allowed, true)) {
            $this->bookingServiceType = '';
        }
    }

    public function mapPanelToServiceType(string $panel): ?string
    {
        return match ($panel) {
            'Repairs' => 'Motorcycle Repairs Enquiry',
            'MOT' => 'MOT Booking Enquiry',
            'Rental' => 'Motorcycle Rental Enquiry',
            'Accident' => 'Accident Management Services Enquiry',
            default => null,
        };
    }

    public function bookingPresetForChild(): ?string
    {
        $allowed = ServiceBooking::allowedServiceTypes();
        if ($this->bookingServiceType !== '' && in_array($this->bookingServiceType, $allowed, true)) {
            return $this->bookingServiceType;
        }

        return $this->mapPanelToServiceType($this->openPanel);
    }

    public function render()
    {
        return view('livewire.site.services.all-services')
            ->layout('components.layouts.public', [
                'title' => 'Our Services | NGN Motorcycle — Repairs, MOT, Sales, Rental, London',
                'description' => 'Discover NGN Motorcycle’s full range of services: repairs, MOT, servicing, vehicle delivery, sales, rental, accident management and finance. Catford, Tooting and Sutton.',
            ]);
    }
}
