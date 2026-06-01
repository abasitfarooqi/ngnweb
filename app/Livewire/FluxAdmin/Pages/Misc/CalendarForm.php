<?php

namespace App\Livewire\FluxAdmin\Pages\Misc;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Calander;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Calendar Event — Flux Admin')]
class CalendarForm extends Component
{
    use WithAuthorization;

    public ?Calander $calendarEvent = null;

    public array $form = [];

    public function mount(?Calander $calendarEvent = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->calendarEvent = $calendarEvent?->id ? $calendarEvent : null;

        if ($this->calendarEvent) {
            $attrs = $this->calendarEvent->getAttributes();
            foreach (['start', 'end'] as $k) {
                if (! empty($attrs[$k])) {
                    $attrs[$k] = Carbon::parse($attrs[$k])->format('Y-m-d\TH:i');
                }
            }
            $this->form = $attrs;
        } else {
            $this->form = ['start' => now()->format('Y-m-d\TH:i'), 'background_color' => '#2563eb', 'text_color' => '#ffffff'];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.title'            => ['required', 'string', 'max:255'],
            'form.start'            => ['required', 'date'],
            'form.end'              => ['nullable', 'date', 'after_or_equal:form.start'],
            'form.background_color' => ['nullable', 'string', 'max:20'],
            'form.text_color'       => ['nullable', 'string', 'max:20'],
        ]);

        $payload = [
            'title'            => $this->form['title'],
            'start'            => $this->form['start'],
            'end'              => $this->form['end'] ?: null,
            'background_color' => $this->form['background_color'] ?? null,
            'text_color'       => $this->form['text_color'] ?? null,
        ];

        if ($this->calendarEvent) {
            $this->calendarEvent->update($payload);
        } else {
            Calander::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Event saved.');
        $this->redirect(route('flux-admin.calendar.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.misc.calendar-form');
    }
}
