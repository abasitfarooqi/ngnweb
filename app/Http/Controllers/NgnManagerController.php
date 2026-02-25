<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Motorbike;
use App\Models\MotorbikeAnnualCompliance;
use App\Models\MotorbikeRegistration;
use App\Models\MotorbikeRepair;
use App\Models\NgnCategory;
use App\Models\NgnProduct;
use App\Models\NgnStockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NgnManagerController extends Controller
{
    public function checkVehicle(Request $request)
    {
        Log::info('Check Vehicle Payload:', $request->all());
        try {
            // Validate the request
            $validated = $request->validate([
                'registration_number' => 'required|string|max:10',
            ]);

            // Call DVLA API
            $response = Http::withHeaders([
                'x-api-key' => env('DVLA_VEH_API'),
                'Content-Type' => 'application/json',
            ])->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
                'registrationNumber' => strtoupper($validated['registration_number']),
            ]);

            if (! $response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch vehicle details',
                    'error' => $response->json(),
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'data' => $response->json(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking vehicle',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getVehicleDetails(Request $request)
    {
        Log::info('Check Vehicle Payload:', $request->all());
        try {
            // Validate the request
            $validated = $request->validate([
                'registration_number' => 'required|string|max:10',
            ]);

            // Call DVLA API
            $response = Http::withHeaders([
                'x-api-key' => env('DVLA_VEH_API'),
                'Content-Type' => 'application/json',
            ])->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
                'registrationNumber' => strtoupper($validated['registration_number']),
            ]);

            if (! $response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch vehicle details',
                    'error' => $response->json(),
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'data' => $response->json(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking vehicle',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save vehicle details to database with additional processing
     *
     * @return JsonResponse
     */
    public function saveVehicleDetails(Request $request)
    {
        try {
            Log::info('Received vehicle details:', $request->all());

            // Call DVLA API to get vehicle details
            $dvlaResponse = $this->checkVehicle(new Request([
                'registration_number' => $request->reg_no,
            ]));

            $dvlaData = $dvlaResponse->getData();

            if (! $dvlaData->success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch DVLA details',
                    'error' => $dvlaData->message ?? 'DVLA API error',
                ], 422);
            }

            // Merge DVLA data with request
            $vehicleData = array_merge($request->all(), [
                'make' => $dvlaData->data->make ?? null,
                'year' => intval($dvlaData->data->yearOfManufacture ?? 0),
                'color' => $dvlaData->data->colour ?? null,
                'fuel_type' => $dvlaData->data->fuelType ?? null,
                'engine' => intval($dvlaData->data->engineCapacity ?? 0),
                'taxStatus' => $dvlaData->data->taxStatus ?? null,
                'taxDueDate' => $dvlaData->data->taxDueDate ?? null,
                'motStatus' => $dvlaData->data->motStatus ?? null,
                'motExpiryDate' => $dvlaData->data->motExpiryDate ?? null,
                'vehicle_profile_id' => $request->vehicle_profile_id ?? 1, // Default to 1 if not provided
            ]);

            // Update request with merged data
            $request->merge($vehicleData);

            // Handle empty VIN number
            if (empty($request->vin_number)) {
                $request->merge(['vin_number' => 'RND-'.Str::random(17)]);
            }

            // Process marked_for_export
            if (empty($request->marked_for_export) || ($request->marked_for_export == 'no')) {
                $request->merge(['marked_for_export' => '0']);
            } else {
                $request->merge(['marked_for_export' => '1']);
            }

            // Format month_of_first_registration
            if (! empty($request->month_of_first_registration) && strlen($request->month_of_first_registration) == 7) {
                $request->merge(['month_of_first_registration' => $request->month_of_first_registration.'-01']);
            }

            try {
                $validated = $request->validate([
                    'vin_number' => 'required',
                    'make' => 'required|string',
                    'model' => 'required|string',
                    'reg_no' => 'required|string|max:10',
                    'taxStatus' => 'nullable|string',
                    'taxDueDate' => 'nullable|date',
                    'motStatus' => 'nullable|string',
                    'year' => 'required|integer',
                    'engine' => 'nullable|integer',
                    'co2_emissions' => 'nullable|integer',
                    'fuel_type' => 'nullable|string',
                    'marked_for_export' => 'nullable|boolean',
                    'color' => 'nullable|string',
                    'type_approval' => 'nullable|string',
                    'date_of_last_v5c_issuance' => 'nullable|date',
                    'motExpiryDate' => 'nullable|date',
                    'wheel_plan' => 'nullable|string',
                    'month_of_first_registration' => 'nullable|string',
                    'vehicle_profile_id' => 'required|integer|in:1,2',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Validation failed:', [
                    'errors' => $e->errors(),
                    'request_data' => $request->all(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            // Check if motorbike already exists
            $motorbike = Motorbike::where('reg_no', $validated['reg_no'])->first();

            if ($motorbike) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle with registration number '.$validated['reg_no'].' already exists',
                    'error' => 'DUPLICATE_REGISTRATION',
                ], 409); // 409 Conflict status code
            }

            if (! $motorbike) {
                // Create new motorbike
                $motorbike = Motorbike::create($validated);

                // Create registration record
                MotorbikeRegistration::create([
                    'motorbike_id' => $motorbike->id,
                    'registration_number' => $validated['reg_no'],
                    'start_date' => now(),
                    'end_date' => now(),
                ]);

                // Create annual compliance record
                MotorbikeAnnualCompliance::create([
                    'motorbike_id' => $motorbike->id,
                    'year' => date('Y'),
                    'mot_status' => $request->motStatus,
                    'road_tax_status' => $request->taxStatus,
                    'insurance_status' => 'N/A',
                    'tax_due_date' => $request->taxDueDate,
                    'insurance_due_date' => now(),
                    'mot_due_date' => $request->motExpiryDate,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Vehicle details saved successfully',
                'id' => $motorbike->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving vehicle details:', [
                'message' => $e->getMessage(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error saving vehicle details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllProducts(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $page = max($request->query('page', 1), 1); // Ensure page is at least 1
        $search = $request->query('search', '');

        // Convert "all" to a very high limit, or skip pagination altogether
        if ($perPage === 'all') {
            $limit = null; // No limit applied
            $offset = 0; // No offset needed for fetching all records
        } else {
            $limit = (int) $perPage; // Ensure perPage is an integer
            $offset = ($page - 1) * $limit;
        }

        // Build the base query
        $query = DB::table('ngn_products as np')
            ->select(
                'np.id',
                'np.name as PROD_NAME',
                'np.sku as SKU',
                'np.ean as EAN',
                'np.colour as COLOR',
                'np.pos_price as POS_PRICE',
                DB::raw('COALESCE(
                    (SELECT SUM(`in`) - SUM(`out`) FROM ngn_stock_movements
                     WHERE product_id = np.id AND branch_id = 1
                    ), 0
                ) AS CATFORD_STOCK'),
                DB::raw('COALESCE(
                    (SELECT SUM(`in`) - SUM(`out`) FROM ngn_stock_movements
                     WHERE product_id = np.id AND branch_id = 2
                    ), 0
                ) AS TOOTING_STOCK'),
                DB::raw('COALESCE(
                    (SELECT SUM(`in`) - SUM(`out`) FROM ngn_stock_movements
                     WHERE product_id = np.id AND branch_id = 3
                    ), 0
                ) AS SUTTON_STOCK'),
                'np.global_stock as GLOBAL_STOCK',
                'nb.name as BRAND',
                'nc.name as CAT',
                'nm.name as MODEL'
            )
            ->join('ngn_brands as nb', 'nb.id', '=', 'np.brand_id')
            ->join('ngn_categories as nc', 'nc.id', '=', 'np.category_id')
            ->join('ngn_models as nm', 'nm.id', '=', 'np.model_id');

        // Apply search filter if provided
        if (! empty($search)) {
            $searchTerm = '%'.$search.'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('np.name', 'like', $searchTerm)
                    ->orWhere('np.sku', 'like', $searchTerm)
                    ->orWhere('nb.name', 'like', $searchTerm)
                    ->orWhere('nc.name', 'like', $searchTerm)
                    ->orWhere('nm.name', 'like', $searchTerm);
            });
        }

        // Get total count for pagination
        $total = $query->count();

        // Get paginated results if limit is set
        $products = $limit ? $query->limit($limit)->offset($offset)->get() : $query->get();

        $totalPages = $limit ? ceil($total / $limit) : 1;

        return response()->json([
            'data' => $products,
            'pagination' => [
                'total' => $total,
                'per_page' => $limit ?: $total, // Show total count if fetching all records
                'current_page' => (int) $page,
                'total_pages' => $totalPages,
            ],
        ]);
    }

    /**
     * Handle adding stock.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addStock(Request $request)
    {
        // Log the incoming payload
        Log::info('Add Stock Payload:', $request->all());

        // Validate the request
        $request->validate([
            'product_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'quantity' => 'required|integer',
            'user_id' => 'required|integer', // Ensure user_id is provided
        ]);

        // Create a new stock movement record
        NgnStockMovement::create([
            'branch_id' => $request->input('branch_id'),
            'transaction_date' => now(),
            'product_id' => $request->input('product_id'),
            'in' => $request->input('quantity'),
            'out' => 0,
            'transaction_type' => 'stock_adjustment_inward',
            'user_id' => $request->input('user_id'),
            'ref_doc_no' => 'stock_adjustment_inward',
            'remarks' => 'Stock activities using tablets',
        ]);

        $this->updateGlobalStock($request->input('product_id'));

        // Return a success response
        return response()->json([
            'message' => 'Stock added successfully.',
            'data' => $request->all(),
        ]);
    }

    /**
     * Handle adjusting stock.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function adjustStock(Request $request)
    {
        // Log the incoming payload
        Log::info('Adjust Stock Payload:', $request->all());

        // Validate the request
        $request->validate([
            'product_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'quantity' => 'required|integer',
            'user_id' => 'required|integer', // Ensure user_id is provided
        ]);

        // Create a new stock movement record
        NgnStockMovement::create([
            'branch_id' => $request->input('branch_id'),
            'transaction_date' => now(),
            'product_id' => $request->input('product_id'),
            'in' => 0,
            'out' => $request->input('quantity'),
            'transaction_type' => 'stock_adjustment_outward',
            'user_id' => $request->input('user_id'),
            'ref_doc_no' => 'stock_adjustment_outward',
            'remarks' => 'Stock activities using tablets',
        ]);

        $this->updateGlobalStock($request->input('product_id'));

        // Return a success response
        return response()->json([
            'message' => 'Stock adjusted successfully.',
            'data' => $request->all(),
        ]);
    }

    public function harshDelete(Request $request)
    {
        Log::info('HARSH DELETE', $request->all());

        $del_log = NgnStockMovement::where('product_id', $request->product_id)->get();

        Log::info($del_log);

        Log::info(count($del_log));

        NgnStockMovement::where('product_id', $request->product_id)->delete();

        $del_item = NgnProduct::where('id', $request->product_id)->get();

        Log::info($del_item);

        NgnProduct::where('id', $request->product_id)->delete();

        return [
            'status' => 'success',
            'message' => 'deleted',
        ];

    }

    public function updateGlobalStock($productId)
    {
        // Calculate the global stock for the given product
        $globalStock = NgnStockMovement::where('product_id', $productId)
            ->sum('in') - NgnStockMovement::where('product_id', $productId)
            ->sum('out');

        // Update the global stock in the ngn_products table
        DB::table('ngn_products')
            ->where('id', $productId)
            ->update(['global_stock' => $globalStock]);

        // Log the update
        Log::info("Global stock updated for product ID: $productId, new global stock: $globalStock");
    }

    /**
     * * Retrieve all brands.
     * Connected End Points: GET /api/brands (auth:sanctum)
     * GET /shop/brands (auth:sanctum)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllBrands(Request $request)
    {
        Log::info('Fetching all brands');
        try {
            $brands = DB::table('ngn_brands')->select('id', 'name')->get();

            return response()->json([
                'data' => $brands,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching brands: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve brands.',
            ], 500);
        }
    }

    /**
     * Retrieve all categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCategories(Request $request)
    {
        Log::info('Fetching all categories');
        try {
            $categories = DB::table('ngn_categories')->select('id', 'name')->get();

            return response()->json([
                'data' => $categories,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching categories: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve categories.',
            ], 500);
        }
    }

    /**
     * Retrieve all models.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllModels(Request $request)
    {
        Log::info('Fetching all models');
        try {
            $models = DB::table('ngn_models')->select('id', 'name')->get();

            return response()->json([
                'data' => $models,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching models: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve models.',
            ], 500);
        }
    }

    /**
     * Retrieve all NgnProducts with pagination and search functionality.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllNgnProducts(Request $request)
    {
        Log::info('Fetching all NgnProducts', $request->all());
        try {
            // Validate query parameters
            $validator = Validator::make($request->all(), [
                'per_page' => 'sometimes|string|in:10,20,40,100,all', // Allow "all" as a valid option
                'page' => 'sometimes|integer|min:1',
                'search' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid query parameters.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            Log::info('Validated query parameters:', $request->all());

            $perPage = $request->query('per_page', 10);
            $page = $request->query('page', 1);
            $search = $request->query('search', '');

            $query = NgnProduct::with(['brand', 'category', 'productModel']);

            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('sku', 'like', '%'.$search.'%')
                        ->orWhereHas('brand', function ($q2) use ($search) {
                            $q2->where('name', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('category', function ($q2) use ($search) {
                            $q2->where('name', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('productModel', function ($q2) use ($search) {
                            $q2->where('name', 'like', '%'.$search.'%');
                        });
                });
            }

            $total = $query->count();

            // Check if "all" is selected
            if ($perPage === 'all') {
                $products = $query->get();
                $totalPages = 1;
            } else {
                $products = $query->skip(($page - 1) * $perPage)
                    ->take($perPage)
                    ->get();
                $totalPages = ceil($total / $perPage);
            }

            return response()->json([
                'data' => $products,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage === 'all' ? $total : (int) $perPage,
                    'current_page' => (int) $page,
                    'total_pages' => $totalPages,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching NgnProducts: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve products.',
            ], 500);
        }
    }

    /**
     * Retrieve a specific NgnProduct by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNgnProductById($id)
    {
        try {
            $product = NgnProduct::with(['brand', 'category', 'productModel'])
                ->find($id);

            if (! $product) {
                return response()->json([
                    'message' => 'Product not found.',
                ], 404);
            }

            return response()->json([
                'data' => $product,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error fetching NgnProduct ID {$id}: ".$e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve the product.',
            ], 500);
        }
    }

    /**
     * Create a new NgnProduct.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createNgnProduct(Request $request)
    {
        Log::info('Creating NgnProduct', $request->all());

        $data = $request->all();

        $category = NgnCategory::find($data['category_id']);

        Log::info('Category: '.$category->name);

        // Add random digits to SKU if it's "TYRE" and category is TYRE
        if (
            isset($data['sku']) && strtoupper($data['sku']) === 'TYRE' &&
            $category && strtoupper($category->name) === 'TYRE'
        ) {
            Log::info('SKU is TYRE and category is TYRE, adding random digits');
            $randomDigits = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            $data['sku'] = $data['sku'].$randomDigits;
            Log::info('New SKU: '.$data['sku']);
        }

        try {
            // Validate request data
            $validator = Validator::make($data, [
                'sku' => 'required|string|max:100|unique:ngn_products,sku',
                'ean' => 'required|string|max:13|unique:ngn_products,ean',
                'image_url' => 'nullable|url|max:255',
                'name' => 'required|string|max:255',
                'variation' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'extended_description' => 'nullable|string',
                'colour' => 'nullable|string|max:50',
                'pos_variant_id' => 'nullable|integer',
                'pos_product_id' => 'nullable|integer',
                'brand_id' => 'required|integer|exists:ngn_brands,id',
                'category_id' => 'required|integer|exists:ngn_categories,id',
                'model_id' => 'required|integer|exists:ngn_models,id',
                'normal_price' => 'required|numeric|min:0',
                'pos_price' => 'required|numeric|min:0',
                'pos_vat' => 'required|numeric|min:0',
                'global_stock' => 'required|integer|min:0',
                'vatable' => 'required|boolean',
                'is_oxford' => 'required|boolean',
                'dead' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Create the product
            $product = NgnProduct::create([
                'sku' => $data['sku'],
                'ean' => $data['ean'],
                'image_url' => $data['image_url'],
                'name' => $data['name'],
                'variation' => $data['variation'],
                'description' => $data['description'],
                'extended_description' => $data['extended_description'],
                'colour' => $data['colour'],
                'pos_variant_id' => isset($data['pos_variant_id']) ? $data['pos_variant_id'] : null,
                'pos_product_id' => isset($data['pos_product_id']) ? $data['pos_product_id'] : null,
                'brand_id' => $data['brand_id'],
                'category_id' => $data['category_id'],
                'model_id' => $data['model_id'],
                'normal_price' => $data['normal_price'],
                'pos_price' => $data['pos_price'],
                'pos_vat' => $data['pos_vat'],
                'global_stock' => $data['global_stock'],
                'vatable' => $data['vatable'],
                'is_oxford' => $data['is_oxford'],
                'dead' => $data['dead'],
            ]);

            return response()->json([
                'message' => 'Product created successfully.',
                'data' => $product,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating NgnProduct: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to create the product.',
            ], 500);
        }
    }

    /**
     * Update an existing NgnProduct by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNgnProduct(Request $request, $id)
    {
        Log::info($id);
        Log::info('Request data:', $request->all());
        try {
            $product = NgnProduct::find($id);

            if (! $product) {
                return response()->json([
                    'message' => 'Product not found.',
                    'details' => "Unable to find a product with ID {$id}. Please verify the product ID and try again.",
                ], 404);
            }

            // Pre-process request data
            $data = $request->all();

            // Check if product category is TYRE
            $category = NgnCategory::find($data['category_id']);

            // Only append random digits to SKU if category is TYRE
            if (isset($data['sku']) && $category && $category->name === 'TYRE') {
                $existingSku = NgnProduct::where('sku', $data['sku'])
                    ->where('id', '!=', $id)
                    ->exists();

                if ($existingSku) {
                    $randomDigits = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
                    $data['sku'] = $data['sku'].$randomDigits;
                }
            }

            // Convert fields to appropriate types
            $data['image_url'] = filter_var($data['image_url'], FILTER_VALIDATE_URL) ? $data['image_url'] : null;
            $data['pos_variant_id'] = is_numeric($data['pos_variant_id']) ? (int) $data['pos_variant_id'] : null;
            $data['pos_product_id'] = is_numeric($data['pos_product_id']) ? (int) $data['pos_product_id'] : null;
            $data['global_stock'] = is_numeric($data['global_stock']) ? (int) $data['global_stock'] : null;

            // Validate request data
            $validator = Validator::make($data, [
                'sku' => 'sometimes|string|max:100|unique:ngn_products,sku,'.$id,
                'ean' => 'nullable|string|max:19|unique:ngn_products,ean,'.$id,
                'image_url' => 'nullable|url|max:255',
                'name' => 'sometimes|string|max:255',
                'variation' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'extended_description' => 'nullable|string',
                'colour' => 'nullable|string|max:50',
                'pos_variant_id' => 'nullable|integer',
                'pos_product_id' => 'nullable|integer',
                'brand_id' => 'sometimes|integer|exists:ngn_brands,id',
                'category_id' => 'sometimes|integer|exists:ngn_categories,id',
                'model_id' => 'sometimes|integer|exists:ngn_models,id',
                'normal_price' => 'sometimes|numeric|min:0',
                'pos_price' => 'sometimes|numeric|min:0',
                'pos_vat' => 'sometimes|numeric|min:0',
                'global_stock' => 'sometimes|integer|min:0',
                'vatable' => 'sometimes|boolean',
                'is_oxford' => 'sometimes|boolean',
                'dead' => 'sometimes|boolean',
            ], [
                // Custom error messages
                'sku.unique' => 'This SKU is already in use. Please provide a unique SKU.',
                'sku.max' => 'SKU cannot be longer than 100 characters.',
                'ean.unique' => 'This EAN is already in use. Please provide a unique EAN.',
                'ean.max' => 'EAN cannot be longer than 19 characters.',
                'image_url.url' => 'Please provide a valid URL for the image.',
                'name.max' => 'Product name cannot exceed 255 characters.',
                'variation.max' => 'Variation name cannot exceed 255 characters.',
                'colour.max' => 'Colour name cannot exceed 50 characters.',
                'brand_id.exists' => 'The selected brand does not exist in our records.',
                'category_id.exists' => 'The selected category does not exist in our records.',
                'model_id.exists' => 'The selected model does not exist in our records.',
                'normal_price.numeric' => 'Normal price must be a valid number.',
                'normal_price.min' => 'Normal price cannot be negative.',
                'pos_price.numeric' => 'POS price must be a valid number.',
                'pos_price.min' => 'POS price cannot be negative.',
                'pos_vat.numeric' => 'VAT must be a valid number.',
                'pos_vat.min' => 'VAT cannot be negative.',
                'global_stock.integer' => 'Global stock must be a whole number.',
                'global_stock.min' => 'Global stock cannot be negative.',
                'vatable.boolean' => 'Vatable field must be true or false.',
                'is_oxford.boolean' => 'Oxford field must be true or false.',
                'dead.boolean' => 'Dead field must be true or false.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Product update validation failed.',
                    'details' => 'Please review the following issues:',
                    'errors' => $validator->errors()->all(),
                    'error_fields' => $validator->errors()->keys(),
                    'submitted_data' => $data,
                ], 422);
            }

            // Update the product with validated data
            $product->update($validator->validated());

            return response()->json([
                'message' => 'Product updated successfully.',
                'data' => $product,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error updating NgnProduct ID {$id}: ".$e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to update the product.',
                'details' => 'An unexpected error occurred while updating the product. Please try again or contact support if the problem persists.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a NgnProduct by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteNgnProduct($id)
    {
        try {
            $product = NgnProduct::find($id);

            if (! $product) {
                return response()->json([
                    'message' => 'Product not found.',
                ], 404);
            }

            $product->delete();

            return response()->json([
                'message' => 'Product deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error deleting NgnProduct ID {$id}: ".$e->getMessage());

            return response()->json([
                'message' => 'Failed to delete the product.',
            ], 500);
        }
    }

    /**
     * Transfer products between branches.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function transferProduct(Request $request)
    {
        // Log the incoming payload
        Log::info('Transfer Product Payload:', $request->all());

        // Validate the request
        $validator = Validator::make($request->all(), [
            'from_branch_id' => 'required|integer|different:to_branch_id',
            'to_branch_id' => 'required|integer',
            'user_id' => 'required|integer|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:ngn_products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $refDocNo = now()->format('YmdHis').'tr'.$request->from_branch_id.$request->to_branch_id;

            foreach ($request->items as $item) {
                // Outward transaction
                NgnStockMovement::create([
                    'branch_id' => $request->from_branch_id,
                    'transaction_date' => now(),
                    'product_id' => $item['product_id'],
                    'in' => 0,
                    'out' => $item['quantity'],
                    'transaction_type' => 'transfer_outward',
                    'user_id' => $request->user_id,
                    'ref_doc_no' => $refDocNo,
                    'remarks' => 'Product transfer outward',
                ]);

                // Inward transaction
                NgnStockMovement::create([
                    'branch_id' => $request->to_branch_id,
                    'transaction_date' => now(),
                    'product_id' => $item['product_id'],
                    'in' => $item['quantity'],
                    'out' => 0,
                    'transaction_type' => 'transfer_inward',
                    'user_id' => $request->user_id,
                    'ref_doc_no' => $refDocNo,
                    'remarks' => 'Product transfer inward',
                ]);

                // Update global stock
                $this->updateGlobalStock($item['product_id']);
            }

            DB::commit();

            // Log the successful transaction
            Log::info("Product transfer completed successfully. Ref Doc No: $refDocNo");

            return response()->json([
                'message' => 'Product transfer completed successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during product transfer: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to transfer products.',
            ], 500);
        }
    }

    /**
     * Get all vehicles for a user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllVehicles(Request $request)
    {
        \Log::info('Get All Vehicles', $request->all());
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get search query parameter
        $search = $request->query('search', '');

        // Build query
        $query = DB::table('motorbikes');

        // Apply search filter if provided
        if (! empty($search)) {
            $searchTerm = '%'.$search.'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('make', 'like', $searchTerm)
                    ->orWhere('model', 'like', $searchTerm)
                    ->orWhere('vin_number', 'like', $searchTerm)
                    ->orWhere('reg_no', 'like', $searchTerm)
                    ->orWhere('color', 'like', $searchTerm)
                    ->orWhere('year', 'like', $searchTerm);
            });
        }

        $vehicles = $query->get();

        return response()->json($vehicles);
    }

    /**
     * Update a vehicle.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateVehicle(Request $request)
    {
        \Log::info('Update Vehicle', $request->all());

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'vehicle_id' => 'required||integer|exists:motorbikes,id',
            'vin_number' => 'required|string',
            'model' => 'required|string',
            'vehicle_profile_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // handle update if error return error message
        try {
            DB::table('motorbikes')->where('id', $request->vehicle_id)->update([
                'vin_number' => $request->vin_number,
                'model' => $request->model,
                'vehicle_profile_id' => $request->vehicle_profile_id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating vehicle: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to update vehicle.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Vehicle updated successfully.',
        ], 200);
    }

    /**
     * Get all branches.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllBranches(Request $request)
    {
        \Log::info('Get All Branches', $request->all());
        $branches = Branch::all();

        return response()->json($branches);
    }

    /**
     * Get all repair records with their associated updates and motorbike details.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRepairs(Request $request)
    {
        \Log::info('Getting all repairs', $request->all());
        try {
            // Validate user_id and optional branch_id
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'branch_id' => 'nullable|integer|exists:branches,id',
                'search' => 'nullable|string|max:255',
                'is_repaired' => 'nullable|in:true,false,1,0',
                'is_returned' => 'nullable|in:true,false,1,0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Get repairs with relationships
            $query = MotorbikeRepair::with([
                'motorbike',
                'updates',
                'branch',
            ]);

            // Scope repairs to the authenticated user
            // $query->where("user_id", $request->user_id);

            // Filter by branch_id if provided
            if ($request->has('branch_id') && ! is_null($request->branch_id)) {
                $query->where('branch_id', $request->branch_id);
            }

            // Apply search filter
            if ($request->has('search') && ! empty($request->search)) {
                $searchTerm = '%'.$request->search.'%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('fullname', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm)
                        ->orWhere('phone', 'like', $searchTerm)
                        ->orWhereHas('motorbike', function ($q2) use ($searchTerm) {
                            $q2->where('reg_no', 'like', $searchTerm);
                        });
                });
            }

            // Apply filters for is_repaired and is_returned
            if ($request->has('is_repaired')) {
                $isRepaired = filter_var($request->input('is_repaired'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($isRepaired !== null) {
                    $query->where('is_repaired', $isRepaired);
                }
            }
            if ($request->has('is_returned')) {
                $isReturned = filter_var($request->input('is_returned'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($isReturned !== null) {
                    $query->where('is_returned', $isReturned);
                }
            }

            $repairs = $query->get();

            return response()->json([
                'success' => true,
                'data' => $repairs,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching repairs: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch repairs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new repair record with associated updates.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createRepair(Request $request)
    {
        Log::info('Creating repair record', $request->all());
        try {
            // Validate request data
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'motorbike_id' => 'required|integer|exists:motorbikes,id',
                'arrival_date' => 'required|date',
                'notes' => 'required|string',
                'fullname' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:255',
                'branch_id' => 'required|integer|exists:branches,id',
                'updates' => 'required|array|min:1',
                'updates.*.job_description' => 'required|string',
                'updates.*.price' => 'required|numeric|min:0',
                'updates.*.note' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            // Create repair record
            $repair = MotorbikeRepair::create([
                'arrival_date' => $request->arrival_date,
                'motorbike_id' => $request->motorbike_id,
                'notes' => $request->notes,
                'fullname' => $request->fullname,
                'email' => $request->email,
                'phone' => $request->phone,
                'branch_id' => $request->branch_id,
                'user_id' => $request->user_id,
                'is_repaired' => false,
                'is_returned' => false,
            ]);

            // Create repair updates
            foreach ($request->updates as $update) {
                $repair->updates()->create([
                    'job_description' => $update['job_description'],
                    'price' => $update['price'],
                    'note' => $update['note'],
                ]);
            }

            DB::commit();

            // Return repair with updates
            return response()->json([
                'success' => true,
                'message' => 'Repair record created successfully',
                'data' => $repair->load('updates'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating repair record: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create repair record',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update repair record status and details
     *
     * @return JsonResponse
     */
    public function updateRepair(Request $request)
    {
        DB::beginTransaction();

        try {
            // Log incoming request
            Log::info('Updating repair record', $request->all());

            // Validate the request
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'repair_id' => 'required|integer|exists:motorbikes_repair,id',
                'reg_no' => 'required|string|exists:motorbikes,reg_no',
                'is_repaired' => 'nullable|boolean',
                'is_returned' => 'nullable|boolean',
                'notes' => 'nullable|string',
                'fullname' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'updates' => 'nullable|array',
                'updates.*.id' => 'nullable|integer|exists:motorbike_repair_updates,id',
                'updates.*.job_description' => 'required_with:updates|string',
                'updates.*.price' => 'required_with:updates|numeric|min:0',
                'updates.*.note' => 'nullable|string',
                'updates_to_delete' => 'nullable|array',
                'updates_to_delete.*' => 'integer|exists:motorbike_repair_updates,id',
            ]);

            if ($validator->fails()) {
                DB::rollBack();
                Log::error('Validation failed while updating repair record', [
                    'errors' => $validator->errors()->toArray(),
                    'request' => $request->all(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Find the motorbike using reg_no
            $motorbike = Motorbike::where('reg_no', $request->reg_no)->first();

            if (! $motorbike) {
                DB::rollBack();
                Log::error('Motorbike not found', [
                    'reg_no' => $request->reg_no,
                    'request' => $request->all(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => "Motorbike with registration number {$request->reg_no} not found.",
                ], 404);
            }

            // Find the repair record
            $repair = MotorbikeRepair::where('id', $request->repair_id)
                ->where('motorbike_id', $motorbike->id)
                ->first();

            if (! $repair) {
                DB::rollBack();
                Log::error('Repair record not found', [
                    'repair_id' => $request->repair_id,
                    'motorbike_id' => $motorbike->id,
                    'request' => $request->all(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Repair record not found for the specified motorbike.',
                ], 404);
            }

            try {
                // Update repair status fields
                $updateData = [];

                // Handle is_repaired status
                if ($request->has('is_repaired')) {
                    $updateData['is_repaired'] = $request->is_repaired;
                    if ($request->is_repaired && ! $repair->is_repaired) {
                        $updateData['repaired_date'] = now();
                    } elseif (! $request->is_repaired) {
                        $updateData['repaired_date'] = null;
                    }
                }

                // Handle is_returned status
                if ($request->has('is_returned')) {
                    $updateData['is_returned'] = $request->is_returned;
                    if ($request->is_returned && ! $repair->is_returned) {
                        $updateData['returned_date'] = now();
                    } elseif (! $request->is_returned) {
                        $updateData['returned_date'] = null;
                    }
                }

                // Handle other optional fields
                foreach (['notes', 'fullname', 'email', 'phone'] as $field) {
                    if ($request->filled($field)) {
                        $updateData[$field] = $request->$field;
                    }
                }

                // Update the repair record if there are changes
                if (! empty($updateData)) {
                    Log::info('Updating repair record with data', $updateData);
                    $repair->update($updateData);
                }

                // Handle repair updates
                if ($request->has('updates')) {
                    foreach ($request->updates as $update) {
                        if (isset($update['id'])) {
                            // Update existing repair update
                            $repairUpdate = $repair->updates()->find($update['id']);
                            if ($repairUpdate) {
                                $repairUpdate->update([
                                    'job_description' => $update['job_description'] ?? $repairUpdate->job_description,
                                    'price' => $update['price'] ?? $repairUpdate->price,
                                    'note' => $update['note'] ?? $repairUpdate->note,
                                ]);
                            }
                        } else {
                            // Create new repair update
                            $repair->updates()->create([
                                'job_description' => $update['job_description'],
                                'price' => $update['price'],
                                'note' => $update['note'] ?? null,
                            ]);
                        }
                    }
                }

                // Delete updates if specified
                if ($request->has('updates_to_delete')) {
                    $repair->updates()
                        ->whereIn('id', $request->updates_to_delete)
                        ->delete();
                }

                DB::commit();

                // Fetch fresh data with relationships
                $repair = $repair->fresh(['updates', 'motorbike']);

                Log::info('Successfully updated repair record', [
                    'repair_id' => $repair->id,
                    'updates' => $updateData,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Repair record updated successfully',
                    'data' => $repair,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error updating repair record details', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update repair record details',
                    'error' => $e->getMessage(),
                    'debug_info' => [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ],
                ], 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in repair record update process', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the repair record update',
                'error' => $e->getMessage(),
                'debug_info' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
            ], 500);
        }
    }
}
