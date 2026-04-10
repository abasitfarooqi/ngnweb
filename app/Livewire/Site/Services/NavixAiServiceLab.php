<?php

namespace App\Livewire\Site\Services;

use App\Livewire\Site\Contact\ServiceBooking;
use App\Models\Branch;
use Livewire\Attributes\Url;
use Livewire\Component;

class NavixAiServiceLab extends Component
{
    #[Url(as: 'booking', except: '')]
    public string $bookingServiceType = '';

    public function mount(): void
    {
        if (! $this->isAllowedBookingType($this->bookingServiceType)) {
            $this->bookingServiceType = '';
        }
    }

    public function setBookingServiceType(string $serviceType): void
    {
        if (! $this->isAllowedBookingType($serviceType)) {
            return;
        }

        $this->bookingServiceType = $serviceType;
    }

    /**
     * @return list<array{
     *     id: string,
     *     title: string,
     *     description: string,
     *     href: string,
     *     cta: string,
     *     booking_type: string|null
     * }>
     */
    public function serviceCards(): array
    {
        return [
            [
                'id' => 'repairs',
                'title' => 'Repairs',
                'description' => 'Workshop diagnostics, repairs and fault fixes for daily riders and delivery bikes.',
                'href' => route('site.repairs'),
                'cta' => 'Open repairs',
                'booking_type' => 'Motorcycle Repairs Enquiry',
            ],
            [
                'id' => 'servicing',
                'title' => 'Servicing',
                'description' => 'Basic and full service packages to keep your bike reliable and roadworthy.',
                'href' => route('site.repairs.comparison'),
                'cta' => 'Compare servicing',
                'booking_type' => 'Motorcycle Full Service Enquiry',
            ],
            [
                'id' => 'mot',
                'title' => 'MOT',
                'description' => 'DVSA-standard motorcycle MOT checks with booking slots and follow-up support.',
                'href' => route('site.mot'),
                'cta' => 'Open MOT',
                'booking_type' => 'MOT Booking Enquiry',
            ],
            [
                'id' => 'rental',
                'title' => 'Rentals',
                'description' => 'Weekly rental options for London riders, including CBT-friendly commuter models.',
                'href' => route('site.rentals'),
                'cta' => 'View rental fleet',
                'booking_type' => 'Motorcycle Rental Enquiry',
            ],
            [
                'id' => 'sales',
                'title' => 'Motorcycle Sales',
                'description' => 'New and used motorcycles with practical options for commuting and business use.',
                'href' => route('motorcycles'),
                'cta' => 'View motorcycles',
                'booking_type' => 'Other',
            ],
            [
                'id' => 'finance',
                'title' => 'Finance',
                'description' => 'Finance paths for qualifying motorcycles, suitable for personal and work riders.',
                'href' => route('site.finance'),
                'cta' => 'Open finance',
                'booking_type' => 'Other',
            ],
            [
                'id' => 'recovery',
                'title' => 'Recovery & Delivery',
                'description' => 'Collection, recovery and motorcycle transport support when your bike needs moving.',
                'href' => route('site.recovery'),
                'cta' => 'Open recovery',
                'booking_type' => 'Other',
            ],
            [
                'id' => 'delivery-order',
                'title' => 'Delivery Order',
                'description' => 'Book motorcycle delivery and movement requests directly from the operations form.',
                'href' => route('motorcycle.delivery'),
                'cta' => 'Open delivery order',
                'booking_type' => 'Other',
            ],
            [
                'id' => 'accident',
                'title' => 'Accident Management',
                'description' => 'Road accident assistance and claim guidance with practical next-step support.',
                'href' => route('accident-management'),
                'cta' => 'Open accident management',
                'booking_type' => 'Accident Management Services Enquiry',
            ],
            [
                'id' => 'partner',
                'title' => 'Partner Programme',
                'description' => 'Business partnerships and trade options for riders, fleets and local operators.',
                'href' => route('site.partner'),
                'cta' => 'Open partner page',
                'booking_type' => 'Other',
            ],
        ];
    }

    /**
     * @return list<array{name: string, phone: string, address: string}>
     */
    public function branchesSummary(): array
    {
        return Branch::query()
            ->select(['name', 'phone_number', 'address'])
            ->orderBy('name')
            ->get()
            ->map(fn (Branch $branch): array => [
                'name' => (string) $branch->name,
                'phone' => (string) ($branch->phone_number ?? ''),
                'address' => (string) ($branch->address ?? ''),
            ])
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.site.services.navix-ai-service-lab', [
            'serviceCards' => $this->serviceCards(),
            'branches' => $this->branchesSummary(),
        ]);
    }

    private function isAllowedBookingType(string $serviceType): bool
    {
        return in_array($serviceType, ServiceBooking::allowedServiceTypes(), true);
    }
}
