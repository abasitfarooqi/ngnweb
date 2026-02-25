<?php

namespace App\Livewire;

use App\Models\Motorbike;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class MotorcycleList extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'make'; // Default sorting field

    public $sortDirection = 'asc'; // Default sorting direction

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'make'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage(); // Reset page whenever sorting changes
    }

    public function render()
    {
        $motorbikes = Motorbike::query()
            ->join('motorbike_registrations as MR', 'motorbikes.id', '=', 'MR.motorbike_id')
            ->join('motorbike_annual_compliance as MC', 'MC.motorbike_id', '=', 'motorbikes.id')
            ->join('motorbikes_sale as MS', 'motorbikes.id', '=', 'MS.motorbike_id')
            ->select(
                'MS.belt as BELT',
                'MS.condition as CONDITION',
                'MS.motorbike_id as MOTORBIKE_ID',
                'MS.mileage as MILEAGE',
                'MS.price as PRICE',
                'MS.engine as ENGINE_CONDITION',
                'MS.suspension as SUSPENSION',
                'MS.brakes as BRAKES',
                'MS.electrical as ELECTRICAL',
                'MS.tires as TIRES',
                'MS.id as ITEM_SALE_ID',
                'MS.note as NOTE',
                'MS.image_one as IMAGE',
                'motorbikes.id as MOTORBIKE_ID',
                'motorbikes.make as MAKE',
                'motorbikes.model as MODEL',
                'motorbikes.year as YEAR',
                'motorbikes.engine as ENGINE',
                'motorbikes.color as COLOR',
                'MR.registration_number as REG_NO',
                DB::raw("CONCAT(MC.mot_status, IFNULL(CONCAT(' ', MC.mot_due_date), '')) as MOT_STATUS"),
                DB::raw("CONCAT(MC.road_tax_status, IFNULL(CONCAT(' ', MC.tax_due_date), '')) as ROAD_TAX_STATUS"),
                'MC.road_tax_status as ROAD_TAX_STATUS_FLAG',
                'MC.insurance_status as INSURANCE_STATUS',
                'MS.is_sold as IS_SOLD'
            )
            ->where(function ($query) {
                $query->where('motorbikes.make', 'like', '%'.$this->search.'%')
                    ->orWhere('motorbikes.model', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(20);

        Log::info('Motorbikes fetched:', ['motorbikes' => $motorbikes]);

        return view('livewire.motorcycle-list', [
            'motorbikes' => $motorbikes,
        ]);
    }
}
