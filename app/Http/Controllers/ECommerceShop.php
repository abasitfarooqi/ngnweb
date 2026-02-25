<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Ecommerce\EcOrder;
use App\Models\Ecommerce\EcOrderItem;
use App\Models\Ecommerce\EcOrderShipping;
use App\Models\Ecommerce\EcPaymentMethod;
use App\Models\Ecommerce\EcShippingMethod;
use App\Models\NgnBrand;
use App\Models\NgnCategory;
use App\Models\NgnProduct;
use App\Models\PaymentsPaypal;
use App\Models\SystemCountry;
use App\Models\TermsVersion;
use Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ECommerceShop extends Controller
{
    /**
     * Route: GET /api/v1/shop/products
     * Frontend: shopAPI.getProducts() -> /api/v1/shopX?page=1&per_page=12&
     *                                  sort=[newest|price_low|price_high|name]&
     *                                  category=[category_id]&brand=[brand_id]&
     *                                  search=[search_term]&brands=[slug|slug,slug]&
     *                                  categories=[slug|slug,slug]
     */
    public function getProducts(Request $request): JsonResponse
    {
        // ---------------------------------------------------------
        // 1) MAIN QUERY with GROUPING (for actual data rows)
        // ---------------------------------------------------------
        $query = NgnProduct::select(
            'ngn_products.name as name',
            'ngn_products.slug',
            'ngn_products.image_url as image_url',
            'ngn_brands.name as brand',
            'ngn_categories.name as category',
            'ngn_models.name as model',
            'ngn_products.normal_price',
            DB::raw('SUM(ngn_products.global_stock) as global_stock'),
            DB::raw('MAX(ngn_products.created_at) as max_created_at')
        )
            ->join('ngn_models', 'ngn_products.model_id', '=', 'ngn_models.id')
            ->join('ngn_brands', 'ngn_products.brand_id', '=', 'ngn_brands.id')
            ->join('ngn_categories', 'ngn_products.category_id', '=', 'ngn_categories.id')
            ->where('ngn_products.is_ecommerce', 1)
            ->where('ngn_products.slug', '!=', null)
            ->where('ngn_products.slug', '!=', '')
            ->where('ngn_products.dead', false)
            ->groupBy(
                'ngn_products.name',
                'ngn_products.slug',
                'ngn_products.image_url',
                'ngn_brands.name',
                'ngn_categories.name',
                'ngn_models.name',
                'ngn_products.normal_price',
            );

        // ---------------------------------------------------------
        // 2) COUNT QUERY (NO GROUPING) // NEW
        // ---------------------------------------------------------
        // We create a separate query that applies identical FILTERS
        // but does NOT do sum(), max() or groupBy()
        $countQuery = NgnProduct::query()
            ->join('ngn_models', 'ngn_products.model_id', '=', 'ngn_models.id')
            ->join('ngn_brands', 'ngn_products.brand_id', '=', 'ngn_brands.id')
            ->join('ngn_categories', 'ngn_products.category_id', '=', 'ngn_categories.id')
            ->where('ngn_products.is_ecommerce', 1)
            ->where('ngn_products.slug', '!=', null)
            ->where('ngn_products.slug', '!=', '')
            ->where('ngn_products.dead', 0);
        // ---------------------------------------------------------
        // 3) Apply the same FILTERS to BOTH queries
        // ---------------------------------------------------------
        if ($request->filled('category')) {
            $categories = is_array($request->category)
                ? $request->category
                : explode(',', $request->category);

            // Main (grouped) query
            $query->whereIn('ngn_products.category_id', $categories);
            // Count query
            $countQuery->whereIn('ngn_products.category_id', $categories);
        }

        if ($request->filled('brands')) {
            $brands = is_array($request->brands)
                ? $request->brands
                : explode(',', $request->brands);

            $query->whereIn('ngn_brands.slug', $brands);
            $countQuery->whereIn('ngn_brands.slug', $brands);
        }

        if ($request->filled('categories')) {
            $categories = is_array($request->categories)
                ? $request->categories
                : explode(',', $request->categories);

            $query->whereIn('ngn_categories.slug', $categories);
            $countQuery->whereIn('ngn_categories.slug', $categories);
        }

        if ($request->filled('brand')) {
            $brands = is_array($request->brand)
                ? $request->brand
                : explode(',', $request->brand);

            $query->whereIn('ngn_products.brand_id', $brands);
            $countQuery->whereIn('ngn_products.brand_id', $brands);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            if ($search !== '') {
                $terms = preg_split('/[\s,-]+/', $search, -1, PREG_SPLIT_NO_EMPTY);

                $query->where(function ($q) use ($terms) {
                    foreach ($terms as $term) {
                        $q->where(function ($subQuery) use ($term) {
                            $subQuery->where('ngn_products.name', 'like', "%{$term}%")
                                ->orWhere('ngn_products.sku', 'like', "%{$term}%")
                                ->orWhere('ngn_products.description', 'like', "%{$term}%")
                                ->orWhere('ngn_products.variation', 'like', "%{$term}%")
                                ->orWhere('ngn_products.ean', 'like', "%{$term}%");
                        });
                    }
                });

                // Do same for count query
                $countQuery->where(function ($q) use ($terms) {
                    foreach ($terms as $term) {
                        $q->where(function ($subQuery) use ($term) {
                            $subQuery->where('ngn_products.name', 'like', "%{$term}%")
                                ->orWhere('ngn_products.sku', 'like', "%{$term}%")
                                ->orWhere('ngn_products.description', 'like', "%{$term}%")
                                ->orWhere('ngn_products.variation', 'like', "%{$term}%")
                                ->orWhere('ngn_products.ean', 'like', "%{$term}%");
                        });
                    }
                });
            }
        }

        // 'has_image' or not
        if ($request->filled('has_image') && $request->boolean('has_image')) {
            $query->where('ngn_products.image_url', 'not like', '%neguinho%');
            $countQuery->where('ngn_products.image_url', 'not like', '%neguinho%');
        }

        // ---------------------------------------------------------
        // 4) Get the total COUNT of DISTINCT products // NEW
        // ---------------------------------------------------------
        // Here we simply count distinct product IDs
        $totalCount = $countQuery->distinct('ngn_products.id')
            ->count('ngn_products.id');

        // ---------------------------------------------------------
        // 5) Figure out pagination
        // ---------------------------------------------------------
        $perPage = (int) $request->input('per_page', 12);
        $page = (int) $request->input('page', 1);

        // If there's at least 1 item, compute real max pages
        $maxPages = $totalCount > 0
            ? (int) ceil($totalCount / $perPage)
            : 1;

        // Only clamp if out of range
        if ($page < 1) {
            $page = 1;
        } elseif ($page > $maxPages) {
            $page = $maxPages;  // or set to $maxPages
        }

        // ---------------------------------------------------------
        // 6) Apply sorting to the MAIN (grouped) query
        // ---------------------------------------------------------
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('ngn_products.normal_price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('ngn_products.normal_price', 'desc');
                    break;
                case 'name':
                    $query->orderBy('ngn_products.name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('ngn_products.name', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('max_created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('max_created_at', 'asc');
                    break;
                default:
                    $query->orderBy('max_created_at', 'desc');
                    break;
            }
        } else {
            // Default
            $query->orderBy('max_created_at', 'desc');
        }

        // ---------------------------------------------------------
        // 7) Finally paginate the MAIN (grouped) query
        // ---------------------------------------------------------
        try {
            $products = $query->paginate($perPage, ['*'], 'page', $page);

            // The paginator will have "total" set to what it thinks is right,
            // but you can re-inject the real total if needed:
            // $products->total() = $totalCount; // (Laravel doesn't allow direct re-assignment)
            // A different approach is to create a custom paginator or read docs for customizing totals.
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Error fetching products: '.$e->getMessage());

            return response()->json([
                'error' => 'Failed to fetch products. Please try again later.',
            ], 500);
        }
    }

    /**
     * Route: GET /api/v1/shop/product/{slug}
     * Frontend: shopAPI.getProductBySlug() -> /api/v1/shop/[slug]
     *
     * @param  string  $slug
     */
    public function getProductBySlug($slug): JsonResponse
    {
        // STEP 1: Retrieve all products matching the slug and marked for e-commerce
        $products = DB::table('ngn_products')
            ->where('slug', 'like', $slug.'%')
            ->where('is_ecommerce', true)
            ->where('dead', false)
            ->get();

        // If no products are found, return a 404 response
        if ($products->isEmpty()) {
            return response()->json(['message' => 'No product found'], 404);
        }

        // Count the number of products retrieved
        $product_count = $products->count();

        // STEP 2: Fetch unique image URLs from ngn_product_images for all related product IDs
        $productIds = $products->pluck('id')->toArray(); // Ensure it's an array
        $uniqueImages = DB::table('ngn_product_images')
            ->whereIn('product_id', $productIds)
            ->pluck('image_url')
            ->unique()
            ->values()
            ->all();

        // Original image_url from the first product (for backward compatibility)
        $originalImageUrl = $products[0]->image_url;

        // STEP 3: Sanitize the descriptions by stripping HTML tags
        $cleanDescription = strip_tags($products[0]->description);
        $cleanExtendedDescription = strip_tags($products[0]->extended_description);

        // STEP 4: Prepare redundant data with the addition of image_array
        $redundantData = [
            'normal_price' => $products[0]->normal_price,
            'global_stock' => $products[0]->global_stock,
            'meta_title' => $products[0]->meta_title,
            'meta_description' => $products[0]->meta_description,
            'image_url' => $originalImageUrl, // Existing single image URL
            'image_array' => $uniqueImages,    // New array of unique image URLs
            'description' => $cleanDescription,
            'extended_description' => $cleanExtendedDescription,
            'colour' => $products[0]->colour,
            'counts' => $product_count,
        ];

        // STEP 5: Map each product to include necessary fields as objects
        $productArray = $products->map(function ($product) {
            return (object) [
                'id' => $product->id,
                'sku' => trim($product->sku),
                'name' => trim($product->name),
                'variation' => trim($product->variation),
                'slug' => trim($product->slug),
            ];
        });

        // STEP 6: Calculate total_balances for all products
        $totalBalances = $this->calculateTotalBalances($productIds);

        // STEP 7: Append total_balance to each product
        $productArray = $productArray->map(function ($product) use ($totalBalances) {
            $product->total_balance = isset($totalBalances[$product->id]) ? $totalBalances[$product->id] : 0;

            return $product;
        });

        // STEP 8: Combine redundant data with the product variations
        $response = [
            'redundantData' => $redundantData,
            'products' => $productArray,
        ];

        return response()->json($response);
    }

    /**
     * Route: GET /api/v1/shop/product-availability/{id}
     * Frontend: shopAPI.getProductAvailability() -> /api/v1/shop/product-availability/[id]
     *
     * @param  int  $id
     */
    public function getProductAvailability($id): JsonResponse
    {
        // Calculate total_balance for the given product_id
        $total_balance = $this->calculateTotalBalances([$id])[$id] ?? 0;

        // Retrieve branch-wise balance
        $balance = DB::table('ngn_stock_movements')
            ->where('product_id', $id)
            ->select(
                'branch_id',
                DB::raw('(SELECT name FROM branches WHERE id = branch_id) AS branch_name'),
                DB::raw('SUM(`in`) - SUM(`out`) AS branch_balance')
            )
            ->groupBy('branch_id')
            ->get();

        \Log::info('Product Availability: ', [$id, $balance, $total_balance]);

        return response()->json([
            'product_id' => (string) $id,
            'balance' => $balance,
            'total_balance' => $total_balance,
        ]);
    }

    /**
     * Private method to calculate total_balance for multiple product_ids.
     */
    private function calculateTotalBalances(array $productIds): array
    {
        // Initialize the result array with default total_balance as 0
        $result = array_fill_keys($productIds, 0);

        if (empty($productIds)) {
            return $result;
        }

        // Fetch branch-wise balances grouped by product_id and branch_id
        $balances = DB::table('ngn_stock_movements')
            ->whereIn('product_id', $productIds)
            ->select(
                'product_id',
                'branch_id',
                DB::raw('SUM(`in`) - SUM(`out`) AS branch_balance')
            )
            ->groupBy('product_id', 'branch_id')
            ->get();

        // Calculate total_balance for each product_id
        foreach ($balances as $balance) {
            if (isset($result[$balance->product_id])) {
                $result[$balance->product_id] += floatval($balance->branch_balance);
            }
        }

        return $result;
    }

    /**
     * Route: GET /api/v1/shop/brands
     * Frontend: shopAPI.getBrands() -> /api/v1/shop/brands
     *
     * @return JsonResponse
     */
    public function getBrands()
    {
        // $brands = NgnBrand::get();
        cache()->forget('brands');
        $brands = cache()->remember('brands', 3600, function () {
            return NgnBrand::select('id', 'name', 'image_url', 'slug', 'description')
                ->where('is_ecommerce', true)
                ->orderBy('name')
                ->get();
        });

        return response()->json($brands);
    }

    /**
     * Route: GET /api/v1/shop/categories
     * Frontend: shopAPI.getCategories() -> /api/v1/shop/categories
     *
     * @return JsonResponse
     */
    public function getCategories()
    {
        cache()->forget('categories');
        $categories = cache()->remember('categories', 3600, function () {
            return NgnCategory::select('id', 'name', 'image_url', 'slug', 'description')
                ->where('is_ecommerce', true)
                ->orderBy('name')
                ->get();
        });

        return response()->json($categories);
    }

    /**
     * Route: GET /api/v1/shop/product/{id}
     * Frontend: shopAPI.getProductById() -> /api/v1/shop/product/[id]
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function getProductById($id)
    {
        $product = NgnProduct::with('brand', 'category', 'model')
            ->where('id', $id)
            ->where('is_ecommerce', 1)
            ->where('dead', false)
            ->first();

        return response()->json($product);
    }

    /**
     * Route: POST /api/v1/shop/orders
     * Frontend: shopAPI.createOrder() -> /api/v1/shop/orders
     *
     * @return JsonResponse
     */
    public function createOrder(Request $request)
    {
        \Log::info('Create Order Request: ', $request->all());

        $paymentMethodId = 3;

        // Get authenticated customer instead of user
        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            DB::beginTransaction();

            // Get shipping details from request
            $shippingDetails = $request->input('shipping_details');

            // Retrieve and sanitize items from request
            $items = $request->input('items', []);
            if (empty($items)) {
                throw ValidationException::withMessages(['items' => 'Your cart is empty.']);
            }

            // Fetch all products in one query to optimize performance
            $productIds = collect($items)->pluck('product_id')->unique();
            $products = NgnProduct::whereIn('id', $productIds)->get()->keyBy('id');

            // Validate all product IDs exist
            if ($products->count() !== $productIds->count()) {
                throw ValidationException::withMessages(['items' => 'One or more selected products are invalid.']);
            }

            // Calculate totals from items
            $totalAmount = collect($items)->reduce(function ($carry, $item) use ($products) {
                return $carry + ($products[$item['product_id']]->normal_price * $item['quantity']);
            }, 0);

            // Create default UK address if customer has no addresses
            $customerAddress = CustomerAddress::where('customer_id', $customer->customer_id)->first();

            if (! $customerAddress) {
                $customerAddress = CustomerAddress::create([
                    'customer_id' => $customer->customer_id,
                    'last_name' => '-',
                    'first_name' => '-',
                    'company_name' => '-',
                    'street_address' => '-',
                    'street_address_plus' => '-',
                    'postcode' => '-',
                    'city' => '-',
                    'phone_number' => '-',
                    'is_default' => true,
                    'type' => 'billing',
                    'country_id' => 3, // UK
                ]);

            }

            // Merge shipping details into request for validation
            if ($shippingDetails) {
                $request->merge([
                    'shipping_method_id' => $shippingDetails['method_id'],
                    'payment_method_id' => $paymentMethodId,
                    'customer_address_id' => $customerAddress->id,
                    'total_amount' => $totalAmount,
                    'grand_total' => $totalAmount + $shippingDetails['shipping_cost'],
                    'shipping_cost' => $shippingDetails['shipping_cost'],
                    'tax' => 0,
                    'discount' => 0,
                    'branch_id' => $shippingDetails['branch_id'] ?? null, // Handle branch_id
                ]);
            }

            // Define validation rules
            $rules = [
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:ngn_products,id',
                'items.*.quantity' => 'required|integer|min:1|max:100',
                'shipping_cost' => 'required|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'tax' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'grand_total' => 'required|numeric|min:0',
                'shipping_method_id' => 'required|exists:ec_shipping_methods,id',
                'customer_address_id' => 'required|exists:customer_addresses,id',
                'branch_id' => 'nullable|exists:branches,id', // Validate branch_id if present
            ];

            // Define custom validation messages
            $messages = [
                'items.required' => 'Your cart is empty.',
                'items.array' => 'Invalid cart data format.',
                'items.min' => 'Your cart must contain at least one item.',
                'items.*.product_id.exists' => 'One or more selected products are invalid.',
                'items.*.quantity.min' => 'Each item must have at least one quantity.',
                'items.*.quantity.max' => 'Each item cannot exceed 100 in quantity.',
                'branch_id.exists' => 'Selected branch does not exist.',
                // Add more custom messages as needed
            ];

            // Validate the request
            $validated = $request->validate($rules, $messages);

            // Check for existing pending order
            $existingOrder = EcOrder::where('customer_id', $customer->id)
                ->where('order_status', 'pending')
                ->where('payment_status', 'pending')
                ->lockForUpdate()
                ->first();

            if ($existingOrder) {
                Log::info('Found existing pending order', [
                    'customer_id' => $customer->id,
                    'order_id' => $existingOrder->id,
                ]);

                // Iterate through items to update or add
                foreach ($validated['items'] as $item) {
                    $orderItem = EcOrderItem::where('order_id', $existingOrder->id)
                        ->where('product_id', $item['product_id'])
                        ->first();

                    if ($orderItem) {

                        \Log::info('Foreach.Order Item: ', [
                            'order_id' => $existingOrder->id,
                            'order_item_id' => $orderItem->id,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                        ]);

                        $orderItem->quantity += $item['quantity'];
                        $orderItem->total_price = $orderItem->unit_price * $orderItem->quantity;
                        $orderItem->tax = ($orderItem->unit_price * $orderItem->quantity) - ($orderItem->unit_price * $orderItem->quantity / 1.2);
                        $orderItem->line_total = $orderItem->unit_price * $orderItem->quantity;
                        $orderItem->save();

                        Log::info('Updated existing order item quantity', [
                            'order_id' => $existingOrder->id,
                            'order_item_id' => $orderItem->id,
                            'new_quantity' => $orderItem->quantity,
                        ]);
                    } else {
                        // Add new item to the existing order
                        EcOrderItem::create([
                            'order_id' => $existingOrder->id,
                            'product_id' => $item['product_id'],
                            'product_name' => $products[$item['product_id']]->name,
                            'sku' => $products[$item['product_id']]->sku,
                            'quantity' => $item['quantity'],
                            'unit_price' => $products[$item['product_id']]->normal_price,
                            'total_price' => $products[$item['product_id']]->normal_price * $item['quantity'],
                            'tax' => ($products[$item['product_id']]->normal_price * $item['quantity']) - ($products[$item['product_id']]->normal_price * $item['quantity'] / 1.2),
                            'discount' => 0,
                            'line_total' => $products[$item['product_id']]->normal_price * $item['quantity'],
                        ]);

                        Log::info('Added new item to existing order', [
                            'order_id' => $existingOrder->id,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                        ]);
                    }
                }

                // Update branch_id if shipping method is in-store pickup
                if ($shippingDetails && $shippingDetails['is_store_pickup']) {
                    $existingOrder->branch_id = $validated['branch_id'];
                    $existingOrder->save();

                    Log::info('Updated existing order with branch_id', [
                        'order_id' => $existingOrder->id,
                        'branch_id' => $validated['branch_id'],
                    ]);
                }

                DB::commit();

                // Clear backend session data after successful transfer
                $request->session()->forget('cart');
                $request->session()->forget('previous_product');
                $request->session()->forget('shipping_details');

                return response()->json([
                    'message' => 'Order updated successfully',
                    'order_id' => $existingOrder->id,
                ], 200);
            } else {
                // Create new order as there is no existing pending order
                $orderData = [
                    'customer_id' => $customer->id,
                    'shipping_method_id' => $validated['shipping_method_id'],
                    'payment_method_id' => $paymentMethodId,
                    'customer_address_id' => $customerAddress->id,
                    'order_status' => 'pending',
                    'shipping_status' => 'pending',
                    'payment_status' => 'pending',
                    'shipping_cost' => $validated['shipping_cost'],
                    'total_amount' => $validated['total_amount'],
                    'tax' => $validated['tax'],
                    'discount' => $validated['discount'] ?? 0,
                    'grand_total' => $validated['total_amount'] + $validated['shipping_cost'] - ($validated['discount'] ?? 0),
                    'currency' => 'GBP',
                    'branch_id' => $validated['branch_id'] ?? null,
                ];

                $order = EcOrder::create($orderData);

                // Log order creation
                Log::info('Order created successfully', [
                    'customer_id' => $customer->id,
                    'order_id' => $order->id,
                    'is_store_pickup' => $shippingDetails['is_store_pickup'] ?? false,
                    'total_amount' => $validated['total_amount'],
                ]);

                // Create order items
                foreach ($validated['items'] as $item) {
                    EcOrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'product_name' => $products[$item['product_id']]->name,
                        'sku' => $products[$item['product_id']]->sku,
                        'quantity' => $item['quantity'],
                        'unit_price' => $products[$item['product_id']]->normal_price,
                        'total_price' => $products[$item['product_id']]->normal_price * $item['quantity'],
                        'tax' => ($products[$item['product_id']]->normal_price * $item['quantity']) - ($products[$item['product_id']]->normal_price * $item['quantity'] / 1.2),
                        'discount' => $validated['discount'] ?? 0,
                        'line_total' => $products[$item['product_id']]->normal_price * $item['quantity'],
                    ]);
                }

                // Create order shipping record
                if (! $shippingDetails) {
                    $fulfillmentMethod = $shippingDetails['is_store_pickup'] ?
                        EcOrderShipping::FULFILLMENT_PICKUP :
                        EcOrderShipping::FULFILLMENT_CARRIER;

                    $shippingNotes = $shippingDetails['is_store_pickup'] ?
                        'Store Pickup at Branch: '.($shippingDetails['branch_details']['name'] ?? 'Unknown') :
                        null;

                    EcOrderShipping::create([
                        'order_id' => $order->id,
                        'fulfillment_method' => $fulfillmentMethod,
                        'status' => 'processing',
                        'notes' => $shippingNotes,
                        'processing_at' => now(),
                    ]);

                    Log::info('Order shipping record created', [
                        'order_id' => $order->id,
                        'fulfillment_method' => $fulfillmentMethod,
                        'shipping_notes' => $shippingNotes,
                    ]);
                }

                DB::commit();

                $request->session()->forget('cart');
                $request->session()->forget('previous_product');
                $request->session()->forget('shipping_details');

                return response()->json([
                    'message' => 'Order created successfully',
                    'order_id' => $order->id,
                ], 201);
            }
        } catch (ValidationException $ve) {
            DB::rollBack();
            Log::warning('Order creation validation failed', [
                'customer_id' => $customer->id,
                'errors' => $ve->errors(),
            ]);

            return response()->json([
                'error' => 'Validation Failed',
                'messages' => $ve->errors(),
            ], 422);
        } catch (ModelNotFoundException $mnfe) {
            DB::rollBack();
            Log::error('Model not found during order creation', [
                'customer_id' => $customer->id,
                'error' => $mnfe->getMessage(),
            ]);

            return response()->json([
                'error' => 'Resource Not Found',
                'message' => $mnfe->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to create order',
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }

    /**
     * Route: GET /api/v1/shop/cart-pending-order
     * Frontend: shopAPI.getCartPendingOrder() -> /api/v1/shop/cart-pending-order
     *
     * @return JsonResponse
     */
    public function getCartPendingOrder()
    {
        try {
            // Retrieve the authenticated customer
            $customer = Auth::guard('customer')->user();
            if (! $customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.',
                ], 401);
            }

            // Fetch the pending order with its items
            $pendingOrder = EcOrder::with('orderItems')
                ->where('customer_id', $customer->id)
                ->where('order_status', 'pending')
                ->where('payment_status', 'pending')
                ->first();

            if (! $pendingOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending order found.',
                ], 404);
            }

            // Structure the order data
            $orderData = [
                'id' => $pendingOrder->id,
                'items' => $pendingOrder->orderItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'sku' => $item->sku,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                        'tax' => $item->tax,
                        'discount' => $item->discount,
                        'line_total' => $item->line_total,
                    ];
                }),
                'total_amount' => $pendingOrder->total_amount,
                'grand_total' => $pendingOrder->grand_total + $pendingOrder->shipping_cost,
                'shipping_cost' => $pendingOrder->shipping_cost,
                'tax' => $pendingOrder->tax,
                'discount' => $pendingOrder->discount,
                'currency' => $pendingOrder->currency,
                'shipping_method_id' => $pendingOrder->shipping_method_id,
                'payment_method_id' => $pendingOrder->payment_method_id,
                'customer_address_id' => $pendingOrder->customer_address_id,
                'order_status' => $pendingOrder->order_status,
                'payment_status' => $pendingOrder->payment_status,
                'shipping_status' => $pendingOrder->shipping_status,
                'branch_id' => $pendingOrder->branch_id,
                'created_at' => $pendingOrder->created_at,
                'updated_at' => $pendingOrder->updated_at,
            ];

            return response()->json([
                'success' => true,
                'order' => $orderData,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching pending order', [
                'customer_id' => optional(Auth::guard('customer')->user())->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch pending order.',
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }

    /**
     * Add item to pending order for logged-in customer
     *
     * @return JsonResponse
     */
    public function addOrderItem(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:ngn_products,id',
                'quantity' => 'required|integer|min:1',
            ]);

            // Get authenticated customer from customer_auth
            $customerAuth = Auth::guard('customer')->user();
            if (! $customerAuth) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.',
                ], 401);
            }

            // Start transaction
            DB::beginTransaction();

            // Get customer record using customer_auth's customer_id
            $customer = Customer::find($customerAuth->customer_id);
            if (! $customer) {
                throw new \Exception('Customer record not found');
            }

            // Get or create pending order
            $pendingOrder = EcOrder::where('customer_id', $customerAuth->id)
                ->where('order_status', 'pending')
                ->where('payment_status', 'pending')
                ->first();

            if (! $pendingOrder) {
                \Log::info('No pending order found, creating new one');

                // Get or create customer address
                $customerAddress = CustomerAddress::where('customer_id', $customer->id)->first();
                if (! $customerAddress) {
                    // Create default UK address if none exists
                    $customerAddress = CustomerAddress::create([
                        'customer_id' => $customer->id,
                        'last_name' => $customer->last_name ?? '-',
                        'first_name' => $customer->first_name ?? '-',
                        'company_name' => '-',
                        'street_address' => $customer->address ?? '-',
                        'street_address_plus' => '-',
                        'postcode' => $customer->postcode ?? '-',
                        'city' => $customer->city ?? '-',
                        'phone_number' => $customer->phone ?? '-',
                        'is_default' => true,
                        'type' => 'billing',
                        'country_id' => 3,
                    ]);

                    \Log::info('Created default customer address', [
                        'customer_id' => $customer->id,
                        'customer_auth_id' => $customerAuth->id,
                        'address_id' => $customerAddress->id,
                    ]);
                }
                // Locate an enabled payment method
                $paymentMethod = EcPaymentMethod::active()->first();

                // Create new pending order with default values
                $pendingOrder = EcOrder::create([
                    'customer_id' => $customerAuth->id,
                    'order_status' => 'pending',
                    'shipping_method_id' => 1,
                    'payment_method_id' => $paymentMethod ? $paymentMethod->id : 3,
                    'customer_address_id' => $customerAddress->id,
                    'payment_status' => 'pending',
                    'currency' => 'GBP',
                    'total_amount' => 0,
                    'grand_total' => 0,
                    'shipping_cost' => 0,
                ]);
            }

            // Check if product already exists in order
            $orderItem = EcOrderItem::where('order_id', $pendingOrder->id)
                ->where('product_id', $validated['product_id'])
                ->first();

            // Get product details from NgnProduct
            $product = NgnProduct::findOrFail($validated['product_id']);
            if (! $product) {
                throw new \Exception('Product not found');
            }

            // Check if product is enabled for ecommerce
            if (! $product->is_ecommerce) {
                throw new \Exception('Product is not available for online purchase');
            }

            $price = $product->pos_price ?? $product->normal_price;
            $tax = $product->pos_vat ?? 0;

            if ($orderItem) {
                // Update existing item
                $orderItem->quantity += $validated['quantity'];
                $orderItem->total_price = $price * $orderItem->quantity;
                $orderItem->tax = ($price * $orderItem->quantity) - ($price * $orderItem->quantity / 1.2);
                $orderItem->line_total = $orderItem->total_price;
                $orderItem->save();
            } else {
                // Create new order item
                EcOrderItem::create([
                    'order_id' => $pendingOrder->id,
                    'product_id' => $validated['product_id'],
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $validated['quantity'],
                    'unit_price' => $price,
                    'total_price' => $price * $validated['quantity'],
                    'tax' => ($price * $validated['quantity']) - ($price * $validated['quantity'] / 1.2),
                    'line_total' => ($price * $validated['quantity']),
                ]);
            }

            // Update order totals
            $orderItems = EcOrderItem::where('order_id', $pendingOrder->id)->get();
            $totalAmount = $orderItems->sum('total_price');
            $totalTax = $orderItems->sum('tax');
            $totalShippingCost = $pendingOrder->shipping_cost;
            $totalDiscount = $pendingOrder->discount;

            $pendingOrder->total_amount = $totalAmount;
            $pendingOrder->tax = $totalTax;
            $pendingOrder->grand_total = $totalAmount + $totalShippingCost - $totalDiscount;
            $pendingOrder->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item added to order successfully',
            ]);

        } catch (ValidationException $ve) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $ve->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding order item', [
                'customer_id' => optional(Auth::guard('customer')->user())->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to order',
            ], 500);
        }
    }

    /**
     * Update the quantity of a specific cart item
     * Route: PUT ('/api/v1/shop/cart-item/{product_id}')
     * Frontend: shopAPI.updateCartItemQuantity(product_id, quantity) -> /api/v1/shop/cart-item/{product_id}
     *
     * @param  int  $product_id
     * @return JsonResponse
     */
    public function updateCartItemQuantity(Request $request, $product_id)
    {
        \Log::info('updateCartItemQuantity', ['request' => $request->all(), 'product_id' => $product_id]);
        try {
            // Validate request
            $validated = $request->validate([
                'quantity' => 'required|integer|min:0',
            ]);

            // Get authenticated customer
            $customer = Auth::guard('customer')->user();
            if (! $customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.',
                ], 401);
            }

            // Start transaction
            DB::beginTransaction();

            // Find the pending order for the customer
            $pendingOrder = EcOrder::where('customer_id', $customer->id)
                ->where('order_status', 'pending')
                ->where('payment_status', 'pending')
                ->first();

            if (! $pendingOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending order found',
                ], 404);
            }

            // Find the order item with the given product_id in the pending order
            $orderItem = EcOrderItem::where('order_id', $pendingOrder->id)
                ->where('product_id', $product_id)
                ->first();

            if (! $orderItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found in pending order',
                ], 404);
            }

            if ($validated['quantity'] === 0) {
                $orderItem->delete();
            } else {
                $orderItem->quantity = $validated['quantity'];
                $orderItem->total_price = $orderItem->unit_price * $validated['quantity'];
                $orderItem->tax = ($orderItem->total_price - ($orderItem->total_price / 1.2));
                $orderItem->line_total = $orderItem->total_price;
                $orderItem->save();
            }

            \Log::info('SHIPPING COST: '.$pendingOrder->shipping_cost);

            // Recalculate order totals
            $orderItems = $pendingOrder->orderItems;
            $totalAmount = $orderItems->sum('total_price');
            $totalTax = $orderItems->sum('tax');
            $totalDiscount = $pendingOrder->discount;
            $totalShippingCost = $pendingOrder->shipping_cost;

            $pendingOrder->total_amount = $totalAmount;
            $pendingOrder->tax = $totalTax;
            $pendingOrder->discount = $totalDiscount;
            $pendingOrder->shipping_cost = $totalShippingCost;
            $pendingOrder->grand_total = $totalAmount + $totalShippingCost - $totalDiscount;
            $pendingOrder->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $validated['quantity'] === 0 ? 'Item removed from order' : 'Quantity updated successfully',
            ]);

        } catch (ValidationException $ve) {
            DB::rollBack();
            \Log::error('updateCartItemQuantity', ['error' => $ve->getMessage(), 'trace' => $ve->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $ve->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating cart item quantity', [
                'customer_id' => optional(Auth::guard('customer')->user())->id,
                'product_id' => $product_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update quantity',
            ], 500);
        }
    }

    /**
     * Change the delivery method of the pending order
     * Route: PUT ('/api/v1/shop/cart-pending-order/delivery-method')
     * Frontend: shopAPI.changeDeliveryMethod(deliveryMethodId, branchId) -> /api/v1/shop/cart-pending-order/delivery-method
     *
     * @return JsonResponse
     */
    public function changeDeliveryMethod(Request $request)
    {
        \Log::info('changeDeliveryMethod', ['request' => $request->all()]);

        try {
            // Validate request
            $validated = $request->validate([
                'delivery_method_id' => 'required|integer|exists:ec_shipping_methods,id',
                'branch_id' => 'nullable|integer|exists:branches,id',
            ]);

            // Get authenticated customer
            $customer = Auth::guard('customer')->user();
            if (! $customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.',
                ], 401);
            }

            // Start transaction
            DB::beginTransaction();

            // Find the pending order for the customer
            $pendingOrder = EcOrder::where('customer_id', $customer->id)
                ->where('order_status', 'pending')
                ->where('payment_status', 'pending')
                ->first();

            if (! $pendingOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending order found',
                ], 404);
            }

            // Get the selected shipping method to check if it requires a branch
            $shippingMethod = EcShippingMethod::findOrFail($validated['delivery_method_id']);

            // Update the order's shipping method and branch ID
            $pendingOrder->shipping_method_id = $validated['delivery_method_id'];

            // Handle branch_id based on shipping method type
            if ($shippingMethod->in_store_pickup) {
                // For in-store pickup, branch_id is required
                if (! $validated['branch_id']) {
                    throw new \Exception('Branch ID is required for in-store pickup');
                }
                $pendingOrder->branch_id = $validated['branch_id'];
            } else {
                // For delivery, remove branch_id
                $pendingOrder->branch_id = null;
            }

            // Save the changes
            $pendingOrder->save();

            \Log::info('Updated order shipping details', [
                'order_id' => $pendingOrder->id,
                'shipping_method_id' => $pendingOrder->shipping_method_id,
                'branch_id' => $pendingOrder->branch_id,
            ]);

            // Update shipping record if exists
            $shipping = EcOrderShipping::where('order_id', $pendingOrder->id)->first();
            if ($shipping) {
                $shipping->fulfillment_method = $shippingMethod->in_store_pickup
                    ? EcOrderShipping::FULFILLMENT_PICKUP
                    : EcOrderShipping::FULFILLMENT_CARRIER;

                if ($shippingMethod->in_store_pickup) {
                    $branch = Branch::find($validated['branch_id']);
                    $shipping->notes = 'Store Pickup at Branch: '.($branch ? $branch->name : 'Unknown');
                } else {
                    $shipping->notes = null;
                }

                $shipping->save();

                \Log::info('Updated order shipping record', [
                    'shipping_id' => $shipping->id,
                    'fulfillment_method' => $shipping->fulfillment_method,
                    'notes' => $shipping->notes,
                ]);
            }

            DB::commit();

            // Store shipping details in session
            $shippingData = [
                'method_id' => $validated['delivery_method_id'],
                'is_store_pickup' => $shippingMethod->in_store_pickup,
                'branch_id' => $shippingMethod->in_store_pickup ? $validated['branch_id'] : null,
                'branch_details' => null,
            ];

            if ($shippingMethod->in_store_pickup && $validated['branch_id']) {
                $branch = Branch::find($validated['branch_id']);
                if ($branch) {
                    $shippingData['branch_details'] = [
                        'id' => $branch->id,
                        'name' => $branch->name,
                        'address' => $branch->address,
                    ];
                }
            }

            session(['shipping_details' => $shippingData]);

            return response()->json([
                'success' => true,
                'message' => 'Delivery method updated successfully',
            ]);

        } catch (ValidationException $ve) {
            DB::rollBack();
            \Log::error('Validation error in changeDeliveryMethod', [
                'errors' => $ve->errors(),
                'trace' => $ve->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $ve->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error changing delivery method', [
                'customer_id' => optional(Auth::guard('customer')->user())->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Route: GET /api/v1/shop/customer-orders
     * Frontend: shopAPI.getCustomerOrders() -> /api/v1/shop/customer-orders
     *
     * @return JsonResponse
     */
    public function getCustomerOrders()
    {
        try {
            // Authenticate customer
            $customer = Auth::guard('customer')->user();
            if (! $customer) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Define valid order statuses
            $validStatuses = ['Confirmed', 'Cancelled', 'In Progress', 'Pending', 'Ready to collect', 'Delivered'];

            // Query orders with proper error handling
            $orders = EcOrder::with(['customer', 'shippingMethod', 'paymentMethod', 'branch'])
                ->where('customer_id', $customer->id)
                ->whereIn('order_status', $validStatuses)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($orders);

        } catch (\Exception $e) {
            \Log::error('Error fetching customer orders: '.$e->getMessage());

            return response()->json([
                'error' => 'An error occurred while fetching orders',
                'message' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function getOrderById($id)
    {
        try {
            $order = EcOrder::with('items')->findOrFail($id);

            return response()->json($order);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Order not found'], 404);
        } catch (\Exception $e) {
            \Log::error('Error fetching order by ID:', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Failed to fetch order details'], 500);
        }
    }

    public function updateOrder(Request $request, $id)
    {
        try {
            $order = EcOrder::findOrFail($id);
            $validated = $request->validate([
                'status' => 'required|string',
                // Add other fields as necessary
            ]);

            $order->update($validated);

            return response()->json(['success' => true, 'message' => 'Order updated successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Order not found'], 404);
        } catch (\Exception $e) {
            \Log::error('Error updating order:', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Failed to update order'], 500);
        }
    }

    public function cancelOrder($id)
    {
        try {
            $order = EcOrder::findOrFail($id);
            $orderDate = new \DateTime($order->created_at);
            $now = new \DateTime;
            $interval = $now->diff($orderDate);
            $hours = $interval->h + ($interval->days * 24);

            if ($hours <= 24 && $order->order_status === 'pending') {
                $order->update(['order_status' => 'cancelled']);

                return response()->json(['success' => true, 'message' => 'Order cancelled successfully']);
            }

            return response()->json(['success' => false, 'message' => 'Order cannot be cancelled'], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Order not found'], 404);
        } catch (\Exception $e) {
            \Log::error('Error cancelling order:', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Failed to cancel order'], 500);
        }
    }

    public function deleteOrder($id)
    {
        $order = EcOrder::findOrFail($id);
        $order->delete();

        return response()->json(null, 204);
    }

    // Order Shipping
    public function getOrderShipping($id)
    {
        $shipping = EcOrderShipping::where('order_id', $id)->first();

        return response()->json($shipping);
    }

    public function createOrderShipping(Request $request, $id)
    {
        // Validate and create order shipping logic
    }

    public function updateOrderShipping(Request $request, $id)
    {
        // Validate and update order shipping logic
    }

    public function getBranches()
    {
        $branches = Branch::all();

        return response()->json($branches);
    }

    // Create a new branch
    public function createBranch(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $branch = Branch::create($validated);

        return response()->json($branch, 201);
    }

    // Get a specific branch by name
    public function getBranchByName($name)
    {
        $branch = Branch::where('name', $name)->firstOrFail();

        return response()->json($branch);
    }

    // Update an existing branch
    public function updateBranch(Request $request, $name)
    {
        $branch = Branch::where('name', $name)->firstOrFail();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'latitude' => 'sometimes|required|numeric',
            'longitude' => 'sometimes|required|numeric',
        ]);

        $branch->update($validated);

        return response()->json($branch);
    }

    // Delete a branch
    public function deleteBranch($name)
    {
        $branch = Branch::where('name', $name)->firstOrFail();
        $branch->delete();

        return response()->json(null, 204);
    }

    // Payment Methods
    public function getPaymentMethods()
    {
        $paymentMethods = EcPaymentMethod::all();

        return response()->json($paymentMethods);
    }

    public function createPaymentMethod(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'slug' => 'nullable|string',
            'logo' => 'nullable|string',
            'link_url' => 'nullable|string',
            'instructions' => 'nullable|string',
            'is_enabled' => 'boolean',
        ]);
        $paymentMethod = EcPaymentMethod::create($validated);

        return response()->json($paymentMethod, 201);
    }

    public function updatePaymentMethod(Request $request, $id)
    {
        $paymentMethod = EcPaymentMethod::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string',
            'slug' => 'nullable|string',
            'logo' => 'nullable|string',
            'link_url' => 'nullable|string',
            'instructions' => 'nullable|string',
            'is_enabled' => 'boolean',
        ]);
        $paymentMethod->update($validated);

        return response()->json($paymentMethod);
    }

    public function deletePaymentMethod($id)
    {
        $paymentMethod = EcPaymentMethod::findOrFail($id);
        $paymentMethod->delete();

        return response()->json(null, 204);
    }

    // Shipping Methods
    public function getShippingMethods()
    {
        $shippingMethods = EcShippingMethod::all();

        return response()->json($shippingMethods);
    }

    public function createShippingMethod(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'slug' => 'nullable|string',
            'logo' => 'nullable|string',
            'link_url' => 'nullable|string',
            'description' => 'nullable|string',
            'shipping_amount' => 'required|numeric',
            'is_enabled' => 'boolean',
            'in_store_pickup' => 'boolean',
        ]);
        $shippingMethod = EcShippingMethod::create($validated);

        return response()->json($shippingMethod, 201);
    }

    public function updateShippingMethod(Request $request, $id)
    {
        $shippingMethod = EcShippingMethod::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string',
            'slug' => 'nullable|string',
            'logo' => 'nullable|string',
            'link_url' => 'nullable|string',
            'description' => 'nullable|string',
            'shipping_amount' => 'required|numeric',
            'is_enabled' => 'boolean',
            'in_store_pickup' => 'boolean',
        ]);
        $shippingMethod->update($validated);

        return response()->json($shippingMethod);
    }

    public function deleteShippingMethod($id)
    {
        $shippingMethod = EcShippingMethod::findOrFail($id);
        $shippingMethod->delete();

        return response()->json(null, 204);
    }

    // Customer Addresses
    public function getCustomerAddresses()
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $addresses = CustomerAddress::where('customer_id', $customerAuth->customer->id)->get();

        return response()->json($addresses);
    }

    public function createCustomerAddress(Request $request): JsonResponse
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'street_address' => 'required|string|max:255',
            'street_address_plus' => 'nullable|string|max:255',
            'postcode' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'is_default' => 'boolean',
            'type' => 'required|string|in:billing,shipping,office,other',
            'country_id' => 'required|exists:system_countries,id',
        ]);

        $validated['customer_id'] = $customerAuth->customer->id;

        if ($validated['is_default']) {
            CustomerAddress::where('customer_id', $customerAuth->customer->id)
                ->where('type', $validated['type'])
                ->update(['is_default' => false]);
        }

        $address = CustomerAddress::create($validated);

        return response()->json($address, 201);
    }

    public function updateCustomerAddress(Request $request, $id)
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $address = CustomerAddress::where('customer_id', $customerAuth->customer->id)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'street_address' => 'required|string|max:255',
            'street_address_plus' => 'nullable|string|max:255',
            'postcode' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'is_default' => 'boolean',
            'type' => 'required|string|in:billing,shipping,office,other',
            'country_id' => 'required|exists:system_countries,id',
        ]);

        if ($validated['is_default']) {
            CustomerAddress::where('customer_id', $customerAuth->customer->id)
                ->where('type', $validated['type'])
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json($address);
    }

    public function deleteCustomerAddress($id)
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $address = CustomerAddress::where('customer_id', $customerAuth->customer->id)
            ->where('id', $id)
            ->firstOrFail();

        $address->delete();

        return response()->json(null, 204);
    }

    public function getCountries()
    {
        $countries = SystemCountry::select([
            'id',
            'name',
            'name_official',
            'cca2',
            'cca3',
            'flag',
            'latitude',
            'longitude',
            'currencies',
        ])->get();

        return response()->json($countries);
    }

    // Get all terms
    public function getTerms()
    {
        $terms = TermsVersion::latest()->active()->get();

        return response()->json($terms);
    }

    // Create new terms
    public function createTerms(Request $request)
    {
        $validated = $request->validate([
            'version' => 'required|string',
            'content' => 'required|string',
            'published_at' => 'required|date',
            'is_active' => 'boolean',
        ]);

        $terms = TermsVersion::create($validated);

        return response()->json($terms, 201);
    }

    // Update existing terms
    public function updateTerms(Request $request, $id)
    {
        $terms = TermsVersion::findOrFail($id);

        $validated = $request->validate([
            'version' => 'required|string',
            'content' => 'required|string',
            'published_at' => 'required|date',
            'is_active' => 'boolean',
        ]);

        $terms->update($validated);

        return response()->json($terms);
    }

    // Delete terms
    public function deleteTerms($id)
    {
        $terms = TermsVersion::findOrFail($id);
        $terms->delete();

        return response()->json(null, 204);
    }
    // -----

    public function login(Request $request)
    {
        \Log::info('Login Attempt');

        // Validate the incoming request data
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        // Optional: Add 'remember me' functionality
        $remember = $request->filled('remember');

        // Attempt to authenticate the user
        if (Auth::attempt($credentials, $remember)) {
            // Regenerate the session to prevent session fixation
            $request->session()->regenerate();

            // Redirect to intended page or a default dashboard
            return 'SUCCESS';
        }

        // Authentication failed: Redirect back with input and error message
        return 'not'; // Optionally retain the email input
    }

    // -------------
    public function BlogIndex()
    {
        return BlogPost::with(['category', 'images'])
            ->where('slug', '!=', '')
            ->get();
    }

    public function BlogShow($slug)
    {
        return BlogPost::where('slug', $slug)
            ->where('slug', '!=', '')
            ->with(['category', 'images'])
            ->firstOrFail();
    }

    /**
     * Get order summary including shipping costs and totals
     * Route: GET /api/v1/shop/order-summary
     * Frontend: shopAPI.getOrderSummary() -> /api/v1/shop/order-summary
     *
     * @return JsonResponse
     */
    public function getOrderSummary()
    {
        try {
            // Get authenticated customer
            $customer = Auth::guard('customer')->user();
            if (! $customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.',
                ], 401);
            }

            // Get pending order with items, shipping method, and product details
            $pendingOrder = EcOrder::with(['orderItems.product', 'shippingMethod'])
                ->where('customer_id', $customer->id)
                ->where('order_status', 'pending')
                ->where('payment_status', 'pending')
                ->first();

            if (! $pendingOrder) {
                return response()->json([
                    'success' => true,
                    'subtotal' => 0,
                    'shipping_cost' => 0,
                    'total' => 0,
                    'items' => [],
                ]);
            }

            // Calculate totals
            $subtotal = $pendingOrder->orderItems->sum('total_price');
            $shippingCost = $pendingOrder->shipping_cost ?? 0;
            $tax = $pendingOrder->tax ?? 0;
            $total = $subtotal + $shippingCost;

            // Format items for response
            $items = $pendingOrder->orderItems->map(function ($item) {

                $product = NgnProduct::find($item->product_id);

                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'tax' => $item->tax,
                    'total_price' => $item->total_price,
                    'image_url' => $product ? $product->image_url : null,
                ];
            });

            return response()->json([
                'success' => true,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'total' => $total,
                'items' => $items,
                'shipping_method' => $pendingOrder->shippingMethod ? [
                    'id' => $pendingOrder->shippingMethod->id,
                    'name' => $pendingOrder->shippingMethod->name,
                    'amount' => $pendingOrder->shippingMethod->shipping_amount,
                ] : null,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching order summary', [
                'customer_id' => optional(Auth::guard('customer')->user())->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order summary',
            ], 500);
        }
    }

    public function checkPendingOrderStatus(Request $request)
    {
        try {
            $customer = auth('customer')->user();

            // Get the most recent order for this customer
            $order = EcOrder::where('customer_id', $customer->id)
                ->whereIn('order_status', ['pending', 'In Progress'])
                ->orderBy('updated_at', 'desc')
                ->first();

            if (! $order) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending order found',
                ], 200);
            }

            // Check if there's a PayPal payment record - remove NGN_ORDER_ prefix
            $paypalPayment = PaymentsPaypal::where('order_id', $order->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $isPending = $order->order_status === 'pending' && $order->payment_status === 'pending';
            $isCompleted = $order->order_status === 'In Progress' && $order->payment_status === 'paid';

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'order_status' => $order->order_status,
                    'payment_status' => $order->payment_status,
                    'total_amount' => $order->total_amount,
                    'tax' => $order->tax,
                    'payment_completed' => $paypalPayment ? ($paypalPayment->status === 'completed') : false,
                    'is_completed' => $isCompleted,
                    'is_pending' => $isPending,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking pending order status:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check order status',
            ], 500);
        }
    }
}
