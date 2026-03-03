<?php

namespace App\Http\Controllers\Admin;

use App\Models\JudopayMitQueue;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class JudopayMitQueueCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(JudopayMitQueue::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/dev-judopay-mit-queue');
        CRUD::setEntityNameStrings('Judopay MIT Queue', 'Judopay MIT Queues');
        
        // Hide from menu - not shown in sidebar
        CRUD::denyAccess('create');
        CRUD::denyAccess('delete');
    }

    protected function setupListOperation()
    {
        // Readonly fields
        CRUD::column('id')->label('ID')->type('number');
        CRUD::column('ngn_mit_queue_id')->label('NGN MIT Queue ID')->type('number');
        CRUD::column('judopay_payment_reference')->label('Payment Reference')->type('text');
        CRUD::column('cleared')->label('Cleared')->type('boolean');
        CRUD::column('cleared_at')->label('Cleared At')->type('datetime');
        
        // Editable field (shown in list)
        CRUD::column('mit_fire_date')->label('MIT Fire Date')->type('datetime');
        
        // Additional info
        CRUD::column('retry')->label('Retry');
        CRUD::column('fired')->label('Fired')->type('boolean');
        CRUD::addColumn([
            'name' => 'authorizedBy',
            'label' => 'Authorized By',
            'type' => 'relationship',
            'attribute' => 'name',
            'entity' => 'authorizedBy',
            'model' => 'App\Models\User',
        ]);
        CRUD::column('created_at')->label('Created At')->type('datetime');
        CRUD::column('updated_at')->label('Updated At')->type('datetime');
        
        // Relationship - NGN MIT Queue
        CRUD::addColumn([
            'name' => 'ngnMitQueue',
            'label' => 'NGN MIT Queue',
            'type' => 'relationship',
            'attribute' => 'id',
            'entity' => 'ngnMitQueue',
            'model' => 'App\Models\NgnMitQueue',
        ]);

        // ========== FILTERS ==========
        
        // Billing Frequency Filter
        CRUD::addFilter([
            'name' => 'billing_frequency',
            'type' => 'dropdown',
            'label' => 'Billing Frequency',
        ], [
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'custom' => 'Custom',
        ], function ($value) {
            $this->crud->addClause('whereHas', 'ngnMitQueue.subscribable', function ($query) use ($value) {
                $query->where('billing_frequency', $value);
            });
        });

        // Type Filter (Rental/Finance)
        CRUD::addFilter([
            'name' => 'subscribable_type',
            'type' => 'dropdown',
            'label' => 'Type',
        ], [
            'App\\Models\\RentingBooking' => 'Rental',
            'App\\Models\\FinanceApplication' => 'Finance',
        ], function ($value) {
            $this->crud->addClause('whereHas', 'ngnMitQueue.subscribable', function ($query) use ($value) {
                $query->where('subscribable_type', $value);
            });
        });

        // Registration Number Search
        CRUD::addFilter([
            'name' => 'reg_no',
            'type' => 'text',
            'label' => 'VRM / Registration Number',
        ], false, function ($value) {
            $this->crud->query->whereHas('ngnMitQueue.subscribable', function ($query) use ($value) {
                // For RentingBooking
                $query->where(function ($q) use ($value) {
                    $q->where('subscribable_type', 'App\\Models\\RentingBooking')
                      ->whereHasMorph('subscribable', ['App\\Models\\RentingBooking'], function ($morphQuery) use ($value) {
                          $morphQuery->whereHas('rentingBookingItems.motorbike', function ($motorbikeQuery) use ($value) {
                              $motorbikeQuery->where('reg_no', 'LIKE', "%$value%");
                          });
                      });
                })->orWhere(function ($q) use ($value) {
                    // For FinanceApplication
                    $q->where('subscribable_type', 'App\\Models\\FinanceApplication')
                      ->whereHasMorph('subscribable', ['App\\Models\\FinanceApplication'], function ($morphQuery) use ($value) {
                          $morphQuery->whereHas('application_items.motorbike', function ($motorbikeQuery) use ($value) {
                              $motorbikeQuery->where('reg_no', 'LIKE', "%$value%");
                          });
                      });
                });
            });
        });

        // Customer Name Search
        CRUD::addFilter([
            'name' => 'customer_name',
            'type' => 'text',
            'label' => 'Customer Name',
        ], false, function ($value) {
            $this->crud->addClause('whereHas', 'ngnMitQueue.subscribable.judopayOnboarding.onboardable', function ($query) use ($value) {
                $query->where(function ($q) use ($value) {
                    $q->where('first_name', 'LIKE', "%$value%")
                      ->orWhere('last_name', 'LIKE', "%$value%");
                });
            });
        });

        // Phone Number Search
        CRUD::addFilter([
            'name' => 'phone',
            'type' => 'text',
            'label' => 'Phone Number',
        ], false, function ($value) {
            $this->crud->addClause('whereHas', 'ngnMitQueue.subscribable.judopayOnboarding.onboardable', function ($query) use ($value) {
                $query->where('phone', 'LIKE', "%$value%");
            });
        });

        // ID Search
        CRUD::addFilter([
            'name' => 'id',
            'type' => 'text',
            'label' => 'Judopay MIT Queue ID',
        ], false, function ($value) {
            $this->crud->addClause('where', 'id', $value);
        });

        // NGN MIT Queue ID Search
        CRUD::addFilter([
            'name' => 'ngn_mit_queue_id',
            'type' => 'text',
            'label' => 'NGN MIT Queue ID',
        ], false, function ($value) {
            $this->crud->addClause('where', 'ngn_mit_queue_id', $value);
        });

        // Payment Reference Search
        CRUD::addFilter([
            'name' => 'judopay_payment_reference',
            'type' => 'text',
            'label' => 'Payment Reference',
        ], false, function ($value) {
            $this->crud->addClause('where', 'judopay_payment_reference', 'LIKE', "%$value%");
        });
    }

    protected function setupUpdateOperation()
    {
        // Readonly fields - use readonly only (not disabled) so they submit with form
        CRUD::addField([
            'name' => 'id',
            'label' => 'ID',
            'type' => 'number',
            'attributes' => ['readonly' => 'readonly'],
        ]);
        
        CRUD::addField([
            'name' => 'ngn_mit_queue_id',
            'label' => 'NGN MIT Queue ID',
            'type' => 'number',
            'attributes' => ['readonly' => 'readonly'],
        ]);
        
        CRUD::addField([
            'name' => 'judopay_payment_reference',
            'label' => 'Payment Reference',
            'type' => 'text',
            'attributes' => ['readonly' => 'readonly'],
        ]);
        
        CRUD::addField([
            'name' => 'cleared',
            'label' => 'Cleared',
            'type' => 'checkbox',
            'attributes' => ['readonly' => 'readonly', 'disabled' => 'disabled'],
        ]);
        
        CRUD::addField([
            'name' => 'cleared_at',
            'label' => 'Cleared At',
            'type' => 'datetime',
            'attributes' => ['readonly' => 'readonly'],
        ]);
        
        // Editable field
        CRUD::addField([
            'name' => 'mit_fire_date',
            'label' => 'MIT Fire Date',
            'type' => 'datetime',
            'attributes' => ['required' => 'required'],
        ]);
        
        // Readonly info fields
        CRUD::addField([
            'name' => 'retry',
            'label' => 'Retry',
            'type' => 'number',
            'attributes' => ['readonly' => 'readonly'],
        ]);
        
        CRUD::addField([
            'name' => 'fired',
            'label' => 'Fired',
            'type' => 'checkbox',
            'attributes' => ['readonly' => 'readonly', 'disabled' => 'disabled'],
        ]);
        
        CRUD::addField([
            'name' => 'authorized_by',
            'label' => 'Authorized By',
            'type' => 'select',
            'entity' => 'authorizedBy',
            'attribute' => 'name',
            'model' => 'App\Models\User',
            'attributes' => ['readonly' => 'readonly', 'disabled' => 'disabled'],
        ]);
    }
}

