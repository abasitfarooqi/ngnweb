<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MotorcycleDeliveryController;
use App\Http\Requests\MotorbikeDeliveryOrderEnquiriesRequest;
use App\Mail\DeliveryAgreementMail;
use App\Mail\MotorbikeTransportDeliveryOrderEnquiry;
use App\Models\Branch;
use App\Models\DeliveryAgreementAccess;
use App\Models\DeliveryVehicleType;
use App\Models\MotorbikeDeliveryOrderEnquiries;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

/**
 * Class MotorbikeDeliveryOrderEnquiriesCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MotorbikeDeliveryOrderEnquiriesCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\MotorbikeDeliveryOrderEnquiries::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/motorbike-delivery-order-enquiries');
        CRUD::setEntityNameStrings('motorbike delivery order enquiries', 'motorbike delivery order enquiries');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     *
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setFromDb(); // set columns from db columns.
        CRUD::column('order_id');
        CRUD::column('full_name');
        CRUD::column('branch_name');
        CRUD::column('is_dealt');
        CRUD::column('branch_id');
        CRUD::column('dealt_by_user_id');
        CRUD::column('notes');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(MotorbikeDeliveryOrderEnquiriesRequest::class);

        // Pickup postcode (Required)
        CRUD::field('pickup_postcode')->wrapper(['class' => 'form-group col-md-3'])->attributes(['required' => 'required', 'placeholder' => 'Enter pickup postcode (required)']);
        // Dropoff postcode (Required)
        CRUD::field('dropoff_postcode')->wrapper(['class' => 'form-group col-md-3'])->attributes(['required' => 'required', 'placeholder' => 'Enter dropoff postcode (required)']);
        // Pickup address
        CRUD::field('pickup_address')->wrapper(['class' => 'form-group col-md-3'])->attributes(['placeholder' => 'Enter pickup address']);
        // Dropoff address
        CRUD::field('dropoff_address')->wrapper(['class' => 'form-group col-md-3'])->attributes(['placeholder' => 'Enter dropoff address']);
        // Pick up date & time (Required)
        CRUD::field('pick_up_datetime')->wrapper(['class' => 'form-group col-md-2'])->attributes(['required' => 'required', 'placeholder' => 'Select pickup date and time (required)']);
        // Vrm (Required)
        CRUD::field('vrm')->wrapper(['class' => 'form-group col-md-2'])->attributes(['placeholder' => 'Enter vehicle registration']);
        CRUD::field('vehicle_type_id')->type('select')->attributes(['required' => 'required'])->entity('vehicleType')->attribute('name')->model(DeliveryVehicleType::class)->wrapper(['class' => 'form-group col-md-2']);
        // Moveable
        CRUD::field('moveable')->wrapper(['class' => 'form-group col-md-1 mt-4']);
        // Documents
        CRUD::field('documents')->wrapper(['class' => 'form-group col-md-1 mt-4']);
        // KEYS
        CRUD::field('keys')->wrapper(['class' => 'form-group col-md-1 mt-4']);

        // Notes
        CRUD::field('note')->wrapper(['class' => 'form-group col-md-3'])->attributes(['placeholder' => 'Enter any additional notes']);
        // Customer Name (Required)
        CRUD::field('full_name')->wrapper(['class' => 'form-group col-md-2'])->attributes(['placeholder' => 'Enter customer full name']);
        // Phone (Required)
        CRUD::field('phone')->wrapper(['class' => 'form-group col-md-2'])->attributes(['placeholder' => 'Enter customer phone number']);

        // The rest of the fields (keep as is, after the required ones)

        CRUD::field('email')->wrapper(['class' => 'form-group col-md-4'])->attributes(['placeholder' => 'Enter customer email address']);
        CRUD::field('branch_id')->type('select')->entity('branch')->attribute('name')->model(Branch::class)->wrapper(['class' => 'form-group col-md-4']);
        // Name of person who is ordering (Login ID) - dealt_by_user_id, show name

        // CRUD::field('total_cost')->type('number')->attributes(['step' => 'any'])->label('Total Cost')->wrapper(['class' => 'form-group col-md-3']);
        // CRUD::field('distance')->type('number')->attributes(['step' => 'any'])->label('Distance')->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('total_cost')->type('hidden')->attributes(['step' => 'any'])->label('Total Cost')->wrapper(['class' => 'form-group col-md-3']);
        CRUD::field('distance')->type('hidden')->attributes(['step' => 'any'])->label('Distance')->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('customer_address')->type('hidden')->wrapper(['class' => 'form-group col-md-3'])->attributes(['placeholder' => 'Enter customer address']);
        CRUD::field('customer_postcode')->type('hidden')->wrapper(['class' => 'form-group col-md-3'])->attributes(['placeholder' => 'Enter customer postcode']);

        // CRUD::field('is_dealt')->wrapper(['class' => 'form-group col-md-6  mt-4']);
        // CRUD::addField(['name' => 'dealt_by_user_id', 'type' => 'hidden', 'default' => backpack_user()->id])->attributes(['readonly' => 'readonly']);
        CRUD::field('notes')->type('hidden')->wrapper(['class' => 'form-group col-md-3'])->attributes(['placeholder' => 'Enter internal notes']);
        CRUD::field('order_id')->type('hidden')->wrapper(['class' => 'form-group col-md-3']);
        CRUD::field('user')->label('Ordered By')->type('hidden')->entity('user')->model(\App\Models\User::class)->attribute('dealtByUserName')->default(backpack_user()->id)->wrapper(['class' => 'form-group col-md-6'])->attributes(['readonly' => 'readonly']);

        // CRUD::field('recalculate')->type('checkbox')->label('Recalculate Distance and Cost')->wrapper(['class' => 'form-group col-md-6']);
        // $recalculateValue = $this->crud->getRequest()->input('recalculate');
        // Log::info('Recalculate value from request: '. $recalculateValue);
        // if ($recalculateValue) {
        //     $this->calculateDistanceAndCost();
        // }
        $this->calculateDistanceAndCost();

        CRUD::field('send_email')->type('checkbox')->label('Send Email')->wrapper(['class' => 'form-group col-md-2']);

        Log::info('I am create operation');
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        // $this->setupCreateOperation();
        CRUD::field('pickup_postcode')->wrapper(['class' => 'form-group col-md-3'])->attributes(['required' => 'required', 'placeholder' => 'Enter pickup postcode (required)']);
        CRUD::field('dropoff_postcode')->wrapper(['class' => 'form-group col-md-3'])->attributes(['required' => 'required', 'placeholder' => 'Enter dropoff postcode (required)']);

        CRUD::field('pickup_address')->wrapper(['class' => 'form-group col-md-3'])->attributes(['placeholder' => 'Enter pickup address']);
        CRUD::field('dropoff_address')->wrapper(['class' => 'form-group col-md-3'])->attributes(['placeholder' => 'Enter dropoff address']);
        CRUD::field('pick_up_datetime')->wrapper(['class' => 'form-group col-md-2'])->attributes(['required' => 'required', 'placeholder' => 'Select pickup date and time (required)']);
        CRUD::field('vrm')->wrapper(['class' => 'form-group col-md-2'])->attributes(['placeholder' => 'Enter vehicle registration']);
        CRUD::field('vehicle_type_id')->type('select')->entity('vehicleType')->attribute('name')->model(DeliveryVehicleType::class)->wrapper(['class' => 'form-group col-md-2']);

        CRUD::field('moveable')->wrapper(['class' => 'form-group col-md-1 mt-4']);
        CRUD::field('documents')->wrapper(['class' => 'form-group col-md-1 mt-4']);
        CRUD::field('keys')->wrapper(['class' => 'form-group col-md-1 mt-4']);

        CRUD::field('note')->wrapper(['class' => 'form-group col-md-3'])->attributes(['placeholder' => 'Enter any additional notes']);

        CRUD::field('full_name')->wrapper(['class' => 'form-group col-md-2'])->attributes(['placeholder' => 'Enter customer full name']);
        CRUD::field('phone')->wrapper(['class' => 'form-group col-md-2'])->attributes(['placeholder' => 'Enter customer phone number']);
        CRUD::field('branch_id')->type('select')->entity('branch')->attribute('name')->model(Branch::class)->wrapper(['class' => 'form-group col-md-2']);
        CRUD::field('email')->wrapper(['class' => 'form-group col-md-2'])->attributes(['placeholder' => 'Enter customer email address']);

        CRUD::field('customer_address')->wrapper(['class' => 'form-group col-md-2'])->attributes(['placeholder' => 'Enter customer address']);
        CRUD::field('customer_postcode')->wrapper(['class' => 'form-group col-md-2'])->attributes(['placeholder' => 'Enter customer postcode']);

        CRUD::addField(['name' => 'dealt_by_user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);
        CRUD::field('notes')->wrapper(['class' => 'form-group col-md-6'])->attributes(['placeholder' => 'Enter internal notes']);

        CRUD::field('order_id')->wrapper(['class' => 'form-group col-md-6']);

        // CRUD::field('recalculate')->type('checkbox')->label('Recalculate Distance and Cost')->wrapper(['class' => 'form-group col-md-6']);
        // $recalculateValue = $this->crud->getRequest()->input('recalculate');
        // Log::info('Recalculate value from request: '. $recalculateValue);
        // if ($recalculateValue) {
        //     $this->calculateDistanceAndCost();
        // }
        $this->calculateDistanceAndCost();

        CRUD::field('distance')->type('number')->attributes(['step' => 'any'])->label('Distance')->wrapper(['class' => 'form-group col-md-6'])->attributes(['readonly' => 'readonly']);
        CRUD::field('total_cost')->type('number')->attributes(['step' => 'any'])->label('Total Cost')->wrapper(['class' => 'form-group col-md-6'])->attributes(['readonly' => 'readonly']);
        CRUD::field('is_dealt')->wrapper(['class' => 'form-group col-md-3']);
        // Widget::add()->type('script')->inline()->content('assets/js/admin/forms/update-recalculate-distance-cost.js');
        $currentEntry = $this->crud->getCurrentEntry();
        Log::info('I am update operation'.
        'Total Cost: '.$currentEntry->total_cost.', '.
        'Distance: '.$currentEntry->distance.', '.
        'Pickup Postcode: '.$currentEntry->pickup_postcode.', '.
        'Dropoff Postcode: '.$currentEntry->dropoff_postcode.', '.
        'Vehicle Type ID: '.$currentEntry->vehicle_type_id.', '.
        'Moveable: '.$currentEntry->moveable.', '.
        'Pick Up DateTime: '.$currentEntry->pick_up_datetime);

        CRUD::field('send_email')->type('checkbox')->label('Send Email')->wrapper(['class' => 'form-group col-md-2']);
    }

    public function generateDeliveryAgreementAccess($MotorbikeDeliveryOrderEnquiries)
    {
        // Find the enquiry by ID
        $enquiry = MotorbikeDeliveryOrderEnquiries::findOrFail($MotorbikeDeliveryOrderEnquiries->id);
        $customer = $enquiry->customer; // Assuming you have a relationship set up

        // Check if the customer exists
        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        // Generate a passcode and set the expiry date
        $passcode = Str::random(12);
        $expiresAt = now()->addDays(1);

        // Create a new DeliveryAgreementAccess entry
        $access = DeliveryAgreementAccess::create([
            'customer_id' => $customer->id,
            'enquiry_id' => $enquiry->id,
            'passcode' => $passcode,
            'expires_at' => $expiresAt,
        ]);

        // Generate the URL for the contract access
        $url = route('deliveryagreement.access.show', ['id' => $access->id, 'passcode' => $passcode]);

        // Prepare the email data
        $data = [
            'email' => [$customer->email, 'customerservice@neguinhomotors.co.uk'],
            'title' => 'Delivery Agreement Access',
            'body' => 'Dear valued customer,

            We kindly request your attention to finalize your delivery agreement with Neguinho Motors. To proceed, please click the following link to review and sign the agreement: '.$url.'

            Thank you for choosing Neguinho Motors for your motorcycle delivery needs.',
        ];

        // Send the email
        $mailData = [
            'email' => $data['email'],
            'title' => $data['title'],
            'body' => $data['body'],
            'url' => $url,
        ];
        try {
            Mail::to($data['email'])->send(new DeliveryAgreementMail($mailData));
        } catch (\Exception $e) {
            \Log::error('Failed to send email: '.$e->getMessage());

            return response()->json(['message' => 'Failed to send email'], 500);
        }

        return response()->json(['message' => 'Delivery agreement access link generated and sent to customer.']);
    }

    /**
     * Calculate distance and cost
     *
     * @return void
     */
    protected function calculateDistanceAndCost()
    {
        Log::info('I am hit');

        $motorcycleDeliveryController = new MotorcycleDeliveryController;
        $currentEntry = $this->crud->getCurrentEntry();

        if ($currentEntry) {

            $from_coords = Cache::remember("coordinates_{$currentEntry->pickup_postcode}", 86400, function () use ($currentEntry, $motorcycleDeliveryController) {
                return $motorcycleDeliveryController->getCoordinates($currentEntry->pickup_postcode);
            });

            $to_coords = Cache::remember("coordinates_{$currentEntry->dropoff_postcode}", 86400, function () use ($currentEntry, $motorcycleDeliveryController) {
                return $motorcycleDeliveryController->getCoordinates($currentEntry->dropoff_postcode);
            });

            $distance = Cache::remember("distance_{$currentEntry->pickup_postcode}_{$currentEntry->dropoff_postcode}", 86400, function () use ($from_coords, $to_coords, $motorcycleDeliveryController) {
                return $motorcycleDeliveryController->calculateDistance($from_coords, $to_coords);
            });

            // Extract the distance value from the distance array
            $distanceValue = isset($distance['distance_units']) && $distance['distance_units'] === 'meters' ? round($distance['distance'] / 1609.34, 2) : $distance['distance'];

            $totalCost = $motorcycleDeliveryController->calculateTotalCost($distanceValue, $currentEntry->vehicle_type_id, $currentEntry->moveable, $currentEntry->pick_up_datetime);
            $currentEntry->distance = $distanceValue;
            $currentEntry->total_cost = $totalCost;

            Log::info('Before saving: Total Cost: '.$currentEntry->total_cost.', Distance: '.$currentEntry->distance);

            $currentEntry->save();

            Log::info('After saving: Total Cost: '.$currentEntry->total_cost.', Distance: '.$currentEntry->distance);

            Log::info('From Coordinates:', [$from_coords]);
            Log::info('To Coordinates:', [$to_coords]);
            Log::info('Distance:', [$distance]);
            Log::info('Distance Value:', [$distanceValue]);
            Log::info('Total Cost:', [$totalCost]);

            // Return JSON response
            return response()->json([
                'total_cost' => $totalCost,
                'distance' => $distanceValue,
            ]);
        }

        // Return an error response if currentEntry is not found
        return response()->json(['error' => 'Entry not found'], 404);
    }

    public function store()
    {
        $request = $this->crud->getRequest();
        $response = $this->traitStore();
        $entry = $this->crud->entry ?? null;
        if ($entry) {
            // Always calculate and save distance and cost after save
            $this->calculateDistanceAndCostForEntry($entry);
            // Reload the entry to get updated values
            $entry->refresh();
            if ($request->input('send_email')) {
                $this->sendEmail($entry->id);
            }
        }

        return $response;
    }

    public function update()
    {
        $request = $this->crud->getRequest();
        $response = $this->traitUpdate();
        $entry = $this->crud->entry ?? null;
        if ($entry) {
            // Always calculate and save distance and cost after update
            $this->calculateDistanceAndCostForEntry($entry);
            $entry->refresh();
            if ($request->input('send_email')) {
                $this->sendEmail($entry->id);
            }
        }

        return $response;
    }

    protected function calculateDistanceAndCostForEntry($entry)
    {
        $motorcycleDeliveryController = new MotorcycleDeliveryController;
        $from_coords = Cache::remember("coordinates_{$entry->pickup_postcode}", 86400, function () use ($entry, $motorcycleDeliveryController) {
            return $motorcycleDeliveryController->getCoordinates($entry->pickup_postcode);
        });
        $to_coords = Cache::remember("coordinates_{$entry->dropoff_postcode}", 86400, function () use ($entry, $motorcycleDeliveryController) {
            return $motorcycleDeliveryController->getCoordinates($entry->dropoff_postcode);
        });
        $distance = Cache::remember("distance_{$entry->pickup_postcode}_{$entry->dropoff_postcode}", 86400, function () use ($from_coords, $to_coords, $motorcycleDeliveryController) {
            return $motorcycleDeliveryController->calculateDistance($from_coords, $to_coords);
        });
        $distanceValue = isset($distance['distance_units']) && $distance['distance_units'] === 'meters' ? round($distance['distance'] / 1609.34, 2) : $distance['distance'];
        $totalCost = $motorcycleDeliveryController->calculateTotalCost($distanceValue, $entry->vehicle_type_id, $entry->moveable, $entry->pick_up_datetime);
        $entry->distance = $distanceValue;
        $entry->total_cost = $totalCost;
        $entry->save();
    }

    protected function sendEmail($id)
    {
        $enquiry = MotorbikeDeliveryOrderEnquiries::findOrFail($id);
        $data = [
            'order_id' => $enquiry->order_id,
            'full_name' => $enquiry->full_name,
            'email' => $enquiry->email,
            'phone' => $enquiry->phone,
            'customer_address' => $enquiry->customer_address,
            'customer_postcode' => $enquiry->customer_postcode,
            'vrm' => $enquiry->vrm,
            'vehicle_type' => optional($enquiry->vehicleType)->name,
            'moveable' => $enquiry->moveable,
            'documents' => $enquiry->documents,
            'keys' => $enquiry->keys,
            'note' => $enquiry->note,
            'pick_up_datetime' => $enquiry->pick_up_datetime,
            'pickup_address' => $enquiry->pickup_address,
            'dropoff_address' => $enquiry->dropoff_address,
            'distance' => $enquiry->distance,
            'total_cost' => $enquiry->total_cost,
        ];

        $recipients = [$data['email'], 'customerservice@neguinhomotors.co.uk'];

        try {
            Mail::to($recipients)->send(new MotorbikeTransportDeliveryOrderEnquiry($data));
            Log::info('Email sent to: '.json_encode($recipients));
        } catch (\Exception $e) {
            Log::error('Error sending email: '.$e->getMessage());
        }
    }
}
