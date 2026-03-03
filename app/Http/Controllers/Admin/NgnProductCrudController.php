<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\NgnProductRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class NgnProductCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    // use \Backpack\EditableColumns\Http\Controllers\Operations\MinorUpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\NgnProduct::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/ngn-product');
        CRUD::setEntityNameStrings('ngn product', 'ngn products');
    }

    protected function setupListOperation()
    {
        CRUD::setFromDb();

        // Editable global_stock column
        // CRUD::addColumn([
        //     'name'             => 'global_stock',
        //     'type'             => 'editable_text', // Use editable_text for a text input
        //     'label'            => 'Global Stock',

        //     // Optionals
        //     'underlined'       => true, // show a dotted line under the editable column
        //     'min_width'        => '100px', // minimum width for the column
        //     'save_on_focusout' => false, // save on focus out
        //     'auto_update_row'  => true, // update related columns in the same row after AJAX call
        // ]);
        
    // Add is_oxford and is_ecommerce as boolean filters (checkbox), making sure only one can be active at a time (or neither).
    // If one is selected, the other is deselected; both can be unselected (show all).
    // Add a single-select dropdown filter for "Product Type" where selecting Oxford means is_oxford=1 and is_ecommerce=0,
    // selecting Ecommerce means is_oxford=0 and is_ecommerce=1, and "All" shows both.
    CRUD::addFilter([
        'type'  => 'dropdown',
        'name'  => 'mode',
        'label' => 'Product Type',
        'placeholder' => 'Ecommerce',
        'default' => 'is_ecommerce',
    ],
    [
        'is_ecommerce' => 'Ecommerce',
        'is_oxford'    => 'Oxford',
        'all' => 'All',
    ],
    function($value) {
        if ($value === 'is_oxford') {
            CRUD::addClause('where', 'is_oxford', true);
            CRUD::addClause('where', 'is_ecommerce', false);
        } elseif ($value === 'is_ecommerce' || $value === null) {
            // Default (or explicitly Ecommerce): show Ecommerce only
            CRUD::addClause('where', 'is_ecommerce', true);
            CRUD::addClause('where', 'is_oxford', false);
        } elseif ($value === 'all') {
            // Show all products (no filter)
        }
    });

    // Make sure checkboxes in the column view only show the state, but filter uses dropdown to prevent dual selection.
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(NgnProductRequest::class);

        CRUD::field('sku')->type('text')->label('SKU');
        CRUD::field('ean')->type('text')->label('EAN');
        CRUD::field('image_url')
            ->type('upload_multiple')
            ->label('Images')
            ->withFiles([
                'disk' => 'product_images',
                'path' => '',
                'deleteWhenEntryIsDeleted' => true,
            ])
            ->rules('nullable|array|min:1')
            ->rules('nullable|mimes:jpeg,png,jpg,gif,svg|max:32768');

        CRUD::field('name')->label('Product Name');
        CRUD::field('variation')->label('Product Variation');
        CRUD::field('description')->type('summernote')->label('Description');
        CRUD::field('extended_description')->type('summernote')->label('Extended Description');

        CRUD::field('brand_id')
            ->type('select2')
            ->entity('brand')
            ->model('App\Models\NgnBrand')
            ->attribute('name')
            ->label('Brand');

        CRUD::field('category_id')
            ->type('select2')
            ->entity('category')
            ->model('App\Models\NgnCategory')
            ->attribute('name')
            ->label('Category');

        CRUD::field('model_id')
            ->type('select2')
            ->entity('productModel')
            ->model('App\Models\NgnModel')
            ->attribute('name')
            ->label('Model');

        CRUD::field('pos_variant_id')->type('text')->label('POS Variant ID'); // New field for POS variant
        CRUD::field('pos_product_id')->type('text')->label('POS Product ID'); // New field for POS product

        CRUD::field('colour')->type('text')->label('Colour');
        CRUD::field('normal_price')->type('number')->label('Normal Price'); // New field for normal price
        CRUD::field('pos_price')->type('number')->label('POS Price');       // New field for POS price
        CRUD::field('pos_vat')->type('number')->label('POS VAT');           // New field for POS VAT
        CRUD::field('global_stock')->type('number')->label('Global Stock'); // New field for global stock
        CRUD::field('vatable')->type('checkbox')->label('Vatable');
        CRUD::field('is_oxford')->type('checkbox')->label('Oxford Product'); // New field for Oxford products
        CRUD::field('dead')->type('checkbox')->label('Dead Product');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation(); // Reuse the create operation setup
    }

    protected function setupDeleteOperation()
    {
        CRUD::field('image_url')
            ->type('upload_multiple')
            ->withFiles([
                'deleteWhenEntryIsDeleted' => true,
            ]);
    }
    // protected function setupMinorUpdateOperation()
    // {
    //     $this->crud->setValidation(NgnProductRequest::class); // Validation for minor updates
    // }
    // public function saveMinorUpdateFormValidation()
    // {
    //     // Custom validation for global_stock
    //     if (request('attribute') === 'global_stock' && !is_numeric(request('value'))) {
    //         throw \Illuminate\Validation\ValidationException::withMessages([
    //             'global_stock' => ['The global stock must be a number.'],
    //         ]);
    //     }
    // }

    // public function saveMinorUpdateEntry()
    // {
    //     // Save the minor update
    //     $entry = $this->crud->getModel()->find(request('id'));
    //     $entry->{request('attribute')} = request('value');

    //     // Additional logic (if needed)
    //     return $entry->save();
    // }
}
