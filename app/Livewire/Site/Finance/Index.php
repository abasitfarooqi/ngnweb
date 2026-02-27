<?php

namespace App\Livewire\Site\Finance;

use Livewire\Component;

class Index extends Component
{
    public $loanAmount = 3000;
    public $deposit = 500;
    public $term = 36;
    public $monthlyPayment = 0;

    public $name = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $employmentStatus = '';
    public $monthlyIncome = '';
    public $bikeInterest = '';
    public $agreedTerms = false;

    protected $rules = [
        'name'             => 'required|string|min:2',
        'email'            => 'required|email',
        'phone'            => 'required|string|min:10',
        'address'          => 'required|string|min:10',
        'employmentStatus' => 'required|string',
        'monthlyIncome'    => 'required|numeric|min:800',
        'bikeInterest'     => 'nullable|string',
        'agreedTerms'      => 'accepted',
    ];

    public function mount()
    {
        $this->calculatePayment();
    }

    public function updated($field)
    {
        if (in_array($field, ['loanAmount', 'deposit', 'term'])) {
            $this->calculatePayment();
        }
    }

    public function calculatePayment()
    {
        $principal   = max(0, $this->loanAmount - $this->deposit);
        $apr         = 9.9;
        $monthlyRate = ($apr / 100) / 12;

        if ($monthlyRate > 0 && $this->term > 0) {
            $this->monthlyPayment = round(
                $principal * ($monthlyRate * pow(1 + $monthlyRate, $this->term)) /
                (pow(1 + $monthlyRate, $this->term) - 1),
                2
            );
        } else {
            $this->monthlyPayment = 0;
        }
    }

    public function submitApplication()
    {
        $this->validate();
        session()->flash('success', 'Finance application received! We will contact you within 24 hours.');
        $this->reset(['name', 'email', 'phone', 'address', 'employmentStatus', 'monthlyIncome', 'bikeInterest', 'agreedTerms']);
    }

    public function render()
    {
        return view('livewire.site.finance.index')
            ->layout('components.layouts.public', [
                'title' => 'Motorcycle Finance London | Flexible Payment Plans | NGN Motors',
                'description' => 'Flexible motorcycle finance options in London. Apply online today.',
            ]);
    }
}
