<?php

namespace App\Livewire\Portal\Rentals;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Browse extends Component
{
    public $selectedBranch = null;

    public $searchQuery = '';

    public $filterType = 'all';

    public function mount()
    {
        $profile = Auth::guard('customer')->user()->customer;
        if ($profile && $profile->preferred_branch_id) {
            $this->selectedBranch = $profile->preferred_branch_id;
        }
    }

    public function selectBranch($branchId)
    {
        $this->selectedBranch = $branchId;
    }

    public function setFilter($type)
    {
        $this->filterType = $type;
    }

    public function getAvailableMotorbikes()
    {
        Log::info('Portal: Available Motorbikes Requested.');

        try {
            $motorbikes = DB::table('motorbikes as MB')
                ->leftJoin('branches as BR', 'MB.branch_id', '=', 'BR.id')
                ->join('motorbike_registrations as MR', 'MB.id', '=', 'MR.motorbike_id')
                ->leftJoin('motorbike_annual_compliance as MC', 'MC.motorbike_id', '=', 'MB.id')
                ->rightJoin('renting_pricings as RP', 'RP.motorbike_id', '=', 'MB.id')
                ->select(
                    'MB.id as id', 'MB.make as make', 'MB.model as model', 'MB.year as year',
                    'MB.engine as engine', 'MB.branch_id as branch_id', 'MB.color as color',
                    'MB.is_ebike as is_ebike', 'MR.registration_number as reg_no',
                    'BR.name as branch_name',
                    'RP.weekly_price as weekly_price',
                    DB::raw("CONCAT(COALESCE(MC.mot_status,''), IFNULL(CONCAT(' ', MC.mot_due_date), '')) as mot_status"),
                    DB::raw("CONCAT(COALESCE(MC.road_tax_status,''), IFNULL(CONCAT(' ', MC.tax_due_date), '')) as road_tax_status"),
                    'MC.road_tax_status as road_tax_status_flag', 'MC.insurance_status as insurance_status',
                )
                ->where('MB.vehicle_profile_id', 1)
                ->where('RP.iscurrent', true)
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('renting_booking_items')
                        ->whereColumn('renting_booking_items.motorbike_id', 'MB.id')
                        ->where('renting_booking_items.is_posted', true)
                        ->whereNull('renting_booking_items.end_date');
                })
                ->where(function ($q) {
                    $q->where('MB.is_ebike', true)
                        ->orWhere(function ($q2) {
                            $q2->where('MB.is_ebike', false)
                                ->where('MC.road_tax_status', 'Taxed')
                                ->where(function ($q3) {
                                    $q3->where('MC.mot_status', 'Valid')
                                        ->orWhere('MC.mot_status', 'No details held by DVLA');
                                });
                        });
                });

            if ($this->selectedBranch) {
                $motorbikes->where('MB.branch_id', $this->selectedBranch);
            }

            if ($this->filterType !== 'all') {
                if ($this->filterType === 'scooter') {
                    $motorbikes->where('MB.engine', '<=', 125);
                }
                if ($this->filterType === 'motorbike') {
                    $motorbikes->where('MB.engine', '>', 125);
                }
            }

            if ($this->searchQuery) {
                $search = $this->searchQuery;
                $motorbikes->where(function ($q) use ($search) {
                    $q->where('MB.make', 'like', '%'.$search.'%')
                        ->orWhere('MB.model', 'like', '%'.$search.'%')
                        ->orWhere('MR.registration_number', 'like', '%'.$search.'%');
                });
            }

            return $motorbikes->orderBy('MB.make')->orderBy('MB.model')->get();
        } catch (\Exception $e) {
            Log::error('Browse: Error fetching available motorbikes: '.$e->getMessage());

            return collect();
        }
    }

    public function render()
    {
        return view('livewire.portal.rentals.browse', [
            'branches' => Branch::all(),
            'motorbikes' => $this->getAvailableMotorbikes(),
        ])->layout('components.layouts.portal');
    }
}
