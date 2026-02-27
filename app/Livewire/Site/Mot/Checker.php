<?php

namespace App\Livewire\Site\Mot;

use Livewire\Component;

class Checker extends Component
{
    public $regNo = '';
    public $motData = null;
    public $error = null;

    protected $rules = [
        'regNo' => 'required|string|min:2|max:10',
    ];

    public function checkMOT()
    {
        $this->validate();
        $this->regNo = strtoupper(str_replace(' ', '', $this->regNo));
        $this->motData = [
            'registration' => $this->regNo,
            'mot_status'   => 'pending',
            'mot_expiry'   => 'Checking with DVLA...',
        ];
        $this->dispatch('mot-checked', regNo: $this->regNo);
    }

    public function render()
    {
        return view('livewire.site.mot.checker');
    }
}
