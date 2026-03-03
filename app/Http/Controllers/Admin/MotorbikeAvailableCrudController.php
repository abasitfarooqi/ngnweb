<?php

namespace App\Http\Controllers\Admin;

use App\Models\Motorbike;
use App\Models\MotorbikeAnnualCompliance;
use App\Models\RentingPricing;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MotorbikeAvailableCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(Motorbike::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/motorbike-available');
        CRUD::setEntityNameStrings('motorbike available', 'motorbike availables');
    }

    protected function setupListOperation()
    {
        $this->crud->query->select('motorbikes.*');

        // Joins
        $this->crud->query->leftJoin('motorbike_annual_compliance as mac', 'mac.motorbike_id', '=', 'motorbikes.id');
        $this->crud->query->leftJoin('renting_pricings as rp', function ($join) {
            $join->on('rp.motorbike_id', '=', 'motorbikes.id');
        });
        $this->crud->query->leftJoin('renting_booking_items as rbi', function ($join) {
            $join->on('rbi.motorbike_id', '=', 'motorbikes.id')
                ->where('rbi.is_posted', true);
        });

        $this->crud->query->addSelect([
            'motorbikes.vehicle_profile_id',
            'mac.mot_status',
            'mac.road_tax_status',
            'rbi.end_date as end_date',
            'rbi.is_posted as is_posted',
            'rp.weekly_price',
            'rp.iscurrent',
        ]);

        // Filters

        // Filter by vehicle_profile_id (select filter)
        $this->crud->addFilter([
            'name' => 'vehicle_profile_id',
            'type' => 'select2',
            'label' => 'Vehicle Profile',
        ], function () {
            return \App\Models\VehicleProfile::pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'motorbikes.vehicle_profile_id', $value);
        });

        // Filter by mot_status (select filter)
        $this->crud->addFilter([
            'name' => 'mot_status',
            'type' => 'dropdown',
            'label' => 'MOT Status',
        ], [
            'Valid' => 'Valid',
            'No details held by DVLA' => 'No details held by DVLA',
            'Expired' => 'Expired',
        ], function ($value) {
            $this->crud->addClause('where', 'mac.mot_status', $value);
        });

        // Filter by road_tax_status (select filter)
        $this->crud->addFilter([
            'name' => 'road_tax_status',
            'type' => 'dropdown',
            'label' => 'Road Tax Status',
        ], [
            'Taxed' => 'Taxed',
            'SORN' => 'SORN',
            'No details held by DVLA' => 'No details held by DVLA',
        ], function ($value) {
            $this->crud->addClause('where', 'mac.road_tax_status', $value);
        });

        // Filter by booking end date (date range)
        $this->crud->addFilter([
            'name' => 'end_date',
            'type' => 'date_range',
            'label' => 'Booking End Date',
        ], false, function ($value) {
            $dates = json_decode($value);
            if ($dates->from) {
                $this->crud->addClause('where', 'rbi.end_date', '>=', $dates->from);
            }
            if ($dates->to) {
                $this->crud->addClause('where', 'rbi.end_date', '<=', $dates->to);
            }
        });

        // Filter by booking is posted (boolean)
        $this->crud->addFilter([
            'name' => 'is_posted',
            'type' => 'dropdown',
            'label' => 'Booking Is Posted',
        ], [
            1 => 'Yes',
            0 => 'No',
        ], function ($value) {
            $this->crud->addClause('where', 'rbi.is_posted', (bool) $value);
        });

        // Filter by weekly price (range)
        $this->crud->addFilter(
            [
                'type' => 'range',
                'name' => 'weekly_price',
                'label' => 'Weekly Price (£)',
                'label_from' => 'Min £',
                'label_to' => 'Max £',
            ],
            false,
            function ($value) {
                $range = json_decode($value);
                if ($range->from !== null) {
                    $this->crud->addClause('where', 'rp.weekly_price', '>=', $range->from);
                }
                if ($range->to !== null) {
                    $this->crud->addClause('where', 'rp.weekly_price', '<=', $range->to);
                }
            }
        );

        // Filter by iscurrent (boolean)
        $this->crud->addFilter([
            'name' => 'iscurrent',
            'type' => 'dropdown',
            'label' => 'Is Current Pricing',
        ], [
            1 => 'Yes',
            0 => 'No',
        ], function ($value) {
            $this->crud->addClause('where', 'rp.iscurrent', (bool) $value);
        });

        $this->crud->addColumn([
            'name' => 'reg_no',
            'label' => 'Reg No',
            'type' => 'text',
        ]);

        // Your existing columns here...
        $this->crud->addColumn([
            'name' => 'vehicle_profile_id',
            'label' => 'Vehicle Profile ID',
            'type' => 'number',
            'hint' => 'Must be 1 for the bike to be eligible for active renting',
        ]);
        $this->crud->addColumn([
            'name' => 'mot_status',
            'label' => 'MOT Status',
            'type' => 'text',
            'hint' => 'Must be Valid or No details held by DVLA for the bike to be eligible',
        ]);
        $this->crud->addColumn([
            'name' => 'road_tax_status',
            'label' => 'Road Tax Status',
            'type' => 'text',
            'hint' => 'Must be Taxed for the bike to be eligible',
        ]);
        $this->crud->addColumn([
            'name' => 'end_date',
            'label' => 'Booking End Date',
            'type' => 'date',
            'default' => '-',
            'hint' => 'No active booking if end_date is NULL, making the bike eligible',
        ]);
        $this->crud->addColumn([
            'name' => 'is_posted',
            'label' => 'Booking Is Posted',
            'type' => 'boolean',
            'hint' => 'No active booking if is_posted is true and end_date is NULL, making the bike eligible',
        ]);
        $this->crud->addColumn([
            'name' => 'weekly_price',
            'label' => 'Weekly Price (£)',
            'type' => 'number',
            'decimals' => 2,
            'prefix' => '£',
            'default' => '-',
        ]);
        $this->crud->addColumn([
            'name' => 'iscurrent',
            'label' => 'Is Current Pricing',
            'type' => 'boolean',
            'default' => 'No',
            'hint' => 'Pricing must be current (RP.iscurrent = true) for the bike to be eligible',
        ]);
    }

    protected function setupUpdateOperation()
    {
        $id = $this->crud->getCurrentEntryId() ?? request()->route('id');
        $motorbike = Motorbike::with([
            'annualCompliances' => fn ($q) => $q->latest()->limit(1),
            'rentingPricings' => fn ($q) => $q->where('iscurrent', true)->limit(1),
            'rentingBookingItems' => fn ($q) => $q->where('is_posted', true)->limit(1),
        ])->findOrFail($id);

        $mac = $motorbike->annualCompliances->first();
        $rp = $motorbike->rentingPricings->first();
        $rbi = $motorbike->rentingBookingItems->first();

        $this->crud->addField([
            'name' => 'vehicle_profile_id',
            'label' => 'Vehicle Profile',
            'type' => 'select2',
            'entity' => 'vehicleProfile',
            'attribute' => 'name',
            'model' => "App\Models\VehicleProfile",
            'default' => $motorbike->vehicle_profile_id,
            'hint' => 'Must be 1 for the bike to be eligible for active renting',
        ]);

        $this->crud->addField([
            'name' => 'reg_no',
            'label' => 'Reg No',
            'type' => 'text',
            'attributes' => ['readonly' => 'readonly'],
            'default' => $motorbike->reg_no,
        ]);

        $this->crud->addField([
            'name' => 'mot_status',
            'label' => 'MOT Status',
            'type' => 'select_from_array',
            'options' => [
                'Valid' => 'Valid',
                'No details held by DVLA' => 'No details held by DVLA',
                'Expired' => 'Expired',
            ],
            'default' => $mac->mot_status ?? null,
            'hint' => 'Must be Valid or No details held by DVLA for the bike to be eligible',
        ]);

        $this->crud->addField([
            'name' => 'road_tax_status',
            'label' => 'Road Tax Status',
            'type' => 'select_from_array',
            'options' => [
                'Taxed' => 'Taxed',
                'SORN' => 'SORN',
                'No details held by DVLA' => 'No details held by DVLA',
            ],
            'default' => $mac->road_tax_status ?? null,
            'hint' => 'Must be Taxed for the bike to be eligible',
        ]);

        $this->crud->addField([
            'name' => 'end_date',
            'label' => 'Booking End Date',
            'type' => 'date',
            'default' => $rbi->end_date ?? null,
            'hint' => 'No active booking if end_date is NULL, making the bike eligible',
            // Remove 'readonly' if you want user to edit it
            // 'attributes' => ['readonly' => 'readonly'],
        ]);

        $this->crud->addField([
            'name' => 'is_posted',
            'label' => 'Booking Is Posted',
            'type' => 'checkbox',
            'default' => $rbi->is_posted ?? false,
            'hint' => 'No active booking if is_posted is true and end_date is NULL, making the bike eligible',
            // Remove 'readonly' here as well to allow checking/unchecking
            // 'attributes' => ['readonly' => 'readonly'],
        ]);

        $this->crud->addField([
            'name' => 'weekly_price',
            'label' => 'Weekly Price (£)',
            'type' => 'number',
            'attributes' => ['step' => 'any'],
            'default' => $rp->weekly_price ?? null,
        ]);

        $this->crud->addField([
            'name' => 'iscurrent',
            'label' => 'Is Current Pricing',
            'type' => 'checkbox',
            'default' => $rp->iscurrent ?? false,
            'hint' => 'Pricing must be current (RP.iscurrent = true) for the bike to be eligible',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'reg_no' => 'required|string|max:255',
            'mot_status' => 'required|string|in:Valid,No details held by DVLA,Expired',
            'road_tax_status' => 'required|string|in:Taxed,SORN,No details held by DVLA',
            'weekly_price' => 'required|numeric|min:0',
            'iscurrent' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request, $id) {
            // Update motorbike reg_no
            $motorbike = Motorbike::findOrFail($id);
            $motorbike->reg_no = $request->reg_no;
            $motorbike->vehicle_profile_id = $request->vehicle_profile_id; // Update if needed
            $motorbike->save();

            // Update or create motorbike_annual_compliance
            MotorbikeAnnualCompliance::updateOrCreate(
                ['motorbike_id' => $id],
                [
                    'mot_status' => $request->mot_status,
                    'road_tax_status' => $request->road_tax_status,
                    'updated_at' => now(),
                ]
            );

            // Update renting_pricings
            if (Schema::hasColumn('renting_pricings', 'weekly_price') && Schema::hasColumn('renting_pricings', 'iscurrent')) {
                RentingPricing::where('motorbike_id', $id)
                    ->where('iscurrent', true)
                    ->update([
                        'weekly_price' => $request->weekly_price,
                        'iscurrent' => $request->iscurrent ?? 1,
                        'updated_at' => now(),
                    ]);
            }

            // If you want to update renting_booking_items as well, you may add here
            // e.g.
            /*
            $rbi = RentingBookingItem::where('motorbike_id', $id)->where('is_posted', true)->first();
            if ($rbi) {
                $rbi->end_date = $request->end_date;
                $rbi->is_posted = $request->is_posted ?? false;
                $rbi->save();
            }
            */
        });

        \Alert::success('Motorbike updated successfully')->flash();

        return redirect()->back();
    }
}
