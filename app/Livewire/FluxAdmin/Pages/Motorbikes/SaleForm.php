<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Motorbike;
use App\Models\MotorbikesSale;
use App\Support\NgnMotorcycleImage;
use Mews\Purifier\Facades\Purifier;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('flux-admin.layouts.app')]
#[Title('Motorbike Sale — Flux Admin')]
class SaleForm extends Component
{
    use WithAuthorization;
    use WithFileUploads;

    public ?MotorbikesSale $motorbikesSale = null;

    public array $form = [];

    public string $motorbikeSearch = '';

    public array $motorbikeSuggestions = [];

    public $imageOneUpload = null;

    public $imageTwoUpload = null;

    public $imageThreeUpload = null;

    public $imageFourUpload = null;

    public function mount(?MotorbikesSale $motorbikesSale = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->motorbikesSale = $motorbikesSale;

        if ($motorbikesSale && $motorbikesSale->exists) {
            $this->fillFormFromSale($motorbikesSale);
            $this->motorbikeSearch = $motorbikesSale->motorbike?->reg_no ?? '';
        } else {
            $this->form = [
                'motorbike_id' => null,
                'condition' => 'USED',
                'is_sold' => false,
                'is_rented' => false,
                'v5_available' => false,
                'buyer_name' => null,
                'buyer_phone' => null,
                'buyer_email' => null,
                'buyer_address' => null,
                'mileage' => null,
                'price' => null,
                'date_of_purchase' => now()->toDateString(),
                'date_of_sale' => now()->toDateString(),
                'engine' => 'NOT CHECKED',
                'suspension' => 'NOT CHECKED',
                'brakes' => 'NOT CHECKED',
                'belt' => 'NOT CHECKED',
                'electrical' => 'NOT CHECKED',
                'tires' => 'NOT CHECKED',
                'accessories' => null,
                'note' => null,
                'image_one' => null,
                'image_two' => null,
                'image_three' => null,
                'image_four' => null,
            ];
        }
    }

    protected function fillFormFromSale(MotorbikesSale $sale): void
    {
        // Use cast values — raw getAttributes() breaks checkbox truthiness (0/"0"/1).
        $this->form = [
            'motorbike_id' => $sale->motorbike_id,
            'condition' => $sale->condition ?: 'USED',
            'is_sold' => (bool) $sale->is_sold,
            'is_rented' => (bool) ($sale->is_rented ?? false),
            'v5_available' => (bool) $sale->v5_available,
            'buyer_name' => $sale->buyer_name,
            'buyer_phone' => $sale->buyer_phone,
            'buyer_email' => $sale->buyer_email,
            'buyer_address' => $sale->buyer_address,
            'mileage' => $sale->mileage,
            'price' => $sale->price,
            'date_of_purchase' => $sale->date_of_purchase ? Carbon::parse($sale->date_of_purchase)->format('Y-m-d') : null,
            'date_of_sale' => $sale->date_of_sale ? Carbon::parse($sale->date_of_sale)->format('Y-m-d') : null,
            'engine' => $sale->engine ?: 'NOT CHECKED',
            'suspension' => $sale->suspension ?: 'NOT CHECKED',
            'brakes' => $sale->brakes ?: 'NOT CHECKED',
            'belt' => $sale->belt ?: 'NOT CHECKED',
            'electrical' => $sale->electrical ?: 'NOT CHECKED',
            'tires' => $sale->tires ?: 'NOT CHECKED',
            'accessories' => $sale->accessories,
            'note' => $sale->note,
            'image_one' => $sale->image_one,
            'image_two' => $sale->image_two,
            'image_three' => $sale->image_three,
            'image_four' => $sale->image_four,
        ];
    }

    public function updatingMotorbikeSearch(): void
    {
        if (strlen($this->motorbikeSearch) < 2) {
            $this->motorbikeSuggestions = [];

            return;
        }

        $query = Motorbike::query()
            ->where('reg_no', 'like', '%'.$this->motorbikeSearch.'%');

        // Match Backpack create: exclude bikes already on a finance application.
        if (! ($this->motorbikesSale && $this->motorbikesSale->exists)) {
            $query->whereNotIn('id', function ($sub) {
                $sub->select('motorbike_id')
                    ->from('application_items')
                    ->whereNotNull('motorbike_id');
            });
        }

        $this->motorbikeSuggestions = $query
            ->limit(8)
            ->get(['id', 'reg_no'])
            ->map(fn ($m) => [
                'id' => $m->id,
                'reg' => $m->reg_no,
            ])->toArray();
    }

    public function selectMotorbike(int $id, string $reg): void
    {
        $this->form['motorbike_id'] = $id;
        $this->motorbikeSearch = $reg;
        $this->motorbikeSuggestions = [];
    }

    public function commitMotorbikeSearch(): void
    {
        if (! empty($this->form['motorbike_id'])) {
            return;
        }

        if ($this->motorbikeSuggestions === [] && strlen($this->motorbikeSearch) >= 2) {
            $this->updatingMotorbikeSearch();
        }

        if ($this->motorbikeSuggestions === []) {
            return;
        }

        $compact = strtoupper(preg_replace('/\s+/', '', $this->motorbikeSearch) ?? '');
        foreach ($this->motorbikeSuggestions as $suggestion) {
            $reg = strtoupper(preg_replace('/\s+/', '', (string) ($suggestion['reg'] ?? '')) ?? '');
            if ($compact !== '' && $reg === $compact) {
                $this->selectMotorbike((int) $suggestion['id'], (string) $suggestion['reg']);

                return;
            }
        }

        if (count($this->motorbikeSuggestions) === 1) {
            $first = $this->motorbikeSuggestions[0];
            $this->selectMotorbike((int) $first['id'], (string) $first['reg']);
        }
    }

