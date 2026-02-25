<?php

namespace App\Http\Controllers;

use App\Models\OxfordProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OxfordProductsController extends Controller
{
    public function index(Request $request)
    {
        $categoryMappings = [
            '%FOOTWEAR%' => 'FOOTWEAR',
            '%GLOVES%' => 'GLOVES',
            '%CASUAL%' => 'CASUAL',
            '%LEGGINGS%' => 'LEGGINGS',
            '%JEANS%' => 'JEANS',
            '%RAINWEAR%' => 'RAINWEAR',
            '%HEADWEAR%' => 'HEADWEAR',
            '%LUGGAGE%' => 'LUGGAGE',
            '%SPARES%' => 'SPARES',
            '%HELMET%' => 'HELMET',
            '%ACCESSORIES%' => 'ACCESSORIES',
            '%ACCESSORY%' => 'ACCESSORIES',
            '%LIGHTING%' => 'LIGHTING',
            '%WORKSHOP%' => 'WORKSHOP',
            '%MINT%' => 'MINT',
            '%HOTGRIPS%' => 'HOTGRIPS',
            '%LOCKS%' => 'LOCKS',
            '%TRANSPORT%' => 'TRANSPORT',
            '%CLOTHING%' => 'CLOTHING',
            '%INTERCOMS%' => 'INTERCOMS',
            '%CARE%' => 'CARE',
            '%LAYERS%' => 'LAYERS',
            '%SUIT%' => 'SUIT',
            '%JACKET%' => 'JACKET',
            '%PANTS%' => 'PANTS',
        ];

        $queryPart = [];
        foreach ($categoryMappings as $pattern => $category) {
            $queryPart[] = "WHEN category LIKE '$pattern' THEN '$category'";
        }

        $caseQuery = implode(' ', $queryPart);

        $baseQuery = "
            WITH categorized_products AS (
                SELECT DISTINCT
                    CASE $caseQuery
                    ELSE category
                    END AS CATEGORY, brand
                FROM oxford_products
                WHERE dead = 0
            )
            SELECT CATEGORY, brand, COUNT(1) AS NO_ITEMS
            FROM categorized_products
            GROUP BY CATEGORY, brand
            ORDER BY NO_ITEMS DESC;
        ";

        $categoriesData = DB::select($baseQuery);

        $categoriesByBrand = collect($categoriesData)
            ->whereNotIn('brand', ['OXFORD/NOVA', 'SKIN SOLUTIONS', 'TRACKER'])
            ->groupBy('brand');

        $allCategories = collect($categoriesData)->groupBy('CATEGORY')->keys();

        $brands = OxfordProducts::select('brand')
            ->selectRaw('COUNT(1) AS NO_ITEMS')
            ->where('dead', 0)
            ->groupBy('brand')
            ->orderByDesc('NO_ITEMS')
            ->get();

        $cat = $request->route('category');
        $brand = $request->route('brand');
        $itemsPerPage = $request->query('page', 10);
        $pageNo = $request->query('page_no', 1);

        $productsQuery = OxfordProducts::where('category', 'like', "%$cat%")
            ->when($brand, function ($query, $brand) {
                return $query->where('brand', 'like', "%$brand%");
            })
            ->where('dead', 0)
            ->orderBy('brand');

        $products = $productsQuery->paginate($itemsPerPage, ['*'], 'page_no', $pageNo);

        // -- to get orignal anchor to specific category of specific brands
        $catByBRAND = OxfordProducts::select('brand', 'category')->distinct()
            ->selectRaw('COUNT(1) AS NO_ITEMS')
            ->where('dead', 0)
            ->groupBy('brand', 'category')
            ->orderBy('brand')
            ->orderBy('category')
            ->get()
            ->groupBy('brand');

        return view('oxfordproducts.index', compact('cat', 'catByBRAND', 'categoriesByBrand', 'brands', 'products', 'allCategories'));
    }

    public function productsByCategory(Request $request)
    {
        $categoryMappings = [
            '%JACKET%' => 'JACKET',
            '%FOOTWEAR%' => 'FOOTWEAR',
            '%GLOVES%' => 'GLOVES',
            '%PANTS%' => 'PANTS',
            '%CASUAL%' => 'CASUAL',
            '%LEGGINGS%' => 'LEGGINGS',
            '%JEANS%' => 'JEANS',
            '%RAINWEAR%' => 'RAINWEAR',
            '%SUIT%' => 'SUIT',
            '%HEADWEAR%' => 'HEADWEAR',
            '%LUGGAGE%' => 'LUGGAGE',
            '%SPARES%' => 'SPARES',
            '%HELMET%' => 'HELMET',
            '%ACCESSORIES%' => 'ACCESSORIES',
            '%ACCESSORY%' => 'ACCESSORIES',
            '%LIGHTING%' => 'LIGHTING',
            '%WORKSHOP%' => 'WORKSHOP',
            '%MINT%' => 'MINT',
            '%HOTGRIPS%' => 'HOTGRIPS',
            '%LOCKS%' => 'LOCKS',
            '%TRANSPORT%' => 'TRANSPORT',
            '%CLOTHING%' => 'CLOTHING',
            '%INTERCOMS%' => 'INTERCOMS',
            '%CARE%' => 'CARE',
            '%LAYERS%' => 'LAYERS',
        ];

        $queryPart = [];
        foreach ($categoryMappings as $pattern => $category) {
            $queryPart[] = "WHEN category LIKE '$pattern' THEN '$category'";
        }
        $caseQuery = implode(' ', $queryPart);

        $baseQuery = "
            WITH categorized_products AS (
                SELECT DISTINCT
                    CASE $caseQuery
                    ELSE category
                    END AS CATEGORY, brand
                FROM oxford_products
                WHERE dead = 0
                order by image_url desc
            )
            SELECT CATEGORY, brand, COUNT(1) AS NO_ITEMS
            FROM categorized_products
            GROUP BY CATEGORY, brand
            ORDER BY NO_ITEMS DESC;
        ";

        $categoriesData = DB::select($baseQuery);

        $categoriesByBrand = collect($categoriesData)->groupBy('brand');
        $allCategories = collect($categoriesData)->groupBy('CATEGORY')->keys();

        $brands = OxfordProducts::select('brand')
            ->selectRaw('COUNT(1) AS NO_ITEMS')
            ->where('dead', 0)
            ->groupBy('brand')
            ->orderByDesc('NO_ITEMS')
            ->orderByDesc('image_url')
            ->get();

        $cat = $request->route('category');
        $brand = $request->route('brand');
        $itemsPerPage = $request->query('page', 10);
        $pageNo = $request->query('page_no', 1);

        $productsQuery = OxfordProducts::where('category', 'like', "%$cat%")
            ->when($brand, function ($query, $brand) {
                return $query->where('brand', 'like', "%$brand%");
            })
            ->where('dead', 0)
            ->orderBy('brand')
            ->orderByDesc('image_url');

        $products = $productsQuery->paginate($itemsPerPage, ['*'], 'page_no', $pageNo);

        return view('oxfordproducts.products_by_category', compact('categoriesByBrand', 'brands', 'products', 'allCategories', 'cat', 'brand', 'itemsPerPage'));
    }

    public function brands()
    {
        $brands = OxfordProducts::select('brand')->distinct()->get();

        $categoriesByBrand = OxfordProducts::select('brand', 'category')->distinct()
            ->orderBy('brand')
            ->orderBy('category')
            ->get()
            ->groupBy('brand');

        return view('oxfordproducts.brands', compact('categoriesByBrand'));
    }

    public function categoriesByBrandF($brand)
    {
        $categories = OxfordProducts::select('category')
            ->where('brand', $brand)
            ->distinct()
            ->get();

        return view('oxfordproducts.categories_by_brand', compact('brand', 'categories'));
    }

    public function productsByCategoryAndBrand($brand, $category)
    {
        $products = OxfordProducts::where('brand', $brand)
            ->where('category', $category)
            ->get();

        return view('oxfordproducts.products_by_category_and_brand', compact('brand', 'category', 'products'));
    }

    public function showProduct($id)
    {
        $product = OxfordProducts::findOrFail($id);

        return view('oxfordproducts.show', compact('product'));
    }
}
