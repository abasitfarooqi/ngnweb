<?php

namespace App\Livewire\Site\Career;

use App\Mail\ContactSubmission;
use App\Models\NgnCareer;
use App\Support\UkMobilePhone;
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

    public function updatedPhone(string $value): void
    {
        $this->phone = UkMobilePhone::sanitizeLiveInput($value);
    }

    protected function rules(): array
    {
        return [
            'firstName'   => 'required|string|min:2|max:100',
            'lastName'    => 'required|string|min:2|max:100',
            'email'       => 'required|email|max:255',
            'phone'       => ['required', 'string', 'size:11', 'regex:/^07\d{9}$/'],
            'coverLetter' => 'required|string|min:20|max:5000',
        ];
    }

    protected function messages(): array
    {
        return array_merge(UkMobilePhone::validationMessages(), [
            'firstName.required' => 'Please enter your first name.',
            'lastName.required' => 'Please enter your last name.',
            'email.required' => 'Please enter your email address.',
            'coverLetter.required' => 'Please include a cover letter or message.',
            'coverLetter.min' => 'Please write at least a short message about why you would be a good fit.',
        ]);
    }

    public function mount(int $id): void
    {
        $this->career = NgnCareer::findOrFail($id);
    }

    public function submitApplication(): void
    {
        $this->phone = UkMobilePhone::normalize($this->phone);
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
