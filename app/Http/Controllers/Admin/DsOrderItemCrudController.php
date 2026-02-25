<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DsOrderItemRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;

class DsOrderItemCrudController extends CrudController
{
    public function setup()
    {
        $this->crud->setModel(\App\Models\DsOrderItem::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/ds-order-item');
        $this->crud->setEntityNameStrings('ds order item', 'ds order items');
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn(['name' => 'ds_order_id', 'type' => 'number', 'label' => 'Order ID']);
        $this->crud->addColumn(['name' => 'pickup_lat', 'type' => 'number', 'label' => 'Pickup Latitude']);
        $this->crud->addColumn(['name' => 'pickup_lon', 'type' => 'number', 'label' => 'Pickup Longitude']);
        $this->crud->addColumn(['name' => 'dropoff_lat', 'type' => 'number', 'label' => 'Dropoff Latitude']);
        $this->crud->addColumn(['name' => 'dropoff_lon', 'type' => 'number', 'label' => 'Dropoff Longitude']);
        $this->crud->addColumn(['name' => 'pickup_address', 'type' => 'text', 'label' => 'Pickup Address']);
        $this->crud->addColumn(['name' => 'pickup_postcode', 'type' => 'text', 'label' => 'Pickup Postcode']);
        $this->crud->addColumn(['name' => 'dropoff_address', 'type' => 'text', 'label' => 'Dropoff Address']);
        $this->crud->addColumn(['name' => 'dropoff_postcode', 'type' => 'text', 'label' => 'Dropoff Postcode']);
        $this->crud->addColumn(['name' => 'vrm', 'type' => 'text', 'label' => 'Vehicle Registration Number']);
        $this->crud->addColumn(['name' => 'moveable', 'type' => 'boolean', 'label' => 'Moveable']);
        $this->crud->addColumn(['name' => 'documents', 'type' => 'boolean', 'label' => 'Documents Present']);
        $this->crud->addColumn(['name' => 'keys', 'type' => 'boolean', 'label' => 'Keys Present']);
        $this->crud->addColumn(['name' => 'note', 'type' => 'textarea', 'label' => 'Note']);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(DsOrderItemRequest::class);

        $this->crud->addField(['name' => 'ds_order_id', 'type' => 'number', 'label' => 'Order ID']);
        $this->crud->addField(['name' => 'pickup_lat', 'type' => 'number', 'label' => 'Pickup Latitude']);
        $this->crud->addField(['name' => 'pickup_lon', 'type' => 'number', 'label' => 'Pickup Longitude']);
        $this->crud->addField(['name' => 'dropoff_lat', 'type' => 'number', 'label' => 'Dropoff Latitude']);
        $this->crud->addField(['name' => 'dropoff_lon', 'type' => 'number', 'label' => 'Dropoff Longitude']);
        $this->crud->addField(['name' => 'pickup_address', 'type' => 'text', 'label' => 'Pickup Address']);
        $this->crud->addField(['name' => 'pickup_postcode', 'type' => 'text', 'label' => 'Pickup Postcode']);
        $this->crud->addField(['name' => 'dropoff_address', 'type' => 'text', 'label' => 'Dropoff Address']);
        $this->crud->addField(['name' => 'dropoff_postcode', 'type' => 'text', 'label' => 'Dropoff Postcode']);
        $this->crud->addField(['name' => 'vrm', 'type' => 'text', 'label' => 'Vehicle Registration Number']);
        $this->crud->addField(['name' => 'moveable', 'type' => 'checkbox', 'label' => 'Moveable']);
        $this->crud->addField(['name' => 'documents', 'type' => 'checkbox', 'label' => 'Documents Present']);
        $this->crud->addField(['name' => 'keys', 'type' => 'checkbox', 'label' => 'Keys Present']);
        $this->crud->addField(['name' => 'note', 'type' => 'textarea', 'label' => 'Note']);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
