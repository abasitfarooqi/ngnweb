<?php

namespace App\Livewire\Site;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;

class AccidentManagement extends Component
{
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $reg_no = '';
    public string $language = 'English';
    public bool $privacy_policy = false;
    public bool $submitted = false;

    protected array $rules = [
        'name'          => 'required|string|min:2|max:120',
        'phone'         => 'required|string|min:7|max:30',
        'email'         => 'required|email|max:200',
        'reg_no'        => 'required|string|max:20',
        'language'      => 'required|string',
        'privacy_policy'=> 'accepted',
    ];

    protected array $messages = [
        'privacy_policy.accepted' => 'You must agree to our Privacy Policy to submit a claim.',
    ];

    public function submit()
    {
        $this->validate();

        try {
            $data = [
                'name'     => $this->name,
                'phone'    => $this->phone,
                'email'    => $this->email,
                'reg_no'   => $this->reg_no,
                'language' => $this->language,
            ];

            if (config('mail.mailers.smtp.host') && config('mail.from.address')) {
                Mail::send([], [], function ($message) use ($data) {
                    $message->to(config('mail.from.address', 'info@neguinhomotors.co.uk'))
                        ->subject('Accident Management Claim – ' . $data['name'])
                        ->html(
                            '<h2>New Accident Management Claim</h2>' .
                            '<p><strong>Name:</strong> ' . e($data['name']) . '</p>' .
                            '<p><strong>Phone:</strong> ' . e($data['phone']) . '</p>' .
                            '<p><strong>Email:</strong> ' . e($data['email']) . '</p>' .
                            '<p><strong>Reg / VRM:</strong> ' . e($data['reg_no']) . '</p>' .
                            '<p><strong>Language:</strong> ' . e($data['language']) . '</p>'
                        );
                });
            }
        } catch (\Throwable $e) {
            // mail failure is non-blocking
        }

        $this->submitted = true;
        $this->reset(['name', 'phone', 'email', 'reg_no', 'language', 'privacy_policy']);
    }

    public function render()
    {
        return view('livewire.site.accident-management')
            ->layout('components.layouts.public', [
                'title'       => 'Accident Management Services – Road Traffic Accident Claims | NGN Motors',
                'description' => 'Involved in a motorcycle accident? NGN Motors can help manage your road traffic accident claim. Free assistance, replacement bikes, and repair management across London.',
            ]);
    }
}
