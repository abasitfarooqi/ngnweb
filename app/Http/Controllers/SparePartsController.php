<?php

namespace App\Http\Controllers;

use App\Mail\QuoteRequest;
use App\Models\BikeModel;
use App\Models\Make;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mail;
use PDF;

class SparePartsController extends Controller
{
    public function spareparts_dashboard()
    {
        return view('olders.admin.spareparts.index');
    }

    public function viewPurchaseRequests()
    {
        \Log::info('Fetching unposted purchase requests');

        $purchaseRequestItems = PurchaseRequestItem::whereHas('purchaseRequest', function ($query) {
            $query->where('is_posted', false);
        })
            ->with(['purchaseRequest', 'brandName', 'bikeModel', 'creator'])
            ->get();

        $formattedData = $purchaseRequestItems->map(function ($item) {
            return [
                'pr_number' => $item->purchaseRequest->id ?? 'N/A',
                'brand_name' => $item->brandName->name ?? 'N/A',
                'bike_model' => $item->bikeModel->model ?? 'N/A',
                'color' => $item->color,
                'year' => $item->year,
                'chassis_no' => $item->chassis_no,
                'reg_no' => $item->reg_no,
                'part_number' => $item->part_number,
                'part_position' => $item->part_position,
                'quantity' => $item->quantity,
                'link_one' => $item->link_one ?? 'N/A',
                'link_two' => $item->link_two ?? 'N/A',
                'image' => $item->image ? url('storage/'.$item->image) : url('images/default.png'),
                'employee_id' => $item->creator->employee_id ?? 'No Employee ID',
            ];
        });

        return response()->json($formattedData);
    }

    public function viewAllPurchaseRequests()
    {
        \Log::info('Fetching all purchase requests');

        $purchaseRequestItems = PurchaseRequestItem::whereHas('purchaseRequest', function ($query) {
            $query->where('is_posted', true);
        })
            ->with(['purchaseRequest', 'brandName', 'bikeModel', 'creator'])
            ->get()->sortByDesc(function ($item) {
                return $item->purchaseRequest->date;
            });

        $formattedData = $purchaseRequestItems->map(function ($item) {
            return [
                'pr_number' => $item->purchaseRequest->id ?? 'N/A',
                'pr_date' => $item->purchaseRequest->date ?? 'N/A',
                'is_posted' => $item->purchaseRequest?->is_posted ? 'Yes' : 'No',
                'brand_name' => $item->brandName->name ?? 'N/A',
                'bike_model' => $item->bikeModel->model ?? 'N/A',
                'color' => $item->color,
                'year' => $item->year,
                'chassis_no' => $item->chassis_no,
                'reg_no' => $item->reg_no,
                'part_number' => $item->part_number,
                'part_position' => $item->part_position,
                'quantity' => $item->quantity,
                'link_one' => $item->link_one ?? 'N/A',
                'link_two' => $item->link_two ?? 'N/A',
                'image' => $item->image ? url('storage/'.$item->image) : url('images/default.png'),
                'employee_id' => $item->creator->employee_id ?? 'No Employee ID',
            ];
        });

        return view('olders.admin.spareparts.view-all-prs', compact('formattedData'));
    }

