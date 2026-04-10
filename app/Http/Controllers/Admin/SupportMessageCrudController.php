<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SupportMessageRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SupportMessageCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup(): void
    {
        CRUD::setModel(\App\Models\SupportMessage::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/support-message');
        CRUD::setEntityNameStrings('support message', 'support messages');
    }

    protected function setupListOperation(): void
    {
        CRUD::column('id');
        CRUD::column('created_at')->type('datetime');
        CRUD::addColumn([
            'name' => 'conversation_id',
            'label' => 'Conversation ID',
            'type' => 'number',
        ]);
        CRUD::column('sender_type');
        CRUD::addColumn([
            'name' => 'sender_user_id',
            'label' => 'Staff sender',
            'type' => 'select',
            'entity' => 'senderUser',
            'model' => \App\Models\User::class,
            'attribute' => 'full_name',
        ]);
        CRUD::addColumn([
            'name' => 'sender_customer_auth_id',
            'label' => 'Customer sender',
            'type' => 'select',
            'entity' => 'senderCustomerAuth',
            'model' => \App\Models\CustomerAuth::class,
            'attribute' => 'email',
        ]);
        CRUD::column('body')->type('textarea')->limit(120);
    }

    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(SupportMessageRequest::class);

        CRUD::field('conversation_id')->type('number')->label('Conversation ID');
        CRUD::field('sender_type')->type('select_from_array')->options([
            'staff' => 'Staff',
            'system' => 'System',
            'ai' => 'AI',
            'customer' => 'Customer',
        ])->default('staff');
        CRUD::field('body')->type('textarea');
        CRUD::addField([
            'name' => 'sender_user_id',
            'type' => 'hidden',
            'default' => backpack_user()?->id,
        ]);
    }

    public function store()
    {
        if (request('sender_type') === 'staff') {
            request()->merge([
                'sender_user_id' => backpack_user()?->id,
                'sender_customer_auth_id' => null,
            ]);
        }

        return $this->traitStore();
    }
}
