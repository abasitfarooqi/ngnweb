<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\VehicleDeliveryOrderRequest;
use App\Mail\MotorbikeTransportDeliveryOrderCancelled;
use App\Mail\MotorbikeTransportDeliveryOrderConfirmed;
use App\Models\VehicleDeliveryOrder;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Mail;

class VehicleDeliveryOrderCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(VehicleDeliveryOrder::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/vehicle-delivery-order');
        CRUD::setEntityNameStrings('vehicle delivery order', 'vehicle delivery orders');
    }

    protected function setupListOperation()
    {
        CRUD::addColumn(['name' => 'quote_date', 'type' => 'date', 'label' => 'Quote Date']);
        CRUD::addColumn(['name' => 'pickup_date', 'type' => 'datetime', 'label' => 'Pickup Date']);
        CRUD::addColumn(['name' => 'vrm', 'type' => 'text', 'label' => 'Vehicle Registration Number']);
        CRUD::addColumn(['name' => 'full_name', 'type' => 'text', 'label' => 'Full Name']);
        CRUD::addColumn(['name' => 'phone_number', 'type' => 'text', 'label' => 'Phone Number']);
        CRUD::addColumn(['name' => 'email', 'type' => 'email', 'label' => 'Email']);
        CRUD::addColumn(['name' => 'total_distance', 'type' => 'number', 'label' => 'Total Distance']);
        CRUD::addColumn(['name' => 'surcharge', 'type' => 'number', 'label' => 'Surcharge']);
        CRUD::addColumn(['name' => 'delivery_vehicle_type_id', 'type' => 'select', 'entity' => 'deliveryVehicleType', 'attribute' => 'name', 'label' => 'Vehicle Type']);
        CRUD::addColumn(['name' => 'branch_id', 'type' => 'select', 'entity' => 'branch', 'attribute' => 'name', 'label' => 'Branch']);
        CRUD::addColumn(['name' => 'user_id', 'type' => 'select', 'entity' => 'user', 'attribute' => 'name', 'label' => 'User']);
        CRUD::addColumn(['name' => 'notes', 'type' => 'textarea', 'label' => 'Notes']);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(VehicleDeliveryOrderRequest::class);

        CRUD::addField(['name' => 'quote_date', 'type' => 'date', 'label' => 'Quote Date']);
        CRUD::addField(['name' => 'pickup_date', 'type' => 'datetime', 'label' => 'Pickup Date']);
        CRUD::addField(['name' => 'vrm', 'type' => 'text', 'label' => 'Vehicle Registration Number']);
        CRUD::addField(['name' => 'full_name', 'type' => 'text', 'label' => 'Full Name']);
        CRUD::addField(['name' => 'phone_number', 'type' => 'text', 'label' => 'Phone Number']);
        CRUD::addField(['name' => 'email', 'type' => 'email', 'label' => 'Email']);
        CRUD::addField(['name' => 'total_distance', 'type' => 'number', 'label' => 'Total Distance']);
        CRUD::addField(['name' => 'surcharge', 'type' => 'number', 'label' => 'Surcharge']);
        CRUD::addField(['name' => 'delivery_vehicle_type_id', 'type' => 'select', 'entity' => 'deliveryVehicleType', 'attribute' => 'name', 'label' => 'Vehicle Type']);
        CRUD::addField(['name' => 'branch_id', 'type' => 'select', 'entity' => 'branch', 'attribute' => 'name', 'label' => 'Branch']);
        CRUD::addField(['name' => 'user_id', 'type' => 'select', 'entity' => 'user', 'attribute' => 'name', 'label' => 'User']);
        CRUD::addField(['name' => 'notes', 'type' => 'textarea', 'label' => 'Notes']);
        CRUD::addField(['name' => 'send_email', 'type' => 'checkbox', 'label' => 'Send Email Notification']);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function store()
    {
        $response = $this->traitStore();
        $this->handleStatusChange($this->crud->entry);

        return $response;
    }

    public function update()
    {
        $response = $this->traitUpdate();
        $this->handleStatusChange($this->crud->entry);

        return $response;
    }

    private function handleStatusChange($order)
    {
        $sendEmail = request()->input('send_email', false);

        if ($sendEmail) {
            if ($order->status === 'confirmed') {
                Mail::to($order->email)->send(new MotorbikeTransportDeliveryOrderConfirmed($order));
            } elseif ($order->status === 'cancelled') {
                Mail::to($order->email)->send(new MotorbikeTransportDeliveryOrderCancelled($order));
            }
        }
    }
}