    // GENERATE PDF
    public function sendToSupplier(Request $request)
    {
        // Ensure the directory exists
        Storage::disk('public')->makeDirectory('quotes');

        // \Log::info('Received send to supplier request', $request->all());

        DB::beginTransaction();
        try {

            $purchaseRequest = PurchaseRequest::where('is_posted', 0)->firstOrFail();

            $items = PurchaseRequestItem::with(['brandName', 'bikeModel', 'creator'])
                ->where('pr_id', $purchaseRequest->id)->get();

            // $items = PurchaseRequestItem::where('pr_id', $purchaseRequest->id)->get();

            // \Log::info($purchaseRequest);
            // \Log::info($items);

            $pdf = PDF::loadView('pdf.quote_request', [
                'purchaseRequest' => $purchaseRequest,
                'items' => $items,
                'dateTime' => now()->format('Y-m-d H:i:s'),
            ])->setPaper('a4', 'portrait');

            $fileName = 'Quote-Request-'.$purchaseRequest->id.'.pdf';
            $pdf->save(storage_path("app/public/quotes/{$fileName}"));

            $data['email'] = ['admin@neguinhomotors.co.uk', 'spares@fowlers.co.uk'];
            // $data["email"] = ['admin@neguinhomotors.co.uk']; //, 'spares@fowlers.co.uk'];
            $data['title'] = 'Dear Spare Parts Team,'; // "Neguinho Motors | QUOTE REQUEST | " . $purchaseRequest->id;
            $data['body'] = 'Find attached our quote request. Please reply to the email below.';
            $data['pdf'] = $pdf;

            // \Log::info("message data: ", $data);
            \Log::info($fileName);

            // STOP EMAIL
            // Mail::to($data["email"])->send(new QuoteRequest($data));

            // wait to make it posted
            $purchaseRequest->is_posted = 1;
            $purchaseRequest->save();
            DB::commit();

            return response()->download(storage_path("app/public/quotes/{$fileName}"));
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function add_pr_item(Request $request)
    {
        \Log::info('Received item addition request for PR.', $request->all());

        if ($request->hasFile('image')) {
            \Log::info('Image file uploaded', ['name' => $request->file('image')->getClientOriginalName()]);
        } else {
            \Log::info('No image file uploaded');
        }

        // Validate input data
        $validated = $request->validate([
            'pr_id' => 'required|exists:purchase_requests,id',
            'brand_name_id' => 'required|exists:makes,id',
            'bike_model_id' => 'required|exists:bike_models,id',
            'color' => 'required',
            'year' => 'required|numeric',
            'chassis_no' => 'required',
            'reg_no' => 'required',
            'part_number' => 'required',
            'part_position' => 'required',
            'quantity' => 'required|numeric',
            'link_one' => 'nullable',
            'link_two' => 'nullable',
            'image' => 'nullable|file|image|max:64048',
        ]);

        $validated['created_by'] = auth()->id();

        \Log::info('Validated data:', $validated);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Store the image file
            $file_path = $request->file('image')->store('images', 'public');
            $validated['image'] = 'public/'.$file_path;  // This will now store paths like 'public/images/vpHed3UL9VmoCQdPc7wYsfKb9GnSbxd3g1C8dAmQ.png'
            \Log::info('File uploaded.', ['path' => $file_path]);
        } else {
            if ($request->hasFile('image')) {
                $error = $request->file('image')->getError();
                \Log::error('Image upload error', ['error' => $error]);

                return response()->json(['success' => false, 'message' => 'File upload failed', 'error' => $error]);
            } else {
                $validated['image'] = null;
            }
        }

        $brandName = Make::find($validated['brand_name_id']);
        $bike_model = BikeModel::find($validated['bike_model_id']);

        \Log::info('BRAND NAME: '.$brandName->name);

        $purchaseRequestItem = PurchaseRequestItem::create($validated);

        $purchaseRequestItem->load('creator');

        $employeeId = optional($purchaseRequestItem->creator)->employee_id ?? 'No Employee ID';
        \Log::info('Employee ID:', ['employee_id' => $employeeId]);

        if ($purchaseRequestItem) {
            \Log::info('Purchase Request Item successfully added', ['item_id' => $purchaseRequestItem->id]);

            return response()->json([
                'success' => true,
                'message' => 'Purchase request item added successfully',
                'brand_name' => $brandName->name ?? 'N/A',
                'bike_model' => $bike_model->model ?? 'N/A',
                'item' => [
                    'employee_id' => $employeeId,
                    'pr_number' => $purchaseRequestItem->pr_id,
                    'brand_name' => $brandName->name ?? 'N/A',
                    'bike_model' => $bike_model->model ?? 'N/A',
                    'color' => $purchaseRequestItem->color,
                    'year' => $purchaseRequestItem->year,
                    'chassis_no' => $purchaseRequestItem->chassis_no,
                    'reg_no' => $purchaseRequestItem->reg_no,
                    'part_number' => $purchaseRequestItem->part_number,
                    'part_position' => $purchaseRequestItem->part_position,
                    'quantity' => $purchaseRequestItem->quantity,
                    'link_one' => $purchaseRequestItem->link_one ?? 'N/A',
                    'link_two' => $purchaseRequestItem->link_two ?? 'N/A',
                    'image' => $purchaseRequestItem->image ? url('storage/'.$purchaseRequestItem->image) : null,
                ],
            ]);
        } else {
            \Log::error('Failed to add Purchase Request Item');

            return response()->json(['success' => false, 'message' => 'Unable to add item']);
        }
    }

    // Purchase Request Create
    public function store_pr(Request $request)
    {
        \Log::info('Received Purchase Request creation request.');

        $pr = new PurchaseRequest([
            'date' => now(),
            'note' => '-',
            'created_by' => auth()->user()->id,
            'is_posted' => false,
        ]);

        $pr->save();

        return response()->json([
            'success' => true,
            'message' => 'Purchase Request created successfully.',
            'prno' => $pr->id,
        ]);
    }

    // public function upload(Request $request)
    // {

    //     \Log::info("Received Customer upload request for customer_id: ", $request->all());

    //     if ($request->isMethod('post'))
    //         return 'Upload image';

    //     $request->validate([
    //         'image' => 'file|mimes:jpg,jpeg,png,pdf|max:65536',
    //     ]);

    //     \Log::info("Received Customer upload request for customer_id : ", $request->all());

    //     $file = $request->file('image');
    //     $imageTypeCode = $request->input('imageTypeCode');
    //     $motorbikeID = $request->input('motorbikeID');
    //     $bookingID = $request->input('bookingID');
    //     $imageType = imageType::where('code', $imageTypeCode)->firstOrFail();

    //     $rrand = rand(10, 999);
    //     $date = now()->format('Y-m-d');
    //     $extension = $file->getClientOriginalExtension();
    //     $filename = "{$imageTypeCode}-{$rrand}-{$date}.{$extension}";

    //     $path = $file->storeAs("customers/{$customer_id}/images", $filename);

    //     $customerimage = new Customerimage([
    //         'customer_id' => $customer_id,
    //         'image_type_id' => $imageType->id,
    //         'file_name' => $filename,
    //         'file_path' => $path,
    //         'file_format' => $extension,
    //         'image_number' => '',
    //         'valid_until' => null,
    //         'is_verified' => true,
    //         'motorbike_id' => $motorbikeID,
    //         'booking_id' => $bookingID,
    //     ]);

    //     $customerimage->save();

    //     \Log::info("image stored at: {$path}");

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'image uploaded successfully.',
    //         'path' => $path,
    //     ]);
    // }

    public function create_pr()
    {
        $brands = Make::query()->orderBy('name')->get();

        return view('olders.admin.spareparts.create-pr', compact('brands'));
    }

    //        Route::get('/fetch-pr-items', [SparePartsController::class, 'fetch_pr_items'])->name('admin.spareparts.fetch-pr-items');
    public function fetch_pr_items()
    {
        $pr_items = PurchaseRequestItem::whereHas('purchaseRequest', function ($query) {
            $query->where('is_posted', false);
        })->get();

        return response()->json($pr_items);
    }

    // [SparePartsController::class, 'get_bike_models'])->name('admin.spareparts.get-bike-models');
    public function get_bike_models(Request $request, $brandId)
    {

        // $brandId = $request->brand_id;

        \Log::info('BrandID: '.$brandId);

        $bikeModels = BikeModel::where('brand_name_id', $brandId)->pluck('model', 'id');

        return response()->json($bikeModels);
    }
}
