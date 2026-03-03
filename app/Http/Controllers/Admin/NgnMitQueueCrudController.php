<?php

namespace App\Http\Controllers\Admin;

use App\Models\NgnMitQueue;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class NgnMitQueueCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(NgnMitQueue::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/dev-ngn-mit-queue');
        CRUD::setEntityNameStrings('NGN MIT Queue', 'NGN MIT Queues');
        
        // Hide from menu - not shown in sidebar
        CRUD::denyAccess('create');
        CRUD::denyAccess('delete');
    }

    protected function setupListOperation()
    {
        // Readonly fields
        CRUD::column('id')->label('ID')->type('number');
        CRUD::column('subscribable_id')->label('Subscribable ID')->type('number');
        CRUD::column('invoice_number')->label('Invoice Number')->type('text');
        
        // Editable fields (shown in list)
        CRUD::column('invoice_date')->label('Invoice Date')->type('date');
        CRUD::column('mit_fire_date')->label('MIT Fire Date')->type('datetime');
        
        // Additional info
        CRUD::column('mit_attempt')->label('MIT Attempt');
        CRUD::column('status')->label('Status');
        CRUD::column('cleared')->label('Cleared')->type('boolean');
        CRUD::column('cleared_at')->label('Cleared At')->type('datetime');
        CRUD::column('created_at')->label('Created At')->type('datetime');
        CRUD::column('updated_at')->label('Updated At')->type('datetime');
        
        // Relationship - Subscription
        CRUD::addColumn([
            'name' => 'subscribable',
            'label' => 'Subscription',
            'type' => 'relationship',
            'attribute' => 'id',
            'entity' => 'subscribable',
            'model' => 'App\Models\JudopaySubscription',
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
            $this->crud->addClause('whereHas', 'subscribable', function ($query) use ($value) {
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
            $this->crud->addClause('whereHas', 'subscribable', function ($query) use ($value) {
                $query->where('subscribable_type', $value);
            });
        });

        // Registration Number Search
        CRUD::addFilter([
            'name' => 'reg_no',
            'type' => 'text',
            'label' => 'VRM / Registration Number',
        ], false, function ($value) {
            $this->crud->query->whereHas('subscribable', function ($query) use ($value) {
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
            $this->crud->addClause('whereHas', 'subscribable.judopayOnboarding.onboardable', function ($query) use ($value) {
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
            $this->crud->addClause('whereHas', 'subscribable.judopayOnboarding.onboardable', function ($query) use ($value) {
                $query->where('phone', 'LIKE', "%$value%");
            });
        });

        // ID Search
        CRUD::addFilter([
            'name' => 'id',
            'type' => 'text',
            'label' => 'NGN MIT Queue ID',
        ], false, function ($value) {
            $this->crud->addClause('where', 'id', $value);
        });

        // Subscribable ID Search
        CRUD::addFilter([
            'name' => 'subscribable_id',
            'type' => 'text',
            'label' => 'Subscription ID',
        ], false, function ($value) {
            $this->crud->addClause('where', 'subscribable_id', $value);
        });

        // Invoice Number Search
        CRUD::addFilter([
            'name' => 'invoice_number',
            'type' => 'text',
            'label' => 'Invoice Number',
        ], false, function ($value) {
            $this->crud->addClause('where', 'invoice_number', 'LIKE', "%$value%");
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
            'name' => 'subscribable_id',
            'label' => 'Subscribable ID',
            'type' => 'number',
            'attributes' => ['readonly' => 'readonly'],
        ]);
        
        CRUD::addField([
            'name' => 'invoice_number',
            'label' => 'Invoice Number',
            'type' => 'text',
            'attributes' => ['readonly' => 'readonly'],
        ]);
        
        // Editable fields
        CRUD::addField([
            'name' => 'invoice_date',
            'label' => 'Invoice Date',
            'type' => 'date',
            'attributes' => ['required' => 'required'],
        ]);
            
        CRUD::addField([
            'name' => 'mit_fire_date',
            'label' => 'MIT Fire Date',
            'type' => 'datetime',
            'attributes' => ['required' => 'required'],
        ]);
        
        // Readonly info fields
        CRUD::addField([
            'name' => 'mit_attempt',
            'label' => 'MIT Attempt',
            'type' => 'text',
            'attributes' => ['readonly' => 'readonly'],
        ]);
        
        CRUD::addField([
            'name' => 'status',
            'label' => 'Status',
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
    }
}