    public function removeExistingImage(string $field): void
    {
        if (! in_array($field, ['image_one', 'image_two', 'image_three', 'image_four'], true)) {
            return;
        }
        $this->form[$field] = null;
    }

    public function save(): void
    {
        $this->commitMotorbikeSearch();

        $this->form['is_sold'] = (bool) ($this->form['is_sold'] ?? false);
        $this->form['is_rented'] = (bool) ($this->form['is_rented'] ?? false);
        $this->form['v5_available'] = (bool) ($this->form['v5_available'] ?? false);
        $this->form['condition'] = 'USED';

        $this->validate([
            'form.motorbike_id' => ['required', 'integer'],
            'form.condition' => ['required', 'string', 'max:120'],
            'form.mileage' => ['nullable', 'numeric'],
            'form.date_of_purchase' => ['nullable', 'date'],
            'form.date_of_sale' => ['nullable', 'date'],
            'form.price' => ['nullable', 'numeric'],
            'form.engine' => ['nullable', 'string', 'max:255'],
            'form.suspension' => ['nullable', 'string', 'max:255'],
            'form.brakes' => ['nullable', 'string', 'max:255'],
            'form.belt' => ['nullable', 'string', 'max:255'],
            'form.electrical' => ['nullable', 'string', 'max:255'],
            'form.tires' => ['nullable', 'string', 'max:255'],
            'form.accessories' => ['nullable', 'string'],
            'form.note' => ['nullable', 'string'],
            'form.is_sold' => ['boolean'],
            'form.is_rented' => ['boolean'],
            'form.buyer_name' => ['nullable', 'string', 'max:255'],
            'form.buyer_phone' => ['nullable', 'string', 'max:50'],
            'form.buyer_email' => ['nullable', 'email', 'max:255'],
            'form.buyer_address' => ['nullable', 'string', 'max:500'],
            'form.v5_available' => ['boolean'],
            'imageOneUpload' => ['nullable', 'image', 'max:8192'],
            'imageTwoUpload' => ['nullable', 'image', 'max:8192'],
            'imageThreeUpload' => ['nullable', 'image', 'max:8192'],
            'imageFourUpload' => ['nullable', 'image', 'max:8192'],
        ]);

        if (! $this->form['is_sold']) {
            $this->form['buyer_name'] = null;
            $this->form['buyer_phone'] = null;
            $this->form['buyer_email'] = null;
            $this->form['buyer_address'] = null;
        }

        $data = [
            'motorbike_id' => $this->form['motorbike_id'] ?? null,
            'condition' => 'USED',
            'mileage' => $this->form['mileage'] ?? 0,
            'date_of_purchase' => ($this->form['date_of_purchase'] ?? null) ?: now()->toDateString(),
            'date_of_sale' => ($this->form['date_of_sale'] ?? null) ?: now()->toDateString(),
            'price' => $this->form['price'] ?? 0,
            'engine' => ($this->form['engine'] ?? null) ?: 'NOT CHECKED',
            'suspension' => ($this->form['suspension'] ?? null) ?: 'NOT CHECKED',
            'brakes' => ($this->form['brakes'] ?? null) ?: 'NOT CHECKED',
            'belt' => ($this->form['belt'] ?? null) ?: 'NOT CHECKED',
            'electrical' => ($this->form['electrical'] ?? null) ?: 'NOT CHECKED',
            'tires' => ($this->form['tires'] ?? null) ?: 'NOT CHECKED',
            'accessories' => self::cleanAccessories($this->form['accessories'] ?? null),
            'note' => $this->form['note'] ?? '',
            'is_sold' => $this->form['is_sold'],
            'is_rented' => $this->form['is_rented'],
            'buyer_name' => $this->form['buyer_name'] ?? null,
            'buyer_phone' => $this->form['buyer_phone'] ?? null,
            'buyer_email' => $this->form['buyer_email'] ?? null,
            'buyer_address' => $this->form['buyer_address'] ?? null,
            'v5_available' => $this->form['v5_available'],
            'user_id' => auth()->id(),
            'image_one' => $this->form['image_one'] ?? null,
            'image_two' => $this->form['image_two'] ?? null,
            'image_three' => $this->form['image_three'] ?? null,
            'image_four' => $this->form['image_four'] ?? null,
        ];

        foreach ([
            'image_one' => $this->imageOneUpload,
            'image_two' => $this->imageTwoUpload,
            'image_three' => $this->imageThreeUpload,
            'image_four' => $this->imageFourUpload,
        ] as $field => $upload) {
            if ($upload) {
                $data[$field] = $upload->store('', 'used_motorbikes');
            }
        }

        if ($this->motorbikesSale && $this->motorbikesSale->exists) {
            $this->motorbikesSale->update($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Sale updated.');
        } else {
            $this->motorbikesSale = MotorbikesSale::create($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Sale created.');
        }

        if (! empty($data['motorbike_id']) && ! empty($data['is_rented'])) {
            Motorbike::whereKey($data['motorbike_id'])->update(['vehicle_profile_id' => 1]);
        }

        $this->redirect(route('flux-admin.motorbike-sales.index'), navigate: true);
    }

    private static function cleanAccessories(?string $raw): ?string
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return null;
        }

        $clean = trim(Purifier::clean($raw, 'motorbike_accessories'));

        return $clean !== '' ? $clean : null;
    }

    public function currentImageUrl(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        return NgnMotorcycleImage::urlForUsedSale($path);
    }

    public function render()
    {
        return view('flux-admin.pages.motorbikes.sale-form');
    }
}
