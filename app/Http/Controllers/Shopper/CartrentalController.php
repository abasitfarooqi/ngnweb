<?php

namespace App\Http\Controllers\Shopper;

use App\Http\Controllers\Controller;
use App\Models\Motorcycle;
use App\Models\Oxford;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class CartrentalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cartCount = Cart::count();
        $cartItems = Cart::instance('default')->content();
        $cartTaxRate = config('cart.tax');
        $tax = config('cart.tax') / 100;
        $cartSubtotal = Cart::instance('default')->subtotal();
        $cartTax = $cartSubtotal * $tax;
        $newTotal = Cart::instance('default')->total();

        return view('frontend.cart', [
            'cartCount' => $cartCount,
            'cartItems' => $cartItems,
            'cartTaxRate' => $cartTaxRate,
            'tax' => $tax,
            'cartSubtotal' => $cartSubtotal,
            'cartTax' => $cartTax,
            'newTotal' => $newTotal,
        ]);
    }

    // Needs to be shown in users dashboard
    public function wishList()
    {
        return view('frontend.cart', [
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

        Cart::instance('default')->add($product_id, $product[0]->description, $quantity, $product[0]->price, 0, ['totalQty' => $totalQty, 'product_code' => $product[0]->sku, 'image' => $product[0]->image_url, 'details' => $product[0]->extended_description])->associate('App\Models\Oxford');

        /* Redirect to prevend re-adding on refreshing */
        return redirect()->route('product.cart')->withSuccess('Product has been successfully added to the Cart.');
    }

    public function storeRental(Request $request, $motorcycle_id)
    {
        // Find the motorcycle details
        $motorcycle = Motorcycle::where('id', $motorcycle_id)->first();

        $quantity = 1;
        $reservePrice = 20;

        // Add to the cart instance
        Cart::instance('default')->add($motorcycle->id, $motorcycle->model, $quantity, $reservePrice, 0);

        /* Redirect to prevend re-adding on refreshing */
        return redirect()->route('product.cart')->withSuccess('Product has been successfully added to the Cart.');
    }

    public function store(Request $request, $id)
    {
        // dd($request);
        $product = Oxford::select('id', 'sku', 'description', 'price', 'image_url', 'extended_description')
            ->where('id', $id)
            ->get();

        // $product = json_decode($prod);

        $quantity = $request->quantity;

        // dd($product);
        Cart::instance('default')->add($product[0]->sku, $product[0]->description, $quantity, $product[0]->price, 0, ['totalQty' => $quantity, 'product_code' => $product[0]->sku, 'image' => $product[0]->image_url, 'details' => $product[0]->extended_description])->associate('App\Models\Oxford');

        // dd($cart);

        /* Redirect to prevend re-adding on refreshing */
        return redirect()->route('product.cart')->withSuccess('Product has been successfully added to the Cart.');
    }

    public function checkout()
    {
        Cart::count();

        return view('frontend.checkout');
    }
}
