<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MotorbikesRequest;
use App\Models\Branch;
use App\Models\Motorbike;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class MotorbikesCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Motorbike::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/motorbikes');
        CRUD::setEntityNameStrings('motorbikes', 'motorbikes');

        // Vehicle Type filter
        CRUD::filter('vehicle_profile_id')
            ->type('dropdown')
            ->label('Vehicle Type')
            ->values([
                1 => 'Internal',
                2 => 'External',
            ]);

        // Registration Number filter
        CRUD::filter('reg_no')
            ->type('text')
            ->label('Registration Number')
            ->whenActive(function ($value) {
                $this->crud->query->where('reg_no', 'LIKE', "%$value%");
            });

        // Year filter
        CRUD::filter('year')
            ->type('range')
            ->label('Year')
            ->whenActive(function ($value) {
                $range = json_decode($value);
                if ($range->from) {
                    $this->crud->query->where('year', '>=', (int) $range->from);
                }
                if ($range->to) {
                    $this->crud->query->where('year', '<=', (int) $range->to);
                }
            });

        // Color filter
        CRUD::filter('color')
            ->type('text')
            ->label('Color')
            ->whenActive(function ($value) {
                $this->crud->query->where('color', 'LIKE', "%$value%");
            });

        // First Registration Date filter
        CRUD::filter('month_of_first_registration')
            ->type('date_range')
            ->label('First Registration')
            ->whenActive(function ($value) {
                $dates = json_decode($value);
                if ($dates->from) {
                    $this->crud->query->whereDate('month_of_first_registration', '>=', $dates->from);
                }
                if ($dates->to) {
                    $this->crud->query->whereDate('month_of_first_registration', '<=', $dates->to);
                }
            });

        // Last V5C Issue Date filter
        CRUD::filter('date_of_last_v5c_issuance')
            ->type('date_range')
            ->label('Last V5C Issue')
            ->whenActive(function ($value) {
                $dates = json_decode($value);
                if ($dates->from) {
                    $this->crud->query->whereDate('date_of_last_v5c_issuance', '>=', $dates->from);
                }
                if ($dates->to) {
                    $this->crud->query->whereDate('date_of_last_v5c_issuance', '<=', $dates->to);
                }
            });

        CRUD::filter('mot_status')
            ->type('dropdown')
            ->label('MOT Status')
            ->values([
                'No details held by DVLA' => 'No details held by DVLA',
                'Valid' => 'Valid',
                'Not valid' => 'Not valid',
            ])
            ->whenActive(function ($value) {
                $this->crud->query->whereHas('annualCompliances', function ($query) use ($value) {
                    $query->where('mot_status', $value)
                        ->whereIn('id', function ($subquery) {
                            $subquery->select(\DB::raw('MAX(id)'))
                                ->from('motorbike_annual_compliance')
                                ->groupBy('motorbike_id');
                        });
                });
            });

        // Road Tax Status filter
        CRUD::filter('road_tax_status')
            ->type('dropdown')
            ->label('Road Tax Status')
            ->values([
                'Taxed' => 'Taxed',
                'Untaxed' => 'Untaxed',
            ])
            ->whenActive(function ($value) {
                $this->crud->query->whereHas('annualCompliances', function ($query) use ($value) {
                    $query->where('road_tax_status', $value)
                        ->whereIn('id', function ($subquery) {
                            $subquery->select(\DB::raw('MAX(id)'))
                                ->from('motorbike_annual_compliance')
                                ->groupBy('motorbike_id');
                        });
                });
            });

        // Tax Due Date filter
        CRUD::filter('tax_due_date')
            ->type('date_range')
            ->label('Tax Due Date')
            ->whenActive(function ($value) {
                $dates = json_decode($value);
                $this->crud->query->whereHas('annualCompliances', function ($query) use ($dates) {
                    if ($dates->from) {
                        $query->whereDate('tax_due_date', '>=', $dates->from);
                    }
                    if ($dates->to) {
                        $query->whereDate('tax_due_date', '<=', $dates->to);
                    }
                    $query->whereIn('id', function ($subquery) {
                        $subquery->select(\DB::raw('MAX(id)'))
                            ->from('motorbike_annual_compliance')
                            ->groupBy('motorbike_id');
                    });
                });
            });

        // MOT Due Date filter
        CRUD::filter('mot_due_date')
            ->type('date_range')
            ->label('MOT Due')
            ->whenActive(function ($value) {
                $dates = json_decode($value);
                $this->crud->query->whereHas('annualCompliances', function ($query) use ($dates) {
                    if ($dates->from) {
                        $query->whereDate('mot_due_date', '>=', $dates->from);
                    }
                    if ($dates->to) {
                        $query->whereDate('mot_due_date', '<=', $dates->to);
                    }
                    $query->whereIn('id', function ($subquery) {
                        $subquery->select(\DB::raw('MAX(id)'))
                            ->from('motorbike_annual_compliance')
                            ->groupBy('motorbike_id');
                    });
                });
            });

        CRUD::enableExportButtons();

    }

    protected function setupListOperation()
    {
        // CRUD::setFromDb();

        CRUD::column('vehicle_profile_id')
            ->label('Vehicle Type')
            ->type('closure')
            ->function(function ($entry) {
                return $entry->vehicle_profile_id == 1 ? 'Internal' : 'External';
            });

        CRUD::column('branch_id')
            ->label('Branch')
            ->type('closure')
            ->function(function ($entry) {
                return $entry->branch ? $entry->branch->name : 'N/A';
            });

        CRUD::filter('vehicle_profile_id')
            ->type('dropdown')
            ->label('Vehicle Type')
            ->values([
                1 => 'Internal',
                2 => 'External',
            ]);

        CRUD::column('reg_no')->label('Registration Number');

        CRUD::column('year')->label('Year');

        CRUD::column('color')->label('Color');

        CRUD::column('mot_status')
            ->label('MOT Status')
            ->type('closure')
            ->function(function ($entry) {
                $compliance = $entry->annualCompliances()->latest()->first();

                return $compliance ? $compliance->mot_status : 'N/A';
            });

        CRUD::column('tax_due_date')
            ->label('Tax Due Date')
            ->type('closure')
            ->function(function ($entry) {
                $compliance = $entry->annualCompliances()->latest()->first();

                return $compliance ? date('d M Y', strtotime($compliance->tax_due_date)) : 'N/A';
            });

        CRUD::column('mot_due')
            ->label('MOT Due')
            ->type('closure')
            ->function(function ($entry) {
                $compliance = $entry->annualCompliances()->latest()->first();

                return $compliance ? date('d M Y', strtotime($compliance->mot_due_date)) : 'N/A';
            });

        CRUD::column('road_tax_status')
            ->label('Road Tax Status')
            ->type('closure')
            ->function(function ($entry) {
                $compliance = $entry->annualCompliances()->latest()->first();

                return $compliance ? $compliance->road_tax_status : 'N/A';
            });

        CRUD::column('date_of_last_v5c_issuance')
            ->label('Last V5C Issue')
            ->type('datetime')
            ->format('D MMM YYYY HH:mm');

        CRUD::column('month_of_first_registration')
            ->label('First Registration')
            ->type('date')
            ->format('D MMM YYYY');

        CRUD::column('fuel_type')->label('Fuel Type');

    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(MotorbikesRequest::class);

        CRUD::setFromDb();

        CRUD::addField([
            'name' => 'vin_number',
            'type' => 'hidden',
            'label' => 'VIN Number',
            'value' => ''.rand(12, 100).'1234567890'.rand(12, 1000).'',
        ]);

        CRUD::addField([
            'name' => 'reg_no',
            'type' => 'text',
            'label' => 'Registration Number',
        ]);

        CRUD::addField([
            'name' => 'year',
            'label' => 'Year',
            'type' => 'number',
            'attributes' => [
                'min' => 1900,
                'max' => 9999,
                'step' => 1,
            ],
        ]);

        //
        CRUD::addField([
            'name' => 'fuel_type',
            'type' => 'hidden',
            'label' => 'Fuel Type',
            'value' => 'Petrol',
        ]);

        CRUD::addField([
            'name' => 'marked_for_export',
            'type' => 'hidden',
            'label' => 'Marked for Export',
            'value' => '0',
        ]);

        CRUD::addField([
            'name' => 'color',
            'type' => 'text',
            'label' => 'Color',
        ]);
        // L3

        CRUD::addField([
            'name' => 'type_approval',
            'type' => 'hidden',
            'label' => 'Type Approval',
            'value' => 'L3',
        ]);

        CRUD::addField([
            'name' => 'wheel_plan',
            'type' => 'text',
            'label' => 'Wheel Plan',
            'value' => '2 Wheel',
        ]);

        CRUD::addField([
            'name' => 'month_of_first_registration',
            'type' => 'date',
            'label' => 'Month of First Registration',
        ]);

        CRUD::addField([
            'name' => 'co2_emissions',
            'type' => 'hidden',
            'label' => 'Co2 Emissions',
        ]);

        CRUD::addField([
            'name' => 'vehicle_profile_id',
            'type' => 'text',
            'label' => 'Vehicle Profile ID',
        ]);

        CRUD::addField([
            'name' => 'branch_id',
            'label' => 'Branch',
            'type' => 'select2',
            'entity' => 'branch',
            'attribute' => 'name',
            'model' => Branch::class,
            'tab' => 'General',
        ]);

        CRUD::addField([
            'name' => 'date_of_last_v5c_issuance',
            'type' => 'datetime',
            'label' => 'Date of Last V5C Issuance',
        ]);

        CRUD::addField([
            'name' => 'vehicle_profile_id',
            'type' => 'number',
            'label' => 'Vehicle Profile ID',
            // 'value' => '2'
        ]);

        CRUD::addField([
            'name' => 'branch_id',
            'label' => 'Branch',
            'type' => 'select2',
            'entity' => 'branch',
            'attribute' => 'name',
            'model' => Branch::class,
            'tab' => 'General',
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function fetchMotorbike()
    {
        return $this->fetch(Motorbike::class);
    }
}
