<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\NgnStockHandlerRequest;
use App\Imports\StockHandler;
use App\Models\NgnProduct;
use App\Models\NgnStockMovement;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Make sure to import your import class
use Maatwebsite\Excel\Facades\Excel; // Import the Request class correctly

/**
 * Class NgnStockHandlerCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class NgnStockHandlerCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    protected $branchIds = [
        'catford' => 1,
        'tooting' => 2,
        'sutton' => 3,  // Add Sutton branch ID
    ];

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(NgnProduct::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/ngn-stock-handler');
        CRUD::setEntityNameStrings('ngn stock handler', 'ngn stock handlers');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumn(['name' => 'sku', 'type' => 'text', 'label' => 'SKU']);
        CRUD::addColumn(['name' => 'name', 'type' => 'text', 'label' => 'Product Name']);
        CRUD::addColumn(['name' => 'global_stock', 'type' => 'number', 'label' => 'Global Stock']);

        CRUD::addColumn([
            'name' => 'catford_stock',
            'type' => 'inline_stock_edit',
            'label' => 'Catford Stock',
        ]);

        CRUD::addColumn([
            'name' => 'tooting_stock',
            'type' => 'inline_stock_edit',
            'label' => 'Tooting Stock',
        ]);

        CRUD::addColumn([
            'name' => 'sutton_stock',
            'type' => 'inline_stock_edit',  // Use custom view if needed
            'label' => 'Sutton Stock',
        ]);

        $this->crud->addButtonFromView('top', 'import', 'import', 'beginning');
        Widget::add()->type('script')->content('assets/js/admin/forms/inline_stock_edit.js');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(NgnStockHandlerRequest::class);

        CRUD::addField([
            'name' => 'product_id',
            'label' => 'Please Select a Product',
            'type' => 'select',
            'entity' => 'product',
            'attribute' => 'name',
            'model' => "App\Models\NgnProduct",
            'ajax' => true,
            'attributes' => [
                'readonly' => 'readonly',
            ],
        ]);

        CRUD::addField([
            'name' => 'sku',
            'type' => 'text',
            'label' => 'SKU',
            'attributes' => [
                'readonly' => 'readonly',
            ],
        ]);

        CRUD::addField(['name' => 'catford_stock', 'type' => 'number', 'label' => 'Catford Stock', 'hint' => 'Enter the stock quantity available in the Catford Branch.']);
        CRUD::addField(['name' => 'tooting_stock', 'type' => 'number', 'label' => 'Tooting Stock', 'hint' => 'Enter the stock quantity available in the Tooting Branch.']);
        CRUD::addField(['name' => 'sutton_stock', 'type' => 'number', 'label' => 'Sutton Stock', 'hint' => 'Enter the stock quantity available in the Sutton Branch.']);

        Widget::add()->type('script')->content('assets/js/admin/forms/product-details.js');
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        CRUD::setValidation(NgnStockHandlerRequest::class);

        CRUD::addField([
            'name' => 'sku',
            'type' => 'text',
            'label' => 'SKU',
            'attributes' => [
                'readonly' => 'readonly',
            ],
        ]);

        CRUD::addField([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Product Name',
            'attributes' => [
                'readonly' => 'readonly',
            ],
        ]);

        CRUD::addField([
            'name' => 'catford_stock',
            'type' => 'number',
            'label' => 'Catford Stock',
            'hint' => 'Enter the stock quantity available in the Catford Branch.',
        ]);

        CRUD::addField([
            'name' => 'tooting_stock',
            'type' => 'number',
            'label' => 'Tooting Stock',
            'hint' => 'Enter the stock quantity available in the Tooting Branch.',
        ]);

        CRUD::addField([
            'name' => 'sutton_stock',
            'type' => 'number',
            'label' => 'Sutton Stock',
            'hint' => 'Enter the stock quantity available in the Sutton Branch.',
        ]);
    }

    public function store()
    {
        $data = $this->crud->getRequest()->all();
        $this->handleStockMovement($data);

        return redirect()->route('ngn-stock-handler.index')->with('success', 'Product created successfully.');
    }

    public function update()
    {
        $data = $this->crud->getRequest()->all();
        $this->handleStockMovement($data);

        return redirect()->route('ngn-stock-handler.index')->with('success', 'Product updated successfully.');
    }

    protected function handleStockMovement($data)
    {
        $sku = $data['sku'];
        $catfordStock = (int) $data['catford_stock'];
        $tootingStock = (int) $data['tooting_stock'];
        $suttonStock = (int) $data['sutton_stock'];

        $product = NgnProduct::where('sku', $sku)->first();

        if ($product) {
            $existingCatfordStock = $this->getCurrentStock($product->id, $this->branchIds['catford']);
            $existingTootingStock = $this->getCurrentStock($product->id, $this->branchIds['tooting']);
            $existingSuttonStock = $this->getCurrentStock($product->id, $this->branchIds['sutton']);

            $catfordDiff = $catfordStock - $existingCatfordStock;
            $tootingDiff = $tootingStock - $existingTootingStock;
            $suttonDiff = $suttonStock - $existingSuttonStock;

            $this->updateStockMovement($product, $this->branchIds['catford'], $catfordDiff);
            $this->updateStockMovement($product, $this->branchIds['tooting'], $tootingDiff);
            $this->updateStockMovement($product, $this->branchIds['sutton'], $suttonDiff);

            $product->global_stock = $existingCatfordStock + $catfordDiff + $existingTootingStock + $tootingDiff + $existingSuttonStock + $suttonDiff;
            $product->save();

            Log::info("Product {$product->name} - Updated stock: Catford: {$catfordStock}, Tooting: {$tootingStock}, Sutton: {$suttonStock}, Global: {$product->global_stock}");
        } else {
            Log::warning("Product not found for SKU: {$sku}");
        }
    }

    protected function getCurrentStock($productId, $branchId)
    {
        return NgnStockMovement::where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->sum('in') - NgnStockMovement::where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->sum('out');
    }

    protected function updateStockMovement($product, $branchId, $stockDiff)
    {
        if ($stockDiff > 0) {
            $this->createStockMovement($product, $branchId, $stockDiff, 'IN', 'Stock Adjustment');
        } elseif ($stockDiff < 0) {
            $this->createStockMovement($product, $branchId, abs($stockDiff), 'OUT', 'Shop Sale');
        }
    }

    protected function createStockMovement($product, $branchId, $stock, $type, $transactionType)
    {
        NgnStockMovement::create([
            'product_id' => $product->id,
            'branch_id' => $branchId,
            'in' => $type === 'IN' ? $stock : 0,
            'out' => $type === 'OUT' ? $stock : 0,
            'transaction_type' => $transactionType,
            'transaction_date' => now(),
            'user_id' => auth()->user()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Log::info("Stock movement {$type} for product: {$product->name} at branch ID: {$branchId} with transaction type: {$transactionType}");
    }

    // Import method
    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate(['file' => 'required|mimes:xlsx']);

        // Check if the request has an 'update_with_zero' parameter and use it, defaulting to false
        $updateWithZero = $request->input('update_with_zero', false);

        // Import the file with the provided 'update_with_zero' value
        Excel::import(new StockHandler($updateWithZero), $request->file('file'));

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Data imported successfully.');
    }

    public function fetchProductData(Request $request)
    {
        $productId = $request->get('id');
        $product = NgnProduct::find($productId);

        if ($product) {
            $catfordStock = $this->getCurrentStock($productId, $this->branchIds['catford']);
            $tootingStock = $this->getCurrentStock($productId, $this->branchIds['tooting']);
            $suttonStock = $this->getCurrentStock($productId, $this->branchIds['sutton']);

            return response()->json([
                'sku' => $product->sku,
                'catford_stock' => $catfordStock,
                'tooting_stock' => $tootingStock,
                'sutton_stock' => $suttonStock,
            ]);
        }

        return response()->json(['error' => 'Product not found'], 404);
    }

    public function updateStock(Request $request, $id)
    {
        $field = $request->input('field');
        $value = (int) $request->input('value');

        if (in_array($field, ['catford_stock', 'tooting_stock', 'sutton_stock'])) {
            $product = NgnProduct::find($id);

            $branchId = $this->branchIds[str_replace('_stock', '', $field)];
            $existingStock = $this->getCurrentStock($id, $branchId);
            $stockDiff = $value - $existingStock;

            $this->updateStockMovement($product, $branchId, $stockDiff);

            $product->global_stock = $this->getCurrentStock($id, $this->branchIds['catford'])
                + $this->getCurrentStock($id, $this->branchIds['tooting'])
                + $this->getCurrentStock($id, $this->branchIds['sutton']);

            $product->save();

            return response()->json(['success' => true, 'global_stock' => $product->global_stock]);
        }

        return response()->json(['error' => 'Invalid field'], 400);
    }
}
