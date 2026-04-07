<?php

namespace App\Livewire\Site\Repairs;

use App\Models\Branch;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Index extends Component
{
    public $branches;

    public int $formNonce = 0;

    public $selectedService = '';

    public $name = '';

    public $email = '';

    public $phone = '';

    public $selectedBranch = '';

    public $regNo = '';

    public $make = '';

    public $model = '';

    public $description = '';

    protected $rules = [
        'name' => 'required|string|min:2',
        'email' => 'required|email',
        'phone' => 'required|string|min:10',
        'selectedBranch' => 'required|exists:branches,id',
        'selectedService' => 'required|string',
        'description' => 'required|string|min:10',
        'regNo' => 'nullable|string|max:10',
        'make' => 'nullable|string|max:120',
        'model' => 'nullable|string|max:120',
    ];

    public function mount(): void
    {
        $this->branches = Branch::orderBy('name')->get();
    }

    public function submitEnquiry(): void
    {
        $this->validate();
        $reg = $this->regNo ? strtoupper(str_replace(' ', '', trim($this->regNo))) : '';

        $branchName = optional($this->branches->firstWhere('id', (int) $this->selectedBranch))->name ?? 'Unknown';

        $mailPayload = [
            'subject' => 'Repair / service enquiry from website',
            'heading' => 'Repair & servicing enquiry',
            'greetingName' => 'Team',
            'introLines' => ['A visitor submitted the repairs page enquiry form.'],
            'details' => [
                'Name' => $this->name,
                'Email' => $this->email,
                'Phone' => $this->phone,
                'Branch' => $branchName,
                'Service interest' => $this->selectedService,
                'Registration' => $reg ?: 'Not given',
                'Make' => $this->make ?: 'Not given',
                'Model' => $this->model ?: 'Not given',
                'Message' => $this->description,
            ],
            'outroLines' => [],
        ];

        try {
            Mail::send('emails.templates.universal', $mailPayload, function ($message) use ($mailPayload) {
                $message->to('customerservice@neguinhomotors.co.uk')->subject($mailPayload['subject']);
            });
        } catch (\Throwable $e) {
            report($e);
        }

        session()->flash('success', 'Repair enquiry received! We will contact you shortly.');
        $this->resetValidation();
        $this->reset([
            'name',
            'email',
            'phone',
            'selectedBranch',
            'selectedService',
            'description',
            'regNo',
            'make',
            'model',
        ]);
        $this->formNonce++;
    }

    public function render()
    {
        return view('livewire.site.repairs.index')
            ->layout('components.layouts.public', [
                'title' => 'NGN Motorcycle Repairs - Motorcycle Rentals, Sale in UK',
                'description' => 'Discover NGN, your premier destination in the UK for motorcycle repairs, rentals, and accessories. Located in Catford, Sutton and Tooting, we offer a wide range of services to meet all your motorcycling needs.',
            ]);
    }
}
