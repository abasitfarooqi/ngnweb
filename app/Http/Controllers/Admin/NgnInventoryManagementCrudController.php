<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\NgnInventoryManagementRequest;
use App\Models\Branch;
use App\Models\NgnProduct;
use App\Models\NgnStockMovement;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Support\Str; // For generating the unique ref_doc_no

class NgnInventoryManagementCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(NgnStockMovement::class); // Change to NgnStockMovement
        CRUD::setRoute(config('backpack.base.route_prefix').'/ngn-inventory-management');
        CRUD::setEntityNameStrings('inventory management', 'inventory managements');
    }

    protected function setupListOperation()
    {
        CRUD::setDefaultPageLength(25);
        CRUD::enableExportButtons();

        // Eager load the relationships needed for stock movements
        CRUD::with(['product', 'branch']);

        // Columns to display in the list view
        CRUD::addColumn([
            'name' => 'product_id',
            'label' => 'Product',
            'type' => 'select',
            'entity' => 'product',
            'attribute' => 'name', // Use the 'name' attribute of the product
            'limit' => 300, // Set limit to null to allow for full display
            'model' => "App\Models\NgnProduct",
        ]);

        // CRUD::addColumn([
        //     'name'  => 'name', // The db column name
        //     'label' => 'Product Name', // Table column heading
        //     'type'  => 'text', // Using type text to accommodate long names
        //     'limit' => 300, // Set limit to null to allow for full display
        // ]);

        CRUD::addColumn([
            'name' => 'branch_id',
            'label' => 'Branch',
            'type' => 'select',
            'entity' => 'branch',
            'attribute' => 'name', // Use the 'name' attribute of the branch
            'model' => "App\Models\Branch",
        ]);

        CRUD::addColumn([
            'name' => 'transaction_date',
            'label' => 'Transaction Date',
            'type' => 'datetime', // Use a datetime column to format the date
        ]);

        CRUD::addColumn([
            'name' => 'in',
            'label' => 'Stock In',
            'type' => 'number',
        ]);

        CRUD::addColumn([
            'name' => 'out',
            'label' => 'Stock Out',
            'type' => 'number',
        ]);

        CRUD::addColumn([
            'name' => 'transaction_type',
            'label' => 'Transaction Type',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'ref_doc_no',
            'label' => 'Reference Doc No',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'remarks',
            'label' => 'Remarks',
            'type' => 'textarea',
        ]);

        // Add product name filter
        CRUD::addFilter([
            'name' => 'product_name',
            'type' => 'text',
            'label' => 'Product Name',
        ], false, function ($value) {
            $this->crud->addClause('whereHas', 'product', function ($query) use ($value) {
                $query->where('name', 'like', "%$value%");
            });
        });

        // Add SKU filter
        CRUD::addFilter([
            'name' => 'sku',
            'type' => 'text',
            'label' => 'SKU',
        ], false, function ($value) {
            $this->crud->addClause('whereHas', 'product', function ($query) use ($value) {
                $query->where('sku', 'like', "%$value%");
            });
        });
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(NgnInventoryManagementRequest::class);

        // Fields for creating a new stock movement
        CRUD::field('product_id')->type('select2')->entity('product')->model(NgnProduct::class)->attribute('detail')->label('Product');

        // Transaction Date (default current date and time, user can change it)
        CRUD::field('transaction_date')
            ->type('datetime_picker')
            ->label('Movement Date')
            ->default(now());  // Default to current date and time

        // Transaction Type
        CRUD::field('transaction_type')
            ->type('enum')
            ->options([
                'stock_adjustment' => 'Stock Adjustment',
                'stock_transfer' => 'Stock Transfer',
                'stock_purchase' => 'Stock Purchase',
                'shop_sale' => 'Shop Sale',
                'online_sale' => 'Online Sale',
            ])
            ->label('Transaction Type');

        CRUD::field('branch_id')->type('select2')->entity('branch')->model(Branch::class)->attribute('name')->label('Branch');

        // Stock IN (default 0, user can input more)
        CRUD::field('in')
            ->type('number')
            ->label('Stock IN')
            ->default(0);  // Default to 0

        // Stock OUT (default 0, user can input more)
        CRUD::field('out')
            ->type('number')
            ->label('Stock OUT')
            ->default(0);  // Default to 0

        // Only add fields for stock transfer when applicable
        CRUD::addField([
            'name' => 'from_branch_id',
            'type' => 'select2',
            'label' => 'From Branch',
            'entity' => 'branch',
            'model' => Branch::class,
            'attribute' => 'name',
            'wrapper' => ['class' => 'd-none'], // Initially hidden, toggle with JS
        ]);

        CRUD::addField([
            'name' => 'to_branch_id',
            'type' => 'select2',
            'label' => 'To Branch',
            'entity' => 'branch',
            'model' => Branch::class,
            'attribute' => 'name',
            'wrapper' => ['class' => 'd-none'], // Initially hidden, toggle with JS
        ]);

        CRUD::addField([
            'name' => 'transfer_qty',
            'type' => 'number',
            'label' => 'Transfer Quantity',
            'wrapper' => ['class' => 'd-none'], // Initially hidden, toggle with JS
        ]);

        Widget::add()->type('script')->content('assets/js/admin/forms/branch-transfer.js');

        CRUD::field('remarks')->type('textarea')->label('Remarks'); // Add remarks field

    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(NgnInventoryManagementRequest::class);
        $this->setupCreateOperation(); // Set up fields as in create

        // Transaction Type
        CRUD::field('transaction_type')
            ->type('enum')
            ->options([
                'stock_adjustment' => 'Stock Adjustment',
                'stock_purchase' => 'Stock Purchase',
                'shop_sale' => 'Shop Sale',
                'online_sale' => 'Online Sale',
            ])
            ->label('Transaction Type');

        // Display the current global stock
        $currentEntryId = $this->crud->getCurrentEntryId();
        $currentStockMovement = NgnStockMovement::find($currentEntryId);

        if ($currentStockMovement) {
            $currentProduct = NgnProduct::find($currentStockMovement->product_id);
            if ($currentProduct) {
                CRUD::addField([
                    'name' => 'global_stock',
                    'label' => 'Current Global Stock',
                    'type' => 'number',
                    'attributes' => ['readonly' => 'readonly'],
                    'value' => $currentProduct->global_stock,
                ]);
            }
        }
    }

    public function store(NgnInventoryManagementRequest $request)
    {
        $ref_doc_no = 'REF-'.now()->format('YmdHis').'-'.Str::random(5);
        $userId = backpack_auth()->user()->id;
        $productId = $request->product_id;
        $transactionDate = $request->transaction_date;
        $remarks = $request->remarks;

        if ($request->transaction_type === 'stock_transfer') {
            // Create stock movements for stock transfer (out from 'from_branch_id', in to 'to_branch_id')
            $this->createStockMovement($request->from_branch_id, $productId, $transactionDate, 0, $request->transfer_qty, 'stock_transfer', $userId, $ref_doc_no, $remarks);
            $this->createStockMovement($request->to_branch_id, $productId, $transactionDate, $request->transfer_qty, 0, 'stock_transfer', $userId, $ref_doc_no, $remarks);

            // Update global stock once after both movements are done
            $this->updateGlobalStock($productId, 0, $request->transfer_qty); // Out from original branch
            $this->updateGlobalStock($productId, $request->transfer_qty, 0); // In to destination branch
        } else {
            // Handle all other transaction types
            $this->createStockMovement($request->branch_id, $productId, $transactionDate, $request->in, $request->out, $request->transaction_type, $userId, $ref_doc_no, $remarks);

            // Update global stock for other transaction types
            $this->updateGlobalStock($productId, $request->in, $request->out);
        }

        return redirect()->back()->with('success', 'Stock movement created successfully!');
    }

    public function update(NgnInventoryManagementRequest $request, $id)
    {
        $originalStockMovement = NgnStockMovement::find($id);

        if ($originalStockMovement) {
            // Get the original in and out values
            $originalIn = $originalStockMovement->in;
            $originalOut = $originalStockMovement->out;

            // Update the stock movement with validated data
            $originalStockMovement->update($request->validated());

            // Get the associated product to adjust global stock
            $product = NgnProduct::find($originalStockMovement->product_id);
            if ($product) {
                // Calculate the change in global stock
                $inChange = $request->in - $originalIn; // New in value minus original in value
                $outChange = $request->out - $originalOut; // New out value minus original out value

                // Update the global stock accordingly
                $product->global_stock += $inChange - $outChange;
                $product->save();
            }
        }

        return redirect()->back()->with('success', 'Stock movement updated successfully!');
    }

    private function createStockMovement($branchId, $productId, $transactionDate, $in, $out, $transactionType, $userId, $refDocNo, $remarks)
    {
        return NgnStockMovement::create([
            'branch_id' => $branchId,
            'product_id' => $productId,
            'transaction_date' => $transactionDate,
            'in' => $in,
            'out' => $out,
            'transaction_type' => $transactionType,
            'user_id' => $userId,
            'ref_doc_no' => $refDocNo,
            'remarks' => $remarks,
        ]);
    }

    private function updateGlobalStock($productId, $inChange, $outChange)
    {
        $product = NgnProduct::find($productId);
        if ($product) {
            $product->global_stock += $inChange - $outChange;
            \Log::info("Updated Global Stock: {$product->global_stock} (In: {$inChange}, Out: {$outChange})");
            $product->save();
        }
    }

    public function destroy($id)
    {
        // Find the stock movement record
        $stockMovement = NgnStockMovement::find($id);

        if ($stockMovement) {
            // Find the associated product
            $product = NgnProduct::find($stockMovement->product_id);

            if ($product) {
                // Adjust the global stock by reversing the stock movement
                $product->global_stock -= ($stockMovement->in - $stockMovement->out);
                $product->save();
            }

            // Delete the stock movement entry
            $stockMovement->delete();

            return response()->json(['success' => true, 'message' => 'Stock movement deleted successfully, and global stock adjusted!']);
        }

        return response()->json(['success' => false, 'message' => 'Stock movement not found.'], 404);
    }
}
