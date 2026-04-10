<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ServiceBookingRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ServiceBookingCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }

    public function setup()
    {
        CRUD::setModel(\App\Models\ServiceBooking::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/service-booking');
        CRUD::setEntityNameStrings('service enquiry', 'service enquiries');
    }

    protected function setupListOperation()
    {
        CRUD::column('id')->label('ID');
        CRUD::column('created_at')->type('datetime')->label('Submitted');
        CRUD::column('service_type')->label('Service type');
        CRUD::column('fullname')->label('Name');
        CRUD::column('phone')->label('Phone');
        CRUD::column('email')->label('Email');
        CRUD::column('status')->label('Status');
        CRUD::column('enquiry_type')->label('Type');
        CRUD::column('is_dealt')->type('boolean')->label('Dealt');
        CRUD::addColumn([
            'name' => 'dealt_by_user_id',
            'label' => 'Dealt by',
            'type' => 'select',
            'entity' => 'user',
            'model' => \App\Models\User::class,
            'attribute' => 'name',
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(ServiceBookingRequest::class);

        CRUD::field('service_type')->type('text');
        CRUD::field('subject')->type('text');
        CRUD::field('description')->type('textarea');
        CRUD::field('fullname')->type('text');
        CRUD::field('phone')->type('text');
        CRUD::field('email')->type('email');
        CRUD::field('reg_no')->type('text');
        CRUD::field('status')->type('text')->default('Pending');
        CRUD::field('is_dealt')->type('checkbox')->label('Mark as dealt');
        CRUD::field('notes')->type('textarea')->label('Staff notes');
        CRUD::addField([
            'name' => 'dealt_by_user_id',
            'type' => 'hidden',
            'default' => backpack_user()?->id,
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function store()
    {
        $this->applyDealtByForRequest();

        return $this->traitStore();
    }

    public function update()
    {
        $this->applyDealtByForRequest();

        return $this->traitUpdate();
    }

    private function applyDealtByForRequest(): void
    {
        $isDealt = request()->boolean('is_dealt');
        request()->merge([
            'is_dealt' => $isDealt,
            'dealt_by_user_id' => $isDealt ? backpack_user()?->id : null,
        ]);
    }
}
