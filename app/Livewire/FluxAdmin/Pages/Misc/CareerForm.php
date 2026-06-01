<?php

namespace App\Livewire\FluxAdmin\Pages\Misc;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnCareer;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Career — Flux Admin')]
class CareerForm extends Component
{
    use WithAuthorization;

    public ?NgnCareer $career = null;

    public array $form = [];

    public function mount(?NgnCareer $career = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->career = $career?->id ? $career : null;

        if ($this->career) {
            $attrs = $this->career->getAttributes();
            foreach (['job_posted', 'expire_date'] as $k) {
                if (! empty($attrs[$k])) {
                    $attrs[$k] = Carbon::parse($attrs[$k])->format('Y-m-d');
                }
            }
            $this->form = $attrs;
        } else {
            $this->form = ['is_active' => true, 'job_posted' => now()->toDateString()];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.job_title'        => ['required', 'string', 'max:255'],
            'form.description'      => ['nullable', 'string'],
            'form.employment_type'  => ['nullable', 'string', 'max:120'],
            'form.location'         => ['nullable', 'string', 'max:255'],
            'form.salary'           => ['nullable', 'string', 'max:120'],
            'form.contact_email'    => ['nullable', 'email', 'max:255'],
            'form.job_posted'       => ['nullable', 'date'],
            'form.expire_date'      => ['nullable', 'date'],
            'form.is_active'        => ['boolean'],
        ]);

        $payload = [
            'job_title'       => $this->form['job_title'],
            'description'     => $this->form['description'] ?? null,
            'employment_type' => $this->form['employment_type'] ?? null,
            'location'        => $this->form['location'] ?? null,
            'salary'          => $this->form['salary'] ?? null,
            'contact_email'   => $this->form['contact_email'] ?? null,
            'job_posted'      => $this->form['job_posted'] ?: null,
            'expire_date'     => $this->form['expire_date'] ?: null,
            'is_active'       => (bool) ($this->form['is_active'] ?? true),
        ];

        if ($this->career) {
            $this->career->update($payload);
        } else {
            NgnCareer::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Job posting saved.');
        $this->redirect(route('flux-admin.careers.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.misc.career-form');
    }
}
