<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MotorbikesSaleExport;
use App\Http\Requests\MotorbikesSaleRequest;
use App\Models\Motorbike;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MotorbikesSaleCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }

    public function setup()
    {
        CRUD::setModel(\App\Models\MotorbikesSale::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/motorbikes-sale');
        CRUD::setEntityNameStrings('motorbikes sale', 'motorbikes sales');

    }

    protected function setupListOperation()
    {
        CRUD::column('motorbike.reg_no')->label('Registraction No');
        CRUD::column('motorbike.model')->label('Model');
        CRUD::column('accessories')->label('Accessories');
        CRUD::setFromDb();

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

        CRUD::addFilter(
            [
                'name' => 'motorbike_model',
                'type' => 'text',
                'label' => 'Model',
            ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas', 'motorbike', function ($query) use ($value) {
                    $query->where('model', 'LIKE', "%$value%");
                });
            }
        );

        CRUD::addFilter(
            [
                'name' => 'is_sold',
                'type' => 'dropdown',
                'label' => 'Availability',
            ],
            [
                0 => 'Available',
                1 => 'Sold',
            ],
            function ($value) {
                $this->crud->addClause('where', 'is_sold', $value);
            }
        );

        CRUD::removeColumn('motorbike_id');

        CRUD::enableExportButtons();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(MotorbikesSaleRequest::class);

        CRUD::setFromDb();

        CRUD::addField([
            'name' => 'user_id',
            'type' => 'hidden',
            'default' => backpack_user()->id,
        ]);

        CRUD::field('price')->label('Price')->type('number')->attributes(['step' => 'any']);

        CRUD::field('motorbike_id')->label('Motorbike')
            ->type('select2')
            ->entity('motorbike')
            ->attribute('reg_no')
            ->model(Motorbike::class)
            ->query(function ($query) {
                $query->whereNotIn('id', DB::raw('(SELECT motorbike_id FROM application_items)'));
            });

        CRUD::addField([
            'name' => 'condition',
            'label' => 'Condition',
            'type' => 'text',
            'value' => 'USED',
            'attributes' => [
                'readonly' => 'readonly',
            ],
        ]);

        CRUD::addField([
            'name' => 'image_one',
            'label' => 'Image One',
            'type' => 'hidden',
        ]);

        CRUD::addField([
            'name' => 'image_two',
            'label' => 'Image Two',
            'type' => 'hidden',
        ]);

        CRUD::addField([
            'name' => 'image_three',
            'label' => 'Image Three',
            'type' => 'hidden',
        ]);

        CRUD::addField([
            'name' => 'image_four',
            'label' => 'Image Four',
            'type' => 'hidden',
        ]);

        CRUD::addField([
            'name' => 'accessories',
            'label' => 'Accessories',
            'type' => 'summernote',
            'options' => [
                'toolbar' => [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']],
                ],
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(MotorbikesSaleRequest::class);

        CRUD::addField([
            'name' => 'user_id',
            'type' => 'hidden',
            'default' => backpack_user()->id,
        ]);

        CRUD::field('motorbike_id')->label('Motorbike')
            ->type('select2')
            ->entity('motorbike')
            ->attribute('reg_no')
            ->model(Motorbike::class)
            ->attributes(['readonly' => 'readonly', 'disabled' => 'disabled']);

        CRUD::addField([
            'name' => 'condition',
            'label' => 'Condition',
            'type' => 'text',
            'value' => 'USED',
            'attributes' => [
                'readonly' => 'readonly',
                'disabled' => 'disabled',
            ],
        ]);

        // Adding image upload fields
        CRUD::addField([
            'name' => 'image_one',
            'label' => 'Image One',
            'type' => 'upload', // or 'image' if you want cropping, etc.
            'upload' => true,
            'disk' => 'used_motorbikes', // Specify the disk where images will be stored
            'withFiles' => true,
        ]);

        CRUD::addField([
            'name' => 'image_two',
            'label' => 'Image Two',
            'type' => 'upload',
            'upload' => true,
            'disk' => 'used_motorbikes',
            'withFiles' => true,
        ]);

        CRUD::addField([
            'name' => 'image_three',
            'label' => 'Image Three',
            'type' => 'upload',
            'upload' => true,
            'disk' => 'used_motorbikes',
            'withFiles' => true,
        ]);

        CRUD::addField([
            'name' => 'image_four',
            'label' => 'Image Four',
            'type' => 'upload',
            'upload' => true,
            'disk' => 'used_motorbikes',
            'withFiles' => true,
        ]);

        CRUD::addField([
            'name' => 'is_sold',
            'label' => 'Is Sold',
            'type' => 'checkbox',
        ]);

        // Buyer fields – shown only when Is Sold is checked
        CRUD::addField([
            'name' => 'buyer_name',
            'label' => 'Buyer Name',
            'type' => 'text',
            'wrapper' => ['class' => 'form-group col-12 buyer-fields-wrapper'],
            'attributes' => ['placeholder' => 'Buyer full name'],
        ]);
        CRUD::addField([
            'name' => 'buyer_phone',
            'label' => 'Buyer Phone',
            'type' => 'text',
            'wrapper' => ['class' => 'form-group col-12 buyer-fields-wrapper'],
            'attributes' => ['placeholder' => 'Phone number'],
        ]);
        CRUD::addField([
            'name' => 'buyer_email',
            'label' => 'Buyer Email',
            'type' => 'email',
            'wrapper' => ['class' => 'form-group col-12 buyer-fields-wrapper'],
            'attributes' => ['placeholder' => 'Email address'],
        ]);
        CRUD::addField([
            'name' => 'buyer_address',
            'label' => 'Address (optional)',
            'type' => 'textarea',
            'wrapper' => ['class' => 'form-group col-12 buyer-fields-wrapper'],
            'attributes' => ['placeholder' => 'Buyer address', 'rows' => 2],
        ]);

        CRUD::addField([
            'name' => 'mileage',
            'label' => 'Mileage',
            'type' => 'number',
            'attributes' => [
                'step' => 'any',
            ],
        ]);

        CRUD::addField([
            'name' => 'price',
            'label' => 'Price',
            'type' => 'number', // Changed to decimal
            'attributes' => [
                'step' => 'any',
            ],
        ]);

        CRUD::addField([
            'name' => 'engine',
            'label' => 'Engine',
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'suspension',
            'label' => 'Suspension',
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'brakes',
            'label' => 'Brakes',
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'belt',
            'label' => 'Belt',
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'electrical',
            'label' => 'Electrical',
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'tires',
            'label' => 'Tires',
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'note',
            'label' => 'Note',
            'type' => 'textarea',
        ]);

        CRUD::addField([
            'name' => 'accessories',
            'label' => 'Accessories',
            'type' => 'summernote',
            'options' => [
                'toolbar' => [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']],
                ],
            ],
        ]);

        CRUD::addField([
            'name' => 'v5_available',
            'label' => 'V5 Available',
            'type' => 'checkbox',
        ]);

        // Toggle buyer fields visibility when Is Sold is checked/unchecked
        Widget::add()->type('script')->content('assets/js/admin/forms/motorbikes-sale-buyer-fields-toggle.js');
    }

    public function store()
    {
        $response = $this->traitStore();
        $this->syncImages($this->crud->entry);
        return $response;
    }

    public function update()
    {
        $response = $this->traitUpdate();
        $entry = $this->crud->entry;

        // When is_sold is unchecked, clear buyer fields
        if (! $entry->is_sold) {
            $entry->buyer_name = null;
            $entry->buyer_phone = null;
            $entry->buyer_email = null;
            $entry->buyer_address = null;
            $entry->save(); // update log with cleared buyer info
        }
        $this->syncImages($this->crud->entry);
        // $this->syncImages($entry);
        return $response;
    }
    
    protected function syncImages($motorbike)
    {
        $fields = ['image_one', 'image_two', 'image_three', 'image_four'];
        $syncService = app(\App\Services\FtpSyncService::class);

        foreach ($fields as $field) {
            if ($motorbike->{$field}) {
                // Build absolute local path – same logic as your working PDF code
                $absoluteLocalPath = storage_path('app/public/motorbikes/' . $motorbike->{$field});

                \Log::info("📁 Local file saved at: " . $absoluteLocalPath);

                $success = $syncService->uploadFile($absoluteLocalPath);

                \Log::info("📤 Actual remote mirror path: " . ($success ? 'SUCCESS' : 'FAILED'));

                if (!$success) {
                    \Log::warning("Uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
                }
            }
        }
    }


    public function export()
    {
        return Excel::download(new MotorbikesSaleExport, 'motorbikes_sales.xlsx');
    }
}
