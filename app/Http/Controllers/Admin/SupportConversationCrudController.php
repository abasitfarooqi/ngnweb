<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SupportConversationRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SupportConversationCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup(): void
    {
        CRUD::setModel(\App\Models\SupportConversation::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/support-conversation');
        CRUD::setEntityNameStrings('support conversation', 'support conversations');
    }

    protected function setupListOperation(): void
    {
        CRUD::column('id');
        CRUD::column('created_at')->type('datetime')->label('Created');
        CRUD::column('title');
        CRUD::column('topic');
        CRUD::column('status');
        CRUD::addColumn([
            'name' => 'customer_auth_id',
            'label' => 'Customer',
            'type' => 'select',
            'entity' => 'customerAuth',
            'model' => \App\Models\CustomerAuth::class,
            'attribute' => 'email',
        ]);
        CRUD::addColumn([
            'name' => 'service_booking_id',
            'label' => 'Enquiry',
            'type' => 'number',
        ]);
        CRUD::addColumn([
            'name' => 'assigned_backpack_user_id',
            'label' => 'Assigned to',
            'type' => 'select',
            'entity' => 'assignedBackpackUser',
            'model' => \App\Models\User::class,
            'attribute' => 'full_name',
        ]);
        CRUD::column('last_message_at')->type('datetime');
    }

    protected function setupUpdateOperation(): void
    {
        CRUD::setValidation(SupportConversationRequest::class);

        CRUD::field('title')->type('text');
        CRUD::field('topic')->type('text');
        CRUD::field('status')->type('select_from_array')->options([
            'open' => 'Open',
            'waiting_for_staff' => 'Waiting for staff',
            'waiting_for_customer' => 'Waiting for customer',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
        ]);
        CRUD::addField([
            'name' => 'assigned_backpack_user_id',
            'label' => 'Assigned to',
            'type' => 'select2',
            'entity' => 'assignedBackpackUser',
            'attribute' => 'full_name',
            'model' => \App\Models\User::class,
        ]);
        CRUD::field('external_ai_session_id')->type('text')->label('External AI session id');
    }
}
