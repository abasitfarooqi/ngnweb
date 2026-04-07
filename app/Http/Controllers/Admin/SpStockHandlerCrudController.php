<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SpStockHandlerRequest;
use App\Models\SpPart;
use App\Models\SpStockMovement;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SpStockHandlerCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    protected array $branchIds = [
        'catford' => 1,
        'tooting' => 2,
        'sutton' => 3,
    ];

    public function setup(): void
    {
        CRUD::setModel(SpPart::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/sp-stock-handler');
        CRUD::setEntityNameStrings('spare part stock handler', 'spare part stock handlers');
    }

    protected function setupListOperation(): void
    {
        $this->crud->query->select('sp_parts.*');
        $this->crud->query->addSelect([
            DB::raw('(SELECT COALESCE(SUM(`in`),0) - COALESCE(SUM(`out`),0) FROM sp_stock_movements sm WHERE sm.sp_part_id = sp_parts.id AND sm.branch_id = 1) AS catford_stock'),
            DB::raw('(SELECT COALESCE(SUM(`in`),0) - COALESCE(SUM(`out`),0) FROM sp_stock_movements sm WHERE sm.sp_part_id = sp_parts.id AND sm.branch_id = 2) AS tooting_stock'),
            DB::raw('(SELECT COALESCE(SUM(`in`),0) - COALESCE(SUM(`out`),0) FROM sp_stock_movements sm WHERE sm.sp_part_id = sp_parts.id AND sm.branch_id = 3) AS sutton_stock'),
        ]);

        CRUD::addColumn(['name' => 'part_number', 'type' => 'text', 'label' => 'Part number']);
        CRUD::addColumn(['name' => 'name', 'type' => 'text', 'label' => 'Part name']);
        CRUD::addColumn(['name' => 'global_stock', 'type' => 'number', 'label' => 'Global stock', 'decimals' => 2]);

        CRUD::addColumn([
            'name' => 'catford_stock',
            'type' => 'inline_sp_stock_edit',
            'label' => 'Catford stock',
        ]);

        CRUD::addColumn([
            'name' => 'tooting_stock',
            'type' => 'inline_sp_stock_edit',
            'label' => 'Tooting stock',
        ]);

        CRUD::addColumn([
            'name' => 'sutton_stock',
            'type' => 'inline_sp_stock_edit',
            'label' => 'Sutton stock',
        ]);

        Widget::add()->type('script')->content('assets/js/admin/forms/sp_inline_stock_edit.js');
    }

    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(SpStockHandlerRequest::class);

        CRUD::addField([
            'name' => 'sp_part_id',
            'label' => 'Spare part',
            'type' => 'select2_from_array',
            'options' => SpPart::query()->orderBy('part_number')->limit(2000)->get()->mapWithKeys(
                fn (SpPart $p) => [$p->id => $p->part_number.' — '.$p->name]
            )->all(),
            'allows_null' => true,
            'hint' => 'First 2000 parts by part number; use Stock handler list to edit stock for any part.',
        ]);

        CRUD::addField([
            'name' => 'part_number',
            'type' => 'text',
            'label' => 'Part number',
            'attributes' => [
                'readonly' => 'readonly',
            ],
        ]);

        CRUD::addField(['name' => 'catford_stock', 'type' => 'number', 'label' => 'Catford stock', 'hint' => 'Quantity at the Catford branch.']);
        CRUD::addField(['name' => 'tooting_stock', 'type' => 'number', 'label' => 'Tooting stock', 'hint' => 'Quantity at the Tooting branch.']);
        CRUD::addField(['name' => 'sutton_stock', 'type' => 'number', 'label' => 'Sutton stock', 'hint' => 'Quantity at the Sutton branch.']);

        Widget::add()->type('script')->content('assets/js/admin/forms/sp_part_details.js');
    }

    protected function setupUpdateOperation(): void
    {
        CRUD::setValidation(SpStockHandlerRequest::class);

        CRUD::addField([
            'name' => 'part_number',
            'type' => 'text',
            'label' => 'Part number',
            'attributes' => [
                'readonly' => 'readonly',
            ],
        ]);

        CRUD::addField([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Part name',
            'attributes' => [
                'readonly' => 'readonly',
            ],
        ]);

        CRUD::addField([
            'name' => 'catford_stock',
            'type' => 'number',
            'label' => 'Catford stock',
            'hint' => 'Quantity at the Catford branch.',
        ]);

        CRUD::addField([
            'name' => 'tooting_stock',
            'type' => 'number',
            'label' => 'Tooting stock',
            'hint' => 'Quantity at the Tooting branch.',
        ]);

        CRUD::addField([
            'name' => 'sutton_stock',
            'type' => 'number',
            'label' => 'Sutton stock',
            'hint' => 'Quantity at the Sutton branch.',
        ]);
    }

    public function store()
    {
        $data = $this->crud->getRequest()->all();
        $this->handleStockMovement($data);

        return redirect(backpack_url('sp-stock-handler'))->with('success', 'Stock saved.');
    }

    public function update()
    {
        $data = $this->crud->getRequest()->all();
        $this->handleStockMovement($data);

        return redirect(backpack_url('sp-stock-handler'))->with('success', 'Stock updated.');
    }

    protected function handleStockMovement(array $data): void
    {
        $part = isset($data['sp_part_id'])
            ? SpPart::find((int) $data['sp_part_id'])
            : SpPart::where('part_number', $data['part_number'] ?? '')->first();

        if (! $part) {
            Log::warning('Spare part stock handler: part not found.', ['data' => $data]);

            return;
        }

        $catfordStock = (int) ($data['catford_stock'] ?? 0);
        $tootingStock = (int) ($data['tooting_stock'] ?? 0);
        $suttonStock = (int) ($data['sutton_stock'] ?? 0);

        $existingCatford = $this->getCurrentStock($part->id, $this->branchIds['catford']);
        $existingTooting = $this->getCurrentStock($part->id, $this->branchIds['tooting']);
        $existingSutton = $this->getCurrentStock($part->id, $this->branchIds['sutton']);

        $this->updateStockMovement($part, $this->branchIds['catford'], $catfordStock - $existingCatford);
        $this->updateStockMovement($part, $this->branchIds['tooting'], $tootingStock - $existingTooting);
        $this->updateStockMovement($part, $this->branchIds['sutton'], $suttonStock - $existingSutton);

        $part->global_stock = $this->getCurrentStock($part->id, $this->branchIds['catford'])
            + $this->getCurrentStock($part->id, $this->branchIds['tooting'])
            + $this->getCurrentStock($part->id, $this->branchIds['sutton']);
        $part->save();
    }

    protected function getCurrentStock(int $partId, int $branchId): float
    {
        $in = SpStockMovement::where('sp_part_id', $partId)->where('branch_id', $branchId)->sum('in');
        $out = SpStockMovement::where('sp_part_id', $partId)->where('branch_id', $branchId)->sum('out');

        return (float) $in - (float) $out;
    }

    protected function updateStockMovement(SpPart $part, int $branchId, float $stockDiff): void
    {
        if ($stockDiff > 0) {
            $this->createStockMovement($part, $branchId, $stockDiff, 'IN', 'Stock Adjustment');
        } elseif ($stockDiff < 0) {
            $this->createStockMovement($part, $branchId, abs($stockDiff), 'OUT', 'Shop Sale');
        }
    }

    protected function createStockMovement(SpPart $part, int $branchId, float $stock, string $type, string $transactionType): void
    {
        SpStockMovement::create([
            'sp_part_id' => $part->id,
            'branch_id' => $branchId,
            'in' => $type === 'IN' ? $stock : 0,
            'out' => $type === 'OUT' ? $stock : 0,
            'transaction_type' => $transactionType,
            'transaction_date' => now(),
            'user_id' => auth()->id(),
        ]);
    }

    public function fetchPartData(Request $request)
    {
        $partId = (int) $request->get('id');
        $part = SpPart::find($partId);

        if (! $part) {
            return response()->json(['error' => 'Part not found'], 404);
        }

        return response()->json([
            'part_number' => $part->part_number,
            'catford_stock' => $this->getCurrentStock($part->id, $this->branchIds['catford']),
            'tooting_stock' => $this->getCurrentStock($part->id, $this->branchIds['tooting']),
            'sutton_stock' => $this->getCurrentStock($part->id, $this->branchIds['sutton']),
        ]);
    }

    public function updateStock(Request $request, int $id)
    {
        $field = $request->input('field');
        $value = (int) $request->input('value');

        if (! in_array($field, ['catford_stock', 'tooting_stock', 'sutton_stock'], true)) {
            return response()->json(['error' => 'Invalid field'], 400);
        }

        $part = SpPart::find($id);
        if (! $part) {
            return response()->json(['error' => 'Part not found'], 404);
        }

        $branchKey = str_replace('_stock', '', $field);
        $branchId = $this->branchIds[$branchKey] ?? null;
        if (! $branchId) {
            return response()->json(['error' => 'Invalid branch'], 400);
        }

        $existingStock = $this->getCurrentStock($id, $branchId);
        $stockDiff = $value - $existingStock;

        $this->updateStockMovement($part, $branchId, $stockDiff);

        $part->global_stock = $this->getCurrentStock($id, $this->branchIds['catford'])
            + $this->getCurrentStock($id, $this->branchIds['tooting'])
            + $this->getCurrentStock($id, $this->branchIds['sutton']);
        $part->save();

        return response()->json(['success' => true, 'global_stock' => $part->global_stock]);
    }
}
