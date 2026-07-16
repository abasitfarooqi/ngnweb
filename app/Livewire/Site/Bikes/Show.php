<?php

namespace App\Livewire\Site\Bikes;

use App\Http\Controllers\MailController;
use App\Models\Motorbike;
use App\Models\Motorcycle;
use App\Models\ServiceBooking;
use App\Support\MotorbikeSoldStatus;
use App\Support\RegistrationMask;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class Show extends Component
{
    public $bike;

    public $isNew = false;

    public $saleInfo = null;

    public string $layoutMode = 'premium';

    public $compliance = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $message = '';

    public bool $privacy = false;

    public function mount($type, $id)
    {
        $this->isNew = ($type === 'new');

        if ($this->isNew) {
            try {
                $this->bike = Motorcycle::findOrFail($id);
            } catch (\Exception $e) {
                abort(404, 'Motorcycle not found');
            }
            $this->layoutMode = 'classic';
        } else {
            try {
                $this->bike = Motorbike::with([
                    'images',
                    'branch',
                    'annualCompliances' => fn ($query) => $query->latest()->limit(1),
                ])->findOrFail($id);

                if (! $this->bike->ngn_vehicle) {
                    abort(404, 'Motorcycle not found');
                }

                if (MotorbikeSoldStatus::isSold((int) $id)) {
                    abort(404, 'Motorcycle not found');
                }

                $this->saleInfo = DB::table('motorbikes_sale')
                    ->where('motorbike_id', $id)
                    ->where('is_sold', 0)
                    ->where(function ($q) {
                        $q->where('is_rented', false)->orWhereNull('is_rented');
                    })
                    ->first();
                $this->compliance = $this->bike->annualCompliances->first();
            } catch (\Exception $e) {
                abort(404, 'Motorcycle not found');
            }
            $this->layoutMode = 'premium';
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
        $this->message = sprintf(
            "I'm interested in %s %s (%s).",
            (string) ($this->bike->make ?? ''),
            (string) ($this->bike->model ?? ''),
            RegistrationMask::hint($this->bike->reg_no ?? null) ?? 'no reg'
        );
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

        $price = $this->isNew
            ? (float) ($this->bike->sale_new_price ?? $this->bike->price ?? 0)
            : (float) ($this->saleInfo->price ?? 0);

        $customerAuth = auth('customer')->user();

        $booking = ServiceBooking::query()->create([
            'customer_id' => $customerAuth?->customer_id,
            'customer_auth_id' => $customerAuth?->id,
            'submission_context' => $customerAuth ? 'authenticated_customer' : 'guest',
            'enquiry_type' => $this->isNew ? 'new_bike' : 'used_bike',
            'service_type' => $this->isNew ? 'New bike sales enquiry' : 'Used bike sales enquiry',
            'subject' => trim(($this->bike->make ?? '').' '.($this->bike->model ?? '').' enquiry'),
            'description' => implode(' | ', array_filter([
                'Source: livewire.site.bikes.show',
                'Bike: '.trim((string) ($this->bike->make ?? '').' '.(string) ($this->bike->model ?? '')),
                'Bike ID: '.(string) ($this->bike->id ?? ''),
                $this->bike->reg_no ? 'Reg: '.$this->bike->reg_no : null,
                $price > 0 ? 'Price GBP: '.number_format($price, 2, '.', '') : null,
                'Customer message: '.trim($this->message),
            ])),
            'requires_schedule' => false,
            'booking_date' => null,
            'booking_time' => null,
            'status' => 'Pending',
            'fullname' => trim($this->name),
            'phone' => trim($this->phone),
            'reg_no' => (string) ($this->bike->reg_no ?? 'N/A'),
            'email' => trim($this->email) ?: null,
        ]);

        app(MailController::class)->sendBookingConfirmation($booking);

        session()->flash('enquiry_success', 'Enquiry received. Our team will contact you shortly.');
        $this->privacy = false;
    }

    /** @return list<string> */
    public function galleryImages(): array
    {
        $urls = [];

        if (! $this->isNew && $this->bike->images) {
            foreach ($this->bike->images as $image) {
                $url = $this->resolveImageUrl($image->url ?? '');
                if ($url !== '') {
                    $urls[] = $url;
                }
            }
        }

        if ($this->saleInfo) {
            foreach (['image_one', 'image_two', 'image_three', 'image_four'] as $field) {
                $raw = $this->saleInfo->{$field} ?? null;
                if ($raw) {
                    $url = $this->resolveImageUrl((string) $raw);
                    if ($url !== '') {
                        $urls[] = $url;
                    }
                }
            }
        }

        return array_values(array_unique($urls));
    }

    public function regLastThree(): ?string
    {
        return RegistrationMask::lastThree($this->bike->reg_no ?? null);
    }

    public function motStatusLabel(): ?string
    {
        if (! $this->compliance) {
            return null;
        }

        $due = $this->compliance->mot_due_date;
        if ($due) {
            return \Carbon\Carbon::parse($due)->endOfDay()->isPast() ? 'Expired' : 'Valid';
        }

        $stored = trim((string) ($this->compliance->getAttributes()['mot_status'] ?? ''));

        if ($stored === '') {
            return null;
        }

        return strcasecmp($stored, 'Expired') === 0 ? 'Expired' : 'Valid';
    }

    public function firstRegistrationLabel(): ?string
    {
        $raw = trim((string) ($this->bike->month_of_first_registration ?? ''));

        return $raw !== '' ? $raw : null;
    }

    public function accessoriesHtml(): ?string
    {
        $raw = trim((string) ($this->saleInfo?->accessories ?? ''));
        if ($raw === '') {
            return null;
        }

        $clean = trim(Purifier::clean($raw, 'motorbike_accessories'));

        return $clean !== '' ? $clean : null;
    }

    /**
     * Catford WhatsApp with bike enquiry details prefilled.
     */
    public function whatsappEnquiryUrl(): string
    {
        $number = (string) config('site.locations.0.whatsapp', '447951790568');
        $price = $this->isNew
            ? (float) ($this->bike->sale_new_price ?? $this->bike->price ?? 0)
            : (float) ($this->saleInfo?->price ?? 0);

        $lines = array_filter([
            'Hello NGN Catford — I want to enquire about this bike:',
            trim(($this->bike->make ?? '').' '.($this->bike->model ?? '')),
            ! empty($this->bike->year) ? 'Year: '.$this->bike->year : null,
            ($regHint = RegistrationMask::hint($this->bike->reg_no ?? null)) !== null ? 'Reg: '.$regHint : null,
            $price > 0 ? 'Price: £'.number_format($price, 2) : null,
            'Link: '.url()->current(),
        ]);

        return 'https://wa.me/'.$number.'?text='.rawurlencode(implode("\n", $lines));
    }

    public function resolveImageUrl(?string $rawPath): string
    {
        $path = trim((string) $rawPath);
        $remoteBase = 'https://neguinhomotors.co.uk';

        if ($path === '') {
            return $remoteBase.'/assets/img/no-image.png';
        }

        // Already an absolute URL.
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        // Keep parity with /bikes cards: always resolve known web paths on the NGN host.
        if (Str::startsWith($path, ['/storage/uploads/', '/storage/motorbikes/', '/assets/'])) {
            return $remoteBase.$path;
        }

        // If we get a relative storage path, normalize onto the remote host.
        if (Str::startsWith($path, ['storage/uploads/', 'storage/motorbikes/'])) {
            return $remoteBase.'/'.ltrim($path, '/');
        }

        // Normalize legacy values like "motorbikes/xxx.jpg" or plain filenames.
        $normalized = ltrim($path, '/');
        $normalized = Str::startsWith($normalized, 'motorbikes/')
            ? $normalized
            : 'motorbikes/'.$normalized;

        return $remoteBase.'/storage/'.$normalized;
    }

    public function render()
    {
        return view('livewire.site.bikes.show')
            ->layout('components.layouts.public', [
                'title' => ($this->bike->make ?? '').' '.($this->bike->model ?? '').' For Sale | NGN Motors',
                'description' => 'Buy '.($this->bike->make ?? '').' '.($this->bike->model ?? '').' at NGN Motors London. Payment plans available.',
            ]);
    }
}
