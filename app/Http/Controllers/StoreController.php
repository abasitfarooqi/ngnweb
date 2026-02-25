<?php

namespace App\Http\Controllers;

use App\Models\NgnBrand;
use App\Models\NgnCategory;
use App\Models\NgnModel;
use App\Models\NgnProduct;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function home()
    {
        // Logic for home page
        return view('frontend.ngnstore.home');
    }

    public function searchResults(Request $request)
    {
        $query = $request->input('query');

        // Split the query into individual keywords
        $keywords = preg_split('/\s+/', $query); // Split by whitespace

        // Search across multiple fields for products (using OR for broader matching)
        $products = NgnProduct::query();

        foreach ($keywords as $keyword) {
            $keyword = trim($keyword);
            if (! empty($keyword)) {
                $products->orWhere(function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', "%{$keyword}%")
                        ->orWhere('description', 'LIKE', "%{$keyword}%")
                        ->orWhere('variation', 'LIKE', "%{$keyword}%")
                        ->orWhereHas('brand', function ($q) use ($keyword) {
                            $q->where('name', 'LIKE', "%{$keyword}%");
                        });
                });
            }
        }

        // Get the results from the query
        $products = $products->get();

        // Static pages array
        $staticPages = [
            ['name' => 'SALES', 'url' => route('sale-motorcycles')],
            ['name' => 'NEW MOTORCYCLES', 'url' => route('motorcycles.new')],
            ['name' => 'USED MOTORCYCLES', 'url' => route('motorcycles.used')],
            ['name' => 'FINANCE', 'url' => url('/finance')],
            ['name' => 'ACCIDENT MANAGEMENT', 'url' => route('road-traffic-accidents')],
            ['name' => 'RENTALS', 'url' => route('rental-hire')],
            ['name' => 'SERVICES', 'url' => route('services')],
            ['name' => 'SHOP', 'url' => route('shop-motorcycle')],
            ['name' => 'ABOUT', 'url' => route('about.page')],
            ['name' => 'CONTACT', 'url' => route('contact.me')],
            ['name' => 'LOGIN', 'url' => route('login')],
            ['name' => 'REGISTER', 'url' => route('register')],
            ['name' => 'JOIN NGN CLUB', 'url' => url('/ngn-club/subscribe')],
            ['name' => 'CAREER', 'url' => url('/career')],
            ['name' => 'CALL BACK', 'url' => route('contact.call-back')],
            ['name' => 'CART', 'url' => route('product.cart')],
        ];

        // Search static pages
        $filteredPages = array_filter($staticPages, function ($page) use ($query) {
            return stripos($page['name'], $query) !== false; // Case-insensitive search
        });

        // // Log the query for debugging
        // logger('Search Query: ', ['query' => $query, 'products' => $products, 'pages' => $filteredPages]);

        // Return the results to the view
        return view('frontend.ngnstore.search-results', compact('query', 'products', 'filteredPages'));
    }
}
