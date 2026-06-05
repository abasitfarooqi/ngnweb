<?php

namespace App\Livewire\Site\Career;

use App\Models\NgnCareer;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $careers = NgnCareer::orderByDesc('job_posted')->get();

        return view('livewire.site.career.index', compact('careers'))
            ->layout('components.layouts.public', [
                'title' => 'Careers at NGN Motors | Jobs in London',
                'description' => 'Join the NGN Motors team. Motorcycle mechanics, sales, and customer service roles in London.',
            ]);
    }
}
