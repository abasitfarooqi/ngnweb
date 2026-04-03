<?php

namespace App\Http\Controllers\Shopper;

use App\Http\Controllers\Controller;
use App\Models\Oxford;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cartCount = Cart::count();
        $cartItems = Cart::instance('default')->content();
        // $cartTaxRate = config('cart.tax');
        // $tax = config('cart.tax') / 100;
        $cartSubtotal = Cart::instance('default')->subtotal();
        // $cartTax = $cartSubtotal * $tax;
        $newTotal = Cart::instance('default')->total();

        return view('livewire.agreements.migrated.frontend.cart', [
            'cartCount' => $cartCount,
            'cartItems' => $cartItems,
            // 'cartTaxRate' => $cartTaxRate,
            // 'tax' => $tax,
            'cartSubtotal' => $cartSubtotal,
            // 'cartTax' => $cartTax,
            'newTotal' => $newTotal,
        ]);
    }

    // Needs to be shown in users dashboard
    public function wishList()
    {
        return view('livewire.agreements.migrated.frontend.cart', [
            'cartItems' => Cart::instance('wishlist')->content(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add($product_id)
    {
        $product = Oxford::select('id', 'sku', 'description', 'price', 'image_url', 'extended_description')
            ->where('id', $product_id)
            ->get();

        $quantity = 1;
        $totalQty = 1;
        $vat = 20;

        Cart::instance('default')->add($product_id, $product[0]->description, $quantity, $product[0]->price, 0, ['totalQty' => $totalQty, 'product_code' => $product[0]->sku, 'image' => $product[0]->image_url, 'details' => $product[0]->extended_description])->associate('App\Models\Oxford');

        /* Redirect to prevent re-adding on refreshing */
        return redirect()->route('product.cart')->withSuccess('Product has been successfully added to the Cart.');
    }

    public function store(Request $request, $id)
    {
        $product = Oxford::select('id', 'sku', 'description', 'price', 'image_url', 'extended_description', 'category_id')
            ->where('id', $id)
            ->first();

        $quantity = $request->quantity;

        // Determine whether the product is taxed
        if ($product->category_id == 1) {
            // If helmets category then no tax
            $vat = 0;
            Cart::add($product->id, $product->description, $request->quantity, $product->price);
        } else {
            // All other categories require the addition of VAT

            // Calculate how much VAT to be added to the price
            $vat = $product->price / 1.2;

            $productPrice = $product->price + $vat;

            Cart::instance('default')->add($product->sku, $product->description, $quantity, $productPrice, 0, ['totalQty' => $quantity, 'product_code' => $product->sku, 'image' => $product->image_url, 'details' => $product->extended_description])->associate('App\Models\Oxford');
        }

        dd(Cart::content());

        /* Redirect to prevend re-adding on refreshing */
        return redirect()->route('product.cart')->withSuccess('Product has been successfully added to the Cart.');
    }

    public function storeRental()
    {
        echo 'Store rental to cart';
    }

    public function checkout()
    {
        Cart::count();

        return view('livewire.agreements.migrated.frontend.checkout');
    }

    public function delete(Request $request, $rowId)
    {
        Cart::remove($rowId);

        return redirect()->back();
    }
}
