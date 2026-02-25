<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MotorbikesSaleExport;
use App\Http\Requests\NewMotorbikesForSaleRequest;
use App\Models\Motorcycle;
use Backpack\CRUD\app\Http\Controllers\CrudController; // Updated to use Motorcycle model
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Maatwebsite\Excel\Facades\Excel;

class NewMotorbikesForSaleCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(Motorcycle::class); // Updated to use Motorcycle model
        CRUD::setRoute(config('backpack.base.route_prefix').'/new-motorbikes-for-sale');
        CRUD::setEntityNameStrings('new motorbike for sale', 'new motorbikes for sale');
        // Add filter for availability
        $this->crud->addFilter([
            'name' => 'availability',
            'type' => 'select2',
            'label' => 'Availability',
        ], [
            'for sale' => 'For Sale',
        ], function ($value) {
            $this->crud->addClause('where', 'availability', $value);
        }, 'for sale'); // Set default selected value to 'for sale'

        // Disable the availability field
        $this->crud->addField([
            'name' => 'availability',
            'type' => 'hidden',
            'value' => 'for sale', // Set default value
        ]);

    }

    protected function setupListOperation()
    {
        CRUD::setFromDb();
        CRUD::column('make')->label('Make');
        CRUD::column('model')->label('Model');
        CRUD::column('year')->label('Year');
        CRUD::column('sale_new_price')->label('Sale Price');
        CRUD::removeColumn('id');

        CRUD::addFilter(
            [
                'name' => 'make',
                'type' => 'text',
                'label' => 'Make',
            ],
            false,
            function ($value) {
                $this->crud->addClause('where', 'make', 'LIKE', "%$value%")
                    ->where('availability', 'for sale'); // Ensure only new bikes are shown
            }
        );

        CRUD::addFilter(
            [
                'name' => 'model',
                'type' => 'text',
                'label' => 'Model',
            ],
            false,
            function ($value) {
                $this->crud->addClause('where', 'model', 'LIKE', "%$value%")
                    ->where('availability', 'for sale'); // Ensure only new bikes are shown
            }
        );
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(NewMotorbikesForSaleRequest::class); // Updated to use the new request class

        CRUD::setFromDb();
        $this->crud->addField([
            'name' => 'availability',
            'type' => 'hidden',
            'value' => 'for sale',
            'attributes' => [
                'readonly' => 'readonly',
            ],
        ]);

        CRUD::field('make')->label('Make')->type('text')->attributes(['placeholder' => 'Enter motorcycle make']);
        CRUD::field('model')->label('Model')->type('text')->attributes(['placeholder' => 'Enter motorcycle model']);
        CRUD::field('year')->label('Year')->type('number')->attributes(['placeholder' => 'Enter year of manufacture']);
        CRUD::field('sale_new_price')->label('Sale Price')->type('number')->attributes(['placeholder' => 'Enter sale price'])->prefix('£');
        CRUD::field('colour')->label('Colour')->type('text')->attributes(['placeholder' => 'Enter motorcycle colour']);
        CRUD::field('description')->label('Description')->type('textarea')->attributes(['placeholder' => 'Enter motorcycle description']);
        CRUD::field('category')->label('Category')->type('hidden')->attributes(['value' => 'new for sale']); // Hidden field for category
        CRUD::field('engine')->label('Engine')->type('text')->attributes(['placeholder' => 'Enter engine specifications']);
        // CRUD::field('file_path')->label('Images')->type('upload')->upload(true)->disk('used_motorbikes');
        // Adding image upload fields
        CRUD::addField([
            'name' => 'file_path',
            'label' => 'Images',
            'type' => 'upload', // or 'image' if you want cropping, etc.
            'upload' => true,
            'disk' => 'used_motorbikes', // Specify the disk where images will be stored
            'withFiles' => true,
        ]);

        CRUD::field('type')->label('Motorcycle Type')->type('select_from_array')->options([
            'Scooter' => 'Scooter',
            'Standard' => 'Standard',
            'Super Sport' => 'Super Sport',
            'Touring' => 'Touring',
            'Other' => 'Other',
        ]);
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(NewMotorbikesForSaleRequest::class); // Updated to use the new request class

        CRUD::setFromDb();
        $this->crud->addField([
            'name' => 'availability',
            'type' => 'hidden',
            'value' => 'for sale',
            'attributes' => [
                'readonly' => 'readonly',
            ],
        ]);

        CRUD::field('make')->label('Make')->type('text')->attributes(['placeholder' => 'Enter motorcycle make']);
        CRUD::field('model')->label('Model')->type('text')->attributes(['placeholder' => 'Enter motorcycle model']);
        CRUD::field('year')->label('Year')->type('number')->attributes(['placeholder' => 'Enter year of manufacture']);
        CRUD::field('sale_new_price')->label('Sale Price')->type('number')->attributes(['placeholder' => 'Enter sale price'])->prefix('£');
        CRUD::field('colour')->label('Colour')->type('text')->attributes(['placeholder' => 'Enter motorcycle colour']);
        CRUD::field('category')->label('Category')->type('hidden')->attributes(['value' => 'new for sale']); // Hidden field for category
        CRUD::field('description')->label('Description')->type('textarea')->attributes(['placeholder' => 'Enter motorcycle description']);
        CRUD::field('engine')->label('Engine')->type('text')->attributes(['placeholder' => 'Enter engine specifications']);
        // CRUD::field('file_path')->label('Images')->type('upload')->upload(true)->disk('new_motorbike_images');
        CRUD::addField([
            'name' => 'file_path',
            'label' => 'Images',
            'type' => 'upload', // or 'image' if you want cropping, etc.
            'upload' => true,
            'disk' => 'used_motorbikes', // Specify the disk where images will be stored
            'withFiles' => true,
        ]);

        CRUD::field('type')->label('Motorcycle Type')->type('select_from_array')->options([
            'Scooter' => 'Scooter',
            'Standard' => 'Standard',
            'Super Sport' => 'Super Sport',
            'Touring' => 'Touring',
            'Other' => 'Other',
        ]);
    }

    public function export()
    {
        return Excel::download(new MotorbikesSaleExport, 'motorbikes_sales.xlsx');
    }
}
