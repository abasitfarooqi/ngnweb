<?php

namespace App\Livewire\Site\Services;

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

    public function mount(): void
    {
        if (! in_array($this->openPanel, self::PANELS, true)) {
            $this->openPanel = 'Repairs';
        }
    }

    public function mapPanelToServiceType(string $panel): ?string
    {
        return match ($panel) {
            'Repairs' => 'Motorcycle Repairs Enquiry',
            'MOT' => 'MOT Booking Enquiry',
            'Rental' => 'Motorcycle Rental Enquiry',
            default => null,
        };
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
