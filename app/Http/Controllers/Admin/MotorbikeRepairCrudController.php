<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MotorbikeRepairRequest;
use App\Models\Branch;
use App\Models\Motorbike;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use PDF;

class MotorbikeRepairCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate;
    }

    public function setup()
    {
        CRUD::setModel(\App\Models\MotorbikeRepair::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/motorbike-repair');
        CRUD::setEntityNameStrings('motorbike repair', 'motorbike repairs');

        Widget::add()->type('script')->inline()->content('assets/js/admin/forms/toggle-motorbike-repairs-services-list.js');
    }

    public function setupInlineCreateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupListOperation()
    {
        CRUD::column('motorbike.reg_no')->label('Registraction No');
        CRUD::setFromDb();
        CRUD::enableExportButtons();
        // Add a custom button for generating PDF for each repair entry
        CRUD::addButtonFromView('line', 'generate_pdf', 'generate_pdf', 'beginning');

        CRUD::addFilter(
            [
                'name' => 'motorbike_reg_no',
                'type' => 'text',
                'label' => 'Registration No',
            ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas', 'motorbike', function ($query) use ($value) {
                    $query->where('reg_no', 'LIKE', "%$value%");
                });
            }
        );
        // Filter for repair status (is_repaired)
        CRUD::addFilter([
            'name' => 'is_repaired',
            'type' => 'dropdown',
            'label' => 'Repair Completed',
        ], [
            1 => 'Yes',
            0 => 'No',
        ], function ($value) {
            $this->crud->addClause('where', 'is_repaired', $value);
        });

        // Filter for return status (is_returned)
        CRUD::addFilter([
            'name' => 'is_returned',
            'type' => 'dropdown',
            'label' => 'Returned to Customer',
        ], [
            1 => 'Yes',
            0 => 'No',
        ], function ($value) {
            $this->crud->addClause('where', 'is_returned', $value);
        });

        // Filter for branch
        CRUD::addFilter([
            'name' => 'branch_id',
            'type' => 'dropdown',
            'label' => 'Branch',
        ], function () {
            return Branch::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'branch_id', $value);
        });

        CRUD::removeColumn('motorbike_id');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(MotorbikeRepairRequest::class);
        CRUD::setOperationSetting('contentClass', 'col-md-12');
        CRUD::setFromDb();

        CRUD::field('fullname')->label('Full Name');
        CRUD::field('email')->label('Email');
        CRUD::field('phone')->label('Phone');

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        CRUD::addField([
            'name' => 'branch_id',
            'label' => 'Branch',
            'type' => 'select2',
            'entity' => 'branch',
            'attribute' => 'name',
            'model' => Branch::class,
        ]);

        CRUD::field('motorbike_id')->type('relationship')->ajax(true)->minimum_input_length(0)->inline_create(true);

        CRUD::addField([
            'name' => 'motorbike_id',
            'label' => 'Motorbike',
            'type' => 'relationship',
            'entity' => 'motorbike',
            'attribute' => 'reg_no',
            'model' => Motorbike::class,
            'ajax' => true,
            'inline_create' => [
                'entity' => 'motorbike',
                'force_select' => true,
                'modal_class' => 'modal-dialog modal-xl',
            ],
        ]);

        CRUD::field('arrival_date')->label('Arrival Date')->type('datetime')->default(date('Y-m-d H:i:s'));

        CRUD::field('motorbike_id')->label('Motorbike')->type('select2')->entity('motorbike')->attribute('reg_no')->model(Motorbike::class);

        CRUD::field('notes')->label('Vehicle Initial Intake Information')->type('textarea');
        CRUD::field('is_repaired')->label('Repair Completed')->type('checkbox')->hint('Marked as repaired, if agreed job has been done.');
        CRUD::field('repaired_date')->label('Repaired Date')->type('datetime')->default(date('Y-m-d H:i:s'));
        CRUD::field('is_returned')->label('Returned to Customer')->type('checkbox')->hint('Marked as returned if keeper take it.');
        CRUD::field('returned_date')->label('Returned Date')->type('datetime')->default(date('Y-m-d H:i:s'));

        CRUD::addField([
            'name' => 'updates',
            'label' => 'Repair Updates',
            'type' => 'repeatable',
            'fields' => [
                ['name' => 'id', 'type' => 'hidden'], // needed for sync
                ['name' => 'motorbike_repair_id', 'type' => 'hidden'],

                [
                    'name' => 'job_description',
                    'label' => 'Job Description',
                    'type' => 'text',
                    'wrapper' => ['class' => 'col-md-3'],
                ],
                [
                    'name' => 'price',
                    'label' => 'Price',
                    'type' => 'number',
                    'wrapper' => ['class' => 'col-md-3'],
                    'attributes' => ['step' => 'any'],
                ],
                [
                    'name' => 'note',
                    'label' => 'Note',
                    'type' => 'textarea',
                    'wrapper' => ['class' => 'col-md-3'],
                    'attributes' => ['required' => 'required'],
                ],
                [
                    'name' => 'toggle_services_btn',
                    'type' => 'custom_html',
                    'value' => '<button type="button" class="btn btn-sm btn-secondary toggle-services-btn mb-2">Show/Hide Services</button>',
                    'wrapper' => ['class' => 'col-md-12'],
                ],
                [
                    'name' => 'services',
                    'label' => 'Select Services',
                    'type' => 'checklist',
                    'entity' => 'services',
                    'attribute' => 'name',
                    'model' => \App\Models\MotorbikeRepairServicesList::class,
                    'pivot' => true,
                    'default' => [], // ✅ ensures services is always present
                    'value' => function ($entry) {
                        return $entry && $entry->services
                            ? $entry->services->pluck('id')->toArray()
                            : [];
                    },
                    'wrapper' => ['class' => 'd-none'], // Hides the field using a CSS class
                ],
            ],
        ]);

        CRUD::addField([
            'name' => 'observations',
            'label' => 'Observations Notes',
            'type' => 'repeatable',
            'fields' => [
                [
                    'name' => 'motorbike_repair_id',
                    'label' => 'Motorbike Repair ID',
                    'type' => 'hidden',
                ],
                [
                    'name' => 'observation_description',
                    'label' => 'Observation Description',
                    'type' => 'textarea',
                    'wrapper' => ['class' => 'form-group col-md-12'],
                ],
            ],
        ]);

    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        // Fetch the entry with updates + services
        $entry = $this->crud->getEntry($this->crud->getCurrentEntryId());

        // DEBUG: see what is loaded
        // dd($entry->load('updates.services')->toArray());

        CRUD::addField([
            'name' => 'updates',
            'label' => 'Repair Updates',
            'type' => 'repeatable',
            'fields' => [
                ['name' => 'id', 'type' => 'hidden'], // needed for sync
                ['name' => 'motorbike_repair_id', 'type' => 'hidden'],

                [
                    'name' => 'job_description',
                    'label' => 'Job Description',
                    'type' => 'text',
                    'wrapper' => ['class' => 'col-md-3'],
                ],
                [
                    'name' => 'price',
                    'label' => 'Price',
                    'type' => 'number',
                    'wrapper' => ['class' => 'col-md-3'],
                    'attributes' => ['step' => 'any'],
                ],
                [
                    'name' => 'note',
                    'label' => 'Note',
                    'type' => 'textarea',
                    'wrapper' => ['class' => 'col-md-3'],
                    'attributes' => ['required' => 'required'],
                ],
                [
                    'name' => 'toggle_services_btn',
                    'type' => 'custom_html',
                    'value' => '<button type="button" class="btn btn-sm btn-secondary toggle-services-btn mb-2">Show/Hide Services</button>',
                    'wrapper' => ['class' => 'col-md-12'],
                ],
                [
                    'name' => 'services',
                    'label' => 'Select Services',
                    'type' => 'checklist',
                    'entity' => 'services',
                    'attribute' => 'name',
                    'model' => \App\Models\MotorbikeRepairServicesList::class,
                    'pivot' => true,
                    'default' => [], // ✅ ensures services is always present
                    'value' => function ($entry) {
                        return $entry && $entry->services
                            ? $entry->services->pluck('id')->toArray()
                            : [];
                    },
                    'wrapper' => ['class' => 'd-none'], // Hides the field using a CSS class
                ],
            ],
        ]);

        $this->crud->entry = $entry;

    }

    public function store(MotorbikeRepairRequest $request)
    {
        $response = $this->traitStore();

        // Sync services for each update
        $this->syncUpdateServices($this->crud->entry, $request->input('updates', []));

        return $response;
    }

    public function update(MotorbikeRepairRequest $request)
    {
        $response = $this->traitUpdate();

        // Sync services for each update
        $this->syncUpdateServices($this->crud->entry, $request->input('updates', []));

        return $response;
    }

    /**
     * Sync the services pivot table for each update inside the repeatable field
     */
    protected function syncUpdateServices($motorbikeRepair, $updatesInput)
    {
        if (is_string($updatesInput)) {
            $updatesInput = json_decode($updatesInput, true);
        }
        $updatesInput = is_array($updatesInput) ? $updatesInput : [];

        $updateIds = [];

        foreach ($updatesInput as $updateInput) {
            // Determine if we should find existing update or create new
            if (! empty($updateInput['id'])) {
                $update = \App\Models\MotorbikeRepairUpdate::find($updateInput['id']);
            } else {
                // Check if identical update already exists (same motorbike_repair_id + job_description + price + note)
                $update = \App\Models\MotorbikeRepairUpdate::where('motorbike_repair_id', $motorbikeRepair->id)
                    ->where('job_description', $updateInput['job_description'] ?? '')
                    ->where('price', $updateInput['price'] ?? null)
                    ->where('note', $updateInput['note'] ?? '')
                    ->first();
                if (! $update) {
                    $update = new \App\Models\MotorbikeRepairUpdate;
                }
            }

            // Assign parent ID and fields
            $update->motorbike_repair_id = $motorbikeRepair->id;
            $update->job_description = $updateInput['job_description'] ?? '';
            $update->price = $updateInput['price'] ?? null;
            $update->note = $updateInput['note'] ?? '';
            $update->save();

            $updateIds[] = $update->id;

            // Sync services
            $services = $updateInput['services'] ?? [];
            if (is_string($services)) {
                $services = json_decode($services, true);
            }
            $services = is_array($services) ? collect($services)
                ->map(fn ($s) => is_array($s) && isset($s['id']) ? (int) $s['id'] : (int) $s)
                ->filter(fn ($id) => $id > 0)
                ->unique()
                ->values()
                ->toArray() : [];

            $update->services()->sync($services);
        }

        // Remove old updates not present in input
        $motorbikeRepair->updates()
            ->whereNotIn('id', $updateIds)
            ->get()
            ->each(function ($removedUpdate) {
                $removedUpdate->services()->detach();
                $removedUpdate->delete();
            });
    }

    public function generatePdf($id)
    {
        $repair = \App\Models\MotorbikeRepair::with([
            'motorbike',
            'branch',
            'updates.services',   // 🔥 load updates and their services
            'observations',
        ])->findOrFail($id);

        $pdf = \PDF::loadView('pdf.repair_invoice', compact('repair'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'watermark' => 'Your Watermark',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'margin-top' => 0,
                'margin-bottom' => 0,
                'margin-left' => 0,
                'margin-right' => 0,
            ]);

        return $pdf->download('Repair_Invoice_'.$repair->motorbike->reg_no.'.pdf');
    }
}
