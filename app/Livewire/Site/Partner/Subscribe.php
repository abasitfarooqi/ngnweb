<?php

namespace App\Livewire\Site\Partner;

use App\Mail\NgnPartnerRegistrationMailer;
use App\Models\NgnPartner;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Subscribe extends Component
{
    use WithFileUploads;

    public string $companyname = '';
    public string $company_address = '';
    public string $company_number = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $phone = '';
    public string $mobile = '';
    public string $email = '';
    public string $website = '';
    public ?int $fleet_size = null;
    public string $operating_since = '';
    public bool $tc_agreed = false;
    public $company_logo = null;

    protected function rules(): array
    {
        return [
            'companyname'     => 'required|string|max:50|unique:ngn_partners,companyname',
            'company_logo'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_address' => 'nullable|string|max:255',
            'company_number'  => 'nullable|string|max:255',
            'first_name'      => 'nullable|string|max:50',
            'last_name'       => 'nullable|string|max:50',
            'phone'           => 'nullable|string|max:20',
            'mobile'          => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255',
            'website'         => 'nullable|string|max:255',
            'fleet_size'      => 'nullable|integer|min:0',
            'operating_since' => 'nullable|string|max:8',
            'tc_agreed'       => 'required|accepted',
        ];
    }

    public function register(): void
    {
        $this->validate();

        $data = [
            'companyname'     => $this->companyname,
            'company_address' => $this->company_address,
            'company_number'  => $this->company_number,
            'first_name'      => $this->first_name,
            'last_name'       => $this->last_name,
            'phone'           => $this->phone,
            'mobile'          => $this->mobile,
            'email'           => $this->email,
            'website'         => $this->website,
            'fleet_size'      => $this->fleet_size,
            'operating_since' => $this->operating_since,
            'is_approved'     => false,
        ];

        if ($this->company_logo) {
            $filename = 'partner-' . Str::slug($this->companyname) . '-' . time() . '.' . $this->company_logo->getClientOriginalExtension();
            $path = $this->company_logo->storeAs('public/partner-logos', $filename);
            $data['company_logo'] = Storage::url($path);
        }

        try {
            NgnPartner::create($data);
            Mail::to('support@neguinhomotors.co.uk')->send(new NgnPartnerRegistrationMailer($data));
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'There was an error processing your registration. Please try again.');
            return;
        }

        session()->flash('success', 'Thank you for registering! We will review your application and contact you soon.');
        $this->redirect(route('ngnpartner.thankyou'), navigate: false);
    }

    public function render()
    {
        return view('livewire.site.partner.subscribe')
            ->layout('components.layouts.public', [
                'title' => 'Partner Registration | NGN Partner Network',
                'description' => 'Join NGN Partner network to grow your business with us.',
            ]);
    }
}
