<?php

namespace App\Livewire\Site\Ebikes;

use App\Http\Controllers\MailController;
use App\Models\ServiceBooking;
use Livewire\Component;

class Index extends Component
{
    /** @var list<string> */
    public array $galleryUrls = [];

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $message = '';

    public string $reg_no = '';

    public bool $privacy = false;

    public function mount(): void
    {
        $this->galleryUrls = [
            asset('assets/images/ebike-london-cheap-price-uk.png'),
            asset('assets/images/your-front-view.png'),
            asset('assets/images/your-front-mudguard.png'),
            asset('assets/images/your-left-handlebar.png'),
            asset('assets/images/your-right-handlebar.png'),
            asset('assets/images/your-front-wheel-brake.png'),
            asset('assets/images/delivery-box.png'),
        ];

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
        $this->message = 'I would like to enquire about pedal-assist e-bikes (purchase or hire). Please contact me with options.';
    }

    public function submitEnquiry(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'min:2'],
            'email' => ['nullable', 'email'],
            'phone' => ['required', 'string', 'min:7'],
            'message' => ['required', 'string', 'min:5'],
            'privacy' => ['accepted'],
            'reg_no' => ['nullable', 'string', 'max:20'],
        ]);

        $customerAuth = auth('customer')->user();

        $booking = ServiceBooking::query()->create([
            'customer_id' => $customerAuth?->customer_id,
            'customer_auth_id' => $customerAuth?->id,
            'submission_context' => $customerAuth ? 'authenticated_customer' : 'guest',
            'enquiry_type' => 'e_bike',
            'service_type' => 'E-Bike enquiry',
            'subject' => 'Pedal-assist e-bike enquiry',
            'description' => implode(' | ', array_filter([
                'Source: livewire.site.ebikes.index',
                trim($this->reg_no) !== '' ? 'Reg (optional): '.trim($this->reg_no) : null,
                'Customer message: '.trim($this->message),
            ])),
            'requires_schedule' => false,
            'booking_date' => null,
            'booking_time' => null,
            'status' => 'Pending',
            'fullname' => trim($this->name),
            'phone' => trim($this->phone),
            'reg_no' => trim($this->reg_no) !== '' ? trim($this->reg_no) : 'N/A',
            'email' => trim($this->email) !== '' ? trim($this->email) : null,
        ]);

        app(MailController::class)->sendBookingConfirmation($booking);

        session()->flash('enquiry_success', 'Thank you — your e-bike enquiry has been sent. Our team will contact you shortly.');
        $this->privacy = false;
    }

    public function render()
    {
        return view('livewire.site.ebikes.index')->layout('components.layouts.public', [
            'title' => 'E-Bikes London | Electric Bikes for Sale & Hire | Eco-Friendly Urban Transport | NGN Motors',
            'description' => 'Discover pedal-assist e-bikes in London. Buy or hire electric bicycles for commuting, delivery, and leisure. EAPC-compliant, local support, launch offers.',
        ]);
    }
}
