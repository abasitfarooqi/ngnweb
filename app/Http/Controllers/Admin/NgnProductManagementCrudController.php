<?php

namespace App\Http\Controllers\Admin;

use App\Exports\NgnPOSProductsExport;
use App\Http\Requests\NgnProductManagementRequest;
use App\Models\NgnBrand;
use App\Models\NgnCategory;
use App\Models\NgnModel;
use App\Models\NgnProduct;
use App\Models\NgnProductImage;
use App\Models\NgnStockMovement;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NgnProductManagementCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(NgnProduct::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/ngn-product-management');
        CRUD::setEntityNameStrings('Product', 'Add Products');
    }

    protected function setupListOperation()
    {
        CRUD::enableExportButtons();
        CRUD::column('sku')->label('SKU');
        CRUD::addColumn([
            'name' => 'name', // The db column name
            'label' => 'Product Name', // Table column heading
            'type' => 'text', // Using type text to accommodate long names
            'limit' => 300, // Set limit to null to allow for full display
        ]);
        CRUD::column('normal_price')->label('Sale Price');
        CRUD::column('global_stock')->label('Global Stock');
        CRUD::addColumn([
            'name' => 'productImages',
            'label' => 'Images',
            'type' => 'relationship',
            'entity' => 'productImages',
            'attribute' => 'image_url',
            'model' => NgnProductImage::class,
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return asset('storage/'.$related_key);
                },
                'target' => '_blank',
            ],
        ]);

        CRUD::addColumn([
            'name' => 'brand_id',
            'type' => 'select',
            'entity' => 'brand',
            'attribute' => 'name',
            'model' => NgnBrand::class,
        ]);
        CRUD::addColumn([
            'name' => 'category_id',
            'type' => 'select',
            'entity' => 'category',
            'attribute' => 'name',
            'model' => NgnCategory::class,
        ]);
        CRUD::addColumn([
            'name' => 'model_id',
            'type' => 'select',
            'entity' => 'productModel',
            'attribute' => 'name',
            'model' => NgnModel::class,
        ]);

        // Add product name filter
        CRUD::addFilter([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Product Name',
        ], false, function ($value) {
            $this->crud->addClause('where', 'name', 'like', "%$value%");
        });

        // Add SKU filter
        CRUD::addFilter([
            'name' => 'sku',
            'type' => 'text',
            'label' => 'SKU',
        ], false, function ($value) {
            $this->crud->addClause('where', 'sku', 'like', "%$value%");
        });

        // Add brand filter
        CRUD::addFilter([
            'name' => 'brand_id',
            'type' => 'dropdown',
            'label' => 'Brand',
        ], function () {
            return NgnBrand::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'brand_id', $value);
        });

        // Add category filter
        CRUD::addFilter([
            'name' => 'category_id',
            'type' => 'dropdown',
            'label' => 'Category',
        ], function () {
            return NgnCategory::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'category_id', $value);
        });

        // Add model filter
        CRUD::addFilter([
            'name' => 'model_id',
            'type' => 'dropdown',
            'label' => 'Model',
        ], function () {
            return NgnModel::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'category_id', $value);
        });

        CRUD::setDefaultPageLength(25);
        $this->crud->addButtonFromView('bottom', 'export_pos', 'export_pos', 'beginning');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(NgnProductManagementRequest::class);

        CRUD::field('sku')->attributes([
            'placeholder' => 'Enter SKU',
            'class' => 'form-control',
        ]);
        CRUD::field('sorting_code')
            ->label('Sorting Code')
            ->attributes([
                'class' => 'form-control',
                'placeholder' => 'Enter Product Sorting Code',
            ]);

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

        CRUD::addField([
            'name' => 'productImages',
            'type' => 'upload_multiple',
            'label' => 'Product Images',
            'upload' => true,
            'disk' => 'product_images',
            'path' => '', // Empty path as in the first field
            'withFiles' => [
                'deleteWhenEntryIsDeleted' => true,
            ],
            'model' => NgnProductImage::class,
            'entity' => 'productImages',
            'attribute' => 'image_url',
            'value' => $this->crud->getCurrentEntry() && $this->crud->getCurrentEntry()->productImages ?
                $this->crud->getCurrentEntry()->productImages->pluck('image_url')->toArray() : [],
            'attributes' => [
                'accept' => 'image/*',
            ],
            'rules' => [
                'nullable',
                'array',
                'min:1',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:32768',
            ],
        ]);

        CRUD::field('name')->attributes([
            'placeholder' => 'Enter product name',
            'class' => 'form-control',
        ]);

        CRUD::field('description')
            ->type('summernote')->label('Description')
            ->attributes([
                'placeholder' => 'Enter product description',
                'class' => 'form-control',
            ]);

        CRUD::field('normal_price')->attributes([
            'placeholder' => 'Enter price',
            'class' => 'form-control',
        ]);

        // CRUD::addField([
        //     'name' => 'brand_id',
        //     'label' => 'Brand',
        //     'type' => 'select2_from_ajax',
        //     'entity' => 'brand',
        //     'attribute' => 'name',
        //     'model' => NgnBrand::class,
        //     // 'ajax' => true,
        //     // 'inline_create' => true,
        //     // 'query' => function ($query) {
        //     //     return $query->where('vehicle_profile_id', 1);
        //     // }
        // ]);

        // CRUD::addField([
        //     'name' => 'brand_id',
        //     'label' => 'Brand',
        //     'type' => 'relationship',
        //     'entity' => 'brand',
        //     'attribute' => 'name',
        //     'model' => NgnBrand::class,
        //     // 'ajax' => true,
        //     'inline_create' => ['entity' => 'ngn-brand'],
        // ]);
        // CRUD::addField([
        //     'name' => 'brand',  // The relationship method on your model
        //     'type' => 'relationship',  // Use 'relationship' type
        //     'ajax' => true,     // Enables AJAX for dynamic loading
        //     'inline_create' => [  // Enables inline creation
        //         'entity'    => 'ngn-brand',      // The singular entity name
        //         'model'     => NgnBrand::class,  // Specify the model class for the relationship
        //         'force_select' => true,  // Optional: auto-select created entry
        //         'modal_class' => 'modal-lg',  // Optional: control modal size
        //         'create_route' => route('ngn-brand-inline-create-save'),  // Correct route for inline creation
        //         'modal_route' => route('ngn-brand-inline-create'),  // Correct route for loading the inline creation modal
        //     ]
        // ]);

        // Widget::add()->type('script')->inline()->content('assets/js/admin/forms/inline-brands.js');

        CRUD::field([
            'name' => 'brand_id',
            'type' => 'select2',
            'entity' => 'brand',
            'attribute' => 'name',
            'model' => NgnBrand::class,
            'label' => 'Brand',
            'wrapper' => ['class' => 'form-group col-md-6'],
        ]);

        CRUD::field('new_brand')
            ->label('New Brand')
            ->type('text')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field([
            'name' => 'category_id',
            'type' => 'select2',
            'entity' => 'category',
            'attribute' => 'name',
            'model' => NgnCategory::class,
            'label' => 'Category',
            'wrapper' => ['class' => 'form-group col-md-6'],
        ]);

        CRUD::field('new_category')
            ->label('New Category')
            ->type('text')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field([
            'name' => 'model_id',
            'type' => 'select2',
            'entity' => 'productModel',
            'attribute' => 'name',
            'model' => NgnModel::class,
            'label' => 'Model',
            'wrapper' => ['class' => 'form-group col-md-6'],
        ]);

        CRUD::field('new_model')
            ->label('New Model')
            ->type('text')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field([
            'name' => 'stock_movements',
            'label' => 'Stock Movements',
            'type' => 'repeatable',
            'subfields' => [
                [
                    'name' => 'branch_id',
                    'type' => 'select2',
                    'label' => 'Branch',
                    'model' => 'App\Models\Branch',
                    'attribute' => 'name',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name' => 'transaction_date',
                    'type' => 'hidden',
                    'label' => 'Transaction Date',
                    'default' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'in',
                    'type' => 'number',
                    'label' => 'Stock In',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                    'attributes' => ['value' => 0], // Set default value
                    'value' => 0,
                ],
                [
                    'name' => 'transaction_type',
                    'type' => 'hidden',
                    'label' => 'Transaction Type',
                    'value' => 'Opening Stock',
                ],
                [
                    'name' => 'remarks',
                    'type' => 'textarea',
                    'label' => 'Remarks',
                    'wrapper' => ['class' => 'form-group col-md-12'],
                ],
            ],
            'new_item_label' => 'Add Stock Movement',
            'init_rows' => 1,
            'min_rows' => 0,
            'max_rows' => 1,
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        CRUD::field('sku')->attributes(['readonly' => 'readonly']);

        CRUD::field('sorting_code')
            ->label('Sorting Code')
            ->attributes([
                'class' => 'form-control',
                'placeholder' => 'Enter Product Sorting Code',
            ]);
        // $existingMovements = NgnStockMovement::where('product_id', $this->crud->getCurrentEntry()->id)->get();

        CRUD::field([
            'name' => 'stock_movements',
            'label' => 'Stock Movements',
            'type' => 'repeatable',
            'subfields' => [
                [
                    'name' => 'branch_id',
                    'type' => 'select2',
                    'label' => 'Branch',
                    'model' => 'App\Models\Branch',
                    'attribute' => 'name',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name' => 'transaction_date',
                    'type' => 'hidden',
                    'label' => 'Transaction Date',
                    'default' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'in',
                    'type' => 'number',
                    'label' => 'Stock In',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                    'attributes' => ['value' => 0], // Set default value
                    'value' => 0,
                ],
                [
                    'name' => 'transaction_type',
                    'type' => 'hidden',
                    'label' => 'Transaction Type',
                    'value' => 'Opening Stock',
                ],
                [
                    'name' => 'remarks',
                    'type' => 'textarea',
                    'label' => 'Remarks',
                    'wrapper' => ['class' => 'form-group col-md-12'],
                ],
            ],
            'new_item_label' => 'Add Stock Movement',
            // 'init_rows' => $existingMovements->count(), // Set the initial rows to the count of existing movements
            // 'default' => $existingMovements, // Provide the existing movements
            'init_rows' => 1,
            'min_rows' => 0,
            'max_rows' => 1,
        ]);

        // Display existing stock movements
        $stockMovements = NgnStockMovement::where('product_id', $this->crud->getCurrentEntry()->id)->get();

        // Calculate total stock per branch and total available stock
        $branchStocks = [];
        $totalAvailableStock = 0;

        foreach ($stockMovements as $movement) {
            $branchId = $movement->branch_id;
            if (! isset($branchStocks[$branchId])) {
                $branchStocks[$branchId] = ['branch' => $movement->branch->name, 'stock' => 0];
            }
            $branchStocks[$branchId]['stock'] += ($movement->in ?? 0) - ($movement->out ?? 0);
            $totalAvailableStock += ($movement->in ?? 0) - ($movement->out ?? 0);
        }

        // Add a field to display the stock movements
        CRUD::addField([
            'name' => 'stock_movements_display',
            'label' => 'Existing Stock Movements',
            'type' => 'custom_html',
            'value' => view('livewire.agreements.migrated.admin.stock_movements_table', compact('stockMovements', 'branchStocks', 'totalAvailableStock'))->render(),
        ]);

    }

    public function store(NgnProductManagementRequest $request)
    {
        // Check and create new Brand
        if ($request->filled('new_brand')) {
            $brand = NgnBrand::create(['name' => $request->new_brand]);
            $request->merge(['brand_id' => $brand->id]);
        }

        // Check and create new Category
        if ($request->filled('new_category')) {
            $category = NgnCategory::create(['name' => $request->new_category]);
            $request->merge(['category_id' => $category->id]);
        }

        // Check and create new Model
        if ($request->filled('new_model')) {
            $model = NgnModel::create(['name' => $request->new_model]);
            $request->merge(['model_id' => $model->id]);
        }

        // Create the product excluding the custom fields
        $product = NgnProduct::create($request->except(['new_brand', 'new_category', 'new_model']));

        // Fetch current global stock from the product table
        $globalStock = $product->global_stock;

        // Handle stock movements
        if ($request->filled('stock_movements')) {
            foreach ($request->input('stock_movements') as $movement) {

                $branchId = isset($movement['branch_id']) && ! empty($movement['branch_id']) ? $movement['branch_id'] : 1;
                // Update stock movement
                NgnStockMovement::create([
                    'product_id' => $product->id,
                    'branch_id' => $branchId,
                    'transaction_date' => $movement['transaction_date'],
                    'in' => $movement['in'] ?? 0,
                    'out' => $movement['out'] ?? 0,
                    'transaction_type' => $movement['transaction_type'],
                    'user_id' => backpack_auth()->user()->id,
                    'remarks' => $movement['remarks'] ?? '',
                ]);

                // Calculate the global stock
                $globalStock += ($movement['in'] ?? 0) - ($movement['out'] ?? 0);
            }
        }

        // Update the global stock of the product
        $product->update(['global_stock' => $globalStock]);

        $product = NgnProduct::create($request->except(['images']));

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('product_images', 'public'); // Store image in public disk
                NgnProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $path, // Save the path to the image
                ]);
            }
        }

        // Return to the product listing with a success message
        return redirect()->to($this->crud->route)->with('success', 'Product created successfully and global stock updated!');
    }

    public function update(NgnProductManagementRequest $request)
    {
        $product = NgnProduct::find($this->crud->getCurrentEntryId());

        $product->update($request->except(['images']));

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('product_images', 'public'); // Store image in public disk
                NgnProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $path, // Save the path to the image
                ]);
            }
        }

        // Handle new Brand, Category, Model creation if provided
        if ($request->filled('new_brand')) {
            $brand = NgnBrand::create(['name' => $request->new_brand]);
            $request->merge(['brand_id' => $brand->id]);
        }

        if ($request->filled('new_category')) {
            $category = NgnCategory::create(['name' => $request->new_category]);
            $request->merge(['category_id' => $category->id]);
        }

        if ($request->filled('new_model')) {
            $model = NgnModel::create(['name' => $request->new_model]);
            $request->merge(['model_id' => $model->id]);
        }

        // Update product
        $product->update($request->except(['new_brand', 'new_category', 'new_model']));

        // Fetch current global stock
        $globalStock = $product->global_stock;

        // Handle stock movements
        if ($request->filled('stock_movements')) {
            foreach ($request->input('stock_movements') as $movement) {

                $branchId = isset($movement['branch_id']) && ! empty($movement['branch_id']) ? $movement['branch_id'] : 1;

                NgnStockMovement::create([
                    'product_id' => $product->id,
                    'branch_id' => $branchId,
                    'transaction_date' => $movement['transaction_date'],
                    'in' => $movement['in'] ?? 0,
                    'out' => $movement['out'] ?? 0,
                    'transaction_type' => $movement['transaction_type'],
                    'user_id' => backpack_auth()->user()->id,
                    'remarks' => $movement['remarks'] ?? '',
                ]);

                // Calculate the global stock
                $globalStock += ($movement['in'] ?? 0) - ($movement['out'] ?? 0);
            }
        }

        // Update the global stock of the product
        $product->update(['global_stock' => $globalStock]);

        return redirect()->to($this->crud->route)->with('success', 'Product updated successfully and global stock updated!');
    }

    // Add method to handle export FOR POS
    public function exportForPOS(Request $request)
    {
        // Get the current date and time in the format 'Y-m-d_H-i-s'
        $timestamp = date('Y-m-d_H-i-s');

        // Create the filename dynamically
        $filename = 'ngn_pos_products_'.$timestamp.'.xlsx';

        // Export the file with the generated filename
        return Excel::download(new NgnPOSProductsExport, $filename);
    }

    protected function setupDeleteOperation()
    {
        CRUD::field('image_url')
            ->type('upload_multiple')
            ->withFiles([
                'deleteWhenEntryIsDeleted' => true,
            ]);
    }
}
