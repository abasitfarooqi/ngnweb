<?php

namespace App\Livewire\Site\Mot;

use App\Models\MotTaxAlertSubscription;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Alert extends Component
{
    public $firstName = '';

    public $lastName = '';

    public $email = '';

    public $phone = '';

    public $regNo = '';

    public bool $notifyEmail = true;

    public bool $notifyPhone = false;

    public bool $enableDeals = false;

    protected $rules = [
        'firstName' => 'required|string|min:2',
        'lastName' => 'required|string|min:2',
        'email' => 'required|email',
        'phone' => 'required|string|min:10',
        'regNo' => 'required|string|min:2|max:10',
    ];

    public function submitAlert(): void
    {
        $this->validate();
        $this->regNo = strtoupper(str_replace(' ', '', trim($this->regNo)));

        MotTaxAlertSubscription::create([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => strtolower(trim($this->email)),
            'phone' => $this->phone,
            'vehicle_registration' => $this->regNo,
            'notify_email' => $this->notifyEmail,
            'notify_sms' => $this->notifyPhone,
            'enable_deals' => $this->enableDeals,
        ]);

        $mailPayload = [
            'subject' => 'MOT / Tax alert — new subscription',
            'heading' => 'New MOT / tax alert subscription',
            'greetingName' => 'Team',
            'introLines' => [
                'Someone subscribed to MOT / tax alerts on the website.',
            ],
            'details' => [
                'Name' => trim($this->firstName.' '.$this->lastName),
                'Email' => strtolower(trim($this->email)),
                'Phone' => $this->phone,
                'Registration' => $this->regNo,
                'Notify by email' => $this->notifyEmail ? 'Yes' : 'No',
                'Notify by SMS' => $this->notifyPhone ? 'Yes' : 'No',
                'Deals opt-in' => $this->enableDeals ? 'Yes' : 'No',
            ],
            'outroLines' => [],
        ];

        try {
            Mail::send('emails.templates.universal', $mailPayload, function ($message) use ($mailPayload) {
                $message
                    ->to('customerservice@neguinhomotors.co.uk')
                    ->subject($mailPayload['subject']);
            });
        } catch (\Throwable $e) {
            report($e);
        }

        session()->flash('success', 'MOT/Tax alert registered successfully! We will be in touch using your chosen contact methods.');
        $this->reset([
            'firstName',
            'lastName',
            'email',
            'phone',
            'regNo',
        ]);
        $this->notifyEmail = true;
        $this->notifyPhone = false;
        $this->enableDeals = false;
    }

    public function render()
    {
        return view('livewire.site.mot.alert');
    }
}
