<?php

namespace App\Livewire\Site\Career;

use App\Mail\ContactSubmission;
use App\Models\NgnCareer;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Show extends Component
{
    public NgnCareer $career;

    public string $firstName = '';
    public string $lastName = '';
    public string $email = '';
    public string $phone = '';
    public string $coverLetter = '';

    protected function rules(): array
    {
        return [
            'firstName'   => 'required|string|min:2|max:100',
            'lastName'    => 'required|string|min:2|max:100',
            'email'       => 'required|email|max:255',
            'phone'       => 'required|string|min:10|max:30',
            'coverLetter' => 'required|string|min:20|max:5000',
        ];
    }

    public function mount(int $id): void
    {
        $this->career = NgnCareer::findOrFail($id);
    }

    public function submitApplication(): void
    {
        $this->validate();

        $body = "Job application for: {$this->career->job_title}\n\n"
            . "Name: {$this->firstName} {$this->lastName}\n"
            . "Phone: {$this->phone}\n\n"
            . "Cover letter:\n{$this->coverLetter}";

        try {
            Mail::to(['enquiries@neguinhomotors.co.uk', 'admin@neguinhomotors.co.uk'])
                ->send(new ContactSubmission(
                    senderName: "{$this->firstName} {$this->lastName}",
                    senderEmail: $this->email,
                    phone: $this->phone,
                    topic: "Job application – {$this->career->job_title}",
                    messageBody: $body,
                    branchName: '',
                ));
        } catch (\Throwable $e) {
            report($e);
        }

        session()->flash('success', 'Application submitted. We will be in touch shortly.');
        $this->reset(['firstName', 'lastName', 'email', 'phone', 'coverLetter']);
    }

    public function render()
    {
        return view('livewire.site.career.show')
            ->layout('components.layouts.public', [
                'title' => $this->career->job_title . ' | NGN Motors Careers',
            ]);
    }
}
