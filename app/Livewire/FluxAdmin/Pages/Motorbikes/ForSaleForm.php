<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Motorcycle;
use App\Support\MotorbikeMediaStorage;
use App\Support\NgnMotorcycleImage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('flux-admin.layouts.app')]
class ForSaleForm extends Component
{
    use WithAuthorization;
    use WithFileUploads;

    public ?Motorcycle $motorcycle = null;

    public array $form = [];

    public $imageUpload = null;

    public function mount(?Motorcycle $motorcycle = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->motorcycle = $motorcycle;

        if ($motorcycle && $motorcycle->exists) {
            $this->form = $motorcycle->only([
                'make', 'model', 'year', 'colour', 'engine', 'type',
                'sale_new_price', 'description', 'availability',
                'file_path', 'file_name', 'category',
            ]);
        } else {
            $this->form = [
                'availability' => 'for sale',
                'category' => 'new for sale',
            ];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.make' => ['required', 'string', 'max:255'],
            'form.model' => ['required', 'string', 'max:255'],
            'form.year' => ['nullable', 'string', 'max:4'],
            'form.colour' => ['nullable', 'string', 'max:255'],
            'form.engine' => ['nullable', 'string', 'max:255'],
            'form.type' => ['nullable', 'string', 'in:Scooter,Standard,Super Sport,Touring,Other'],
            'form.sale_new_price' => ['nullable', 'numeric', 'min:0'],
            'form.description' => ['nullable', 'string'],
            'form.availability' => ['nullable', 'string', 'in:for sale,sold,reserved'],
            'imageUpload' => ['nullable', 'image', 'max:8192'],
        ];
    }

    public function removeExistingImage(): void
    {
        $this->form['file_path'] = null;
        $this->form['file_name'] = null;
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];
        $payload['category'] = $payload['category'] ?? ($this->form['category'] ?? 'new for sale');
        $payload['availability'] = $payload['availability'] ?? 'for sale';

        if ($this->imageUpload) {
            $payload['file_path'] = MotorbikeMediaStorage::putUploadedFile($this->imageUpload);
            $payload['file_name'] = $this->imageUpload->getClientOriginalName();
        } else {
            $payload['file_path'] = $this->form['file_path'] ?? null;
            $payload['file_name'] = $this->form['file_name'] ?? null;
        }

        if ($this->motorcycle && $this->motorcycle->exists) {
            $this->motorcycle->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Listing updated.');
        } else {
            Motorcycle::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Listing created.');
        }

        $this->redirect(route('flux-admin.motorbike-for-sale.index'), navigate: true);
    }

    public function currentImageUrl(): ?string
    {
        $path = $this->form['file_path'] ?? null;
        if ($path === null || trim((string) $path) === '') {
            return null;
        }

        return NgnMotorcycleImage::urlForNewStock((string) $path);
    }

    public function render()
    {
        return view('flux-admin.pages.motorbikes.for-sale-form');
    }
}
