<?php

namespace App\Livewire\FluxAdmin\Pages\Access;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\RentingServiceVideo;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class ServiceVideoForm extends Component
{
    use WithAuthorization;

    public ?RentingServiceVideo $serviceVideo = null;

    public array $form = [];

    public function mount(?RentingServiceVideo $serviceVideo = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-renting-page');
        $this->serviceVideo = $serviceVideo;

        if ($serviceVideo && $serviceVideo->exists) {
            $attrs = $serviceVideo->getAttributes();
            if (! empty($attrs['recorded_at'])) {
                try {
                    $attrs['recorded_at'] = Carbon::parse($attrs['recorded_at'])->format('Y-m-d');
                } catch (\Throwable) {
                    $attrs['recorded_at'] = null;
                }
            }
            $this->form = $attrs;
        } else {
            $this->form = ['recorded_at' => now()->toDateString()];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.booking_id'  => ['required', 'integer'],
            'form.video_path'  => ['nullable', 'string', 'max:1000'],
            'form.recorded_at' => ['nullable', 'date'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        if ($this->serviceVideo && $this->serviceVideo->exists) {
            $this->serviceVideo->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Video updated.');
        } else {
            RentingServiceVideo::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Video created.');
        }

        $this->redirect(route('flux-admin.service-videos.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.access.service-video-form');
    }
}
