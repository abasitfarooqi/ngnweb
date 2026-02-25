<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DsOrderRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;

class DsOrderCrudController extends CrudController
{
    public function setup()
    {
        $this->crud->setModel(\App\Models\DsOrder::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/ds-order');
        $this->crud->setEntityNameStrings('ds order', 'ds orders');
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn(['name' => 'full_name', 'type' => 'text', 'label' => 'Full Name']);
        $this->crud->addColumn(['name' => 'phone', 'type' => 'text', 'label' => 'Phone']);
        $this->crud->addColumn(['name' => 'address', 'type' => 'text', 'label' => 'Address']);
        $this->crud->addColumn(['name' => 'postcode', 'type' => 'text', 'label' => 'Postcode']);
        $this->crud->addColumn(['name' => 'pick_up_datetime', 'type' => 'datetime', 'label' => 'Pickup Date & Time']);
        $this->crud->addColumn(['name' => 'note', 'type' => 'textarea', 'label' => 'Note']);
        $this->crud->addColumn(['name' => 'proceed', 'type' => 'boolean', 'label' => 'Proceed']);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(DsOrderRequest::class);

        $this->crud->addField(['name' => 'full_name', 'type' => 'text', 'label' => 'Full Name']);
        $this->crud->addField(['name' => 'phone', 'type' => 'text', 'label' => 'Phone']);
        $this->crud->addField(['name' => 'address', 'type' => 'text', 'label' => 'Address']);
        $this->crud->addField(['name' => 'postcode', 'type' => 'text', 'label' => 'Postcode']);
        $this->crud->addField(['name' => 'pick_up_datetime', 'type' => 'datetime_picker', 'label' => 'Pickup Date & Time']);
        $this->crud->addField(['name' => 'note', 'type' => 'textarea', 'label' => 'Note']);
        $this->crud->addField(['name' => 'proceed', 'type' => 'checkbox', 'label' => 'Proceed']);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
