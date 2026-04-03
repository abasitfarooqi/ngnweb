<?php

namespace App\Http\Controllers\Admin;

use App\Exports\NgnStoreExport;
use App\Models\Branch;
use App\Models\NgnProduct;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class NgnStorePageController
 */
class NgnStorePageController extends Controller
{
    public function index(Request $request)
    {
        $query = NgnProduct::with(['brand', 'category', 'productModel', 'stockMovements.branch'])
            ->where('normal_price', '>', 0)
            ->where(function ($query) {
                $query->where('is_oxford', 1)
                    ->orWhere('is_ecommerce', 1);
            });

        // Apply filters
        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->input('name').'%');
        }

        if ($request->filled('sku')) {
            $query->where('sku', 'like', '%'.$request->input('sku').'%');
        }

        if ($request->filled('is_oxford')) {
            $query->where('is_oxford', $request->input('is_oxford'));
        }

        if ($request->filled('is_ecommerce')) {
            $query->where('is_ecommerce', $request->input('is_ecommerce'));
        }

        // Apply sorting
        if ($request->filled('sort_by')) {
            $query->orderBy($request->input('sort_by'), $request->input('sort_order', 'asc'));
        }

        // Paginate results
        $products = $query->paginate(10)->map(function ($product) {
            $branchesWithStock = $product->stockMovements->groupBy('branch_id')->map(function ($movements, $branchId) {
                $totalStock = $movements->reduce(function ($carry, $movement) {
                    return $carry + $movement->in - $movement->out;
                }, 0);

                return ['branch' => Branch::find($branchId)->name, 'stock' => $totalStock];
            });

            return [
                'Product Name' => $product->name,
                'SKU' => $product->sku,
                'Category' => $product->category->name,
                'Brand' => $product->brand->name,
                'Total Stock' => $branchesWithStock->sum('stock'),
                'Branches with Stock' => $branchesWithStock,
            ];
        });

        if ($request->ajax()) {
            return response()->json([
                'products' => view('livewire.agreements.migrated.admin.partials.product_list', compact('products'))->render(),
                'pagination' => (string) $products->appends($request->query())->links(),
            ]);
        }

        return view('livewire.agreements.migrated.admin.ngn_store_page', [
            'title' => 'Ngn Store Page',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'NgnStorePage' => false,
            ],
            'products' => $products,
            'page' => 'resources/views/admin/ngn_store_page.blade.php',
            'controller' => 'app/Http/Controllers/Admin/NgnStorePageController.php',
        ]);
    }

    public function exportCsv()
    {
        // Export product data to CSV
        return Excel::download(new NgnStoreExport, 'ngn_store_products_'.date('Y-m-d_H-i-s').'.csv');
    }
}
