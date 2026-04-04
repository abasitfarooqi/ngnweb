<?php

namespace App\Livewire\Site\Contact;

use App\Mail\ContactSubmission;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class CallBack extends Component
{
    public $name = '';

    public $phone = '';

    public $preferredTime = '';

    public $message = '';

    public function submitRequest(): void
    {
        $this->validate([
            'name' => 'required|string|min:2',
            'phone' => 'required|string|min:10',
            'preferredTime' => 'required|string',
            'message' => 'nullable|string|max:5000',
        ]);

        $toEmail = config('mail.from.address', 'customerservice@neguinhomotors.co.uk');
        $body = 'Call back request'."\n\n"
            .'Phone: '.$this->phone."\n"
            .'Preferred time: '.$this->preferredTime."\n\n"
            .'Message:'."\n".($this->message !== '' ? $this->message : '—');

        try {
            Mail::to($toEmail)->send(new ContactSubmission(
                senderName: $this->name,
                senderEmail: $toEmail,
                phone: $this->phone,
                topic: 'Call back request',
                messageBody: $body,
                branchName: '',
            ));
        } catch (\Exception $e) {
            \Log::error('Call back form mail failed: '.$e->getMessage());
        }

        session()->flash('success', 'Callback request received. We\'ll call you shortly.');
        $this->reset(['name', 'phone', 'preferredTime', 'message']);
    }

    public function render()
    {
        return view('livewire.site.contact.call-back')
            ->layout('components.layouts.public', [
                'title' => 'Request a Call Back | NGN Motors',
            ]);
    }
}
