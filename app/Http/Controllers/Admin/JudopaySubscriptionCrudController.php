<?php

namespace App\Http\Controllers\Admin;

use App\Models\JudopaySubscription;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class JudopaySubscriptionCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(JudopaySubscription::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/dev-judopay-subscription');
        CRUD::setEntityNameStrings('Judopay Subscription', 'Judopay Subscriptions');
        
        // Hide from menu - not shown in sidebar
        CRUD::denyAccess('create');
        CRUD::denyAccess('delete');
    }

    protected function setupListOperation()
    {
        // Readonly fields
        CRUD::addColumn([
            'name' => 'id',
            'label' => 'ID',
            'type' => 'number',
            'orderable' => true,
            'searchable' => true,
            'visibleInTable' => true,
            'visibleInModal' => true,
        ]);
        CRUD::column('subscribable_type')->label('Subscribable Type')->type('text');
        CRUD::column('subscribable_id')->label('Subscribable ID')->type('number');
        
        // Editable fields (shown in list)
        CRUD::column('billing_frequency')->label('Billing Frequency');
        CRUD::column('billing_day')->label('Billing Day');
        
        // Additional info
        CRUD::column('amount')->label('Amount')->type('number')->decimals(2);
        CRUD::column('status')->label('Status');
        CRUD::column('start_date')->label('Start Date')->type('date');
        CRUD::column('end_date')->label('End Date')->type('date');
        CRUD::column('created_at')->label('Created At')->type('datetime');
        CRUD::column('updated_at')->label('Updated At')->type('datetime');

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
            $this->crud->addClause('where', 'billing_frequency', $value);
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
            $this->crud->addClause('where', 'subscribable_type', $value);
        });

        // Registration Number Search
        CRUD::addFilter([
            'name' => 'reg_no',
            'type' => 'text',
            'label' => 'VRM / Registration Number',
        ], false, function ($value) {
            $this->crud->query->where(function ($query) use ($value) {
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
            $this->crud->addClause('whereHas', 'judopayOnboarding.onboardable', function ($query) use ($value) {
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
            $this->crud->addClause('whereHas', 'judopayOnboarding.onboardable', function ($query) use ($value) {
                $query->where('phone', 'LIKE', "%$value%");
            });
        });

        // ID Search
        CRUD::addFilter([
            'name' => 'id',
            'type' => 'text',
            'label' => 'Subscription ID',
        ], false, function ($value) {
            $this->crud->addClause('where', 'id', $value);
        });

        // Subscribable ID Search
        CRUD::addFilter([
            'name' => 'subscribable_id',
            'type' => 'text',
            'label' => 'Subscribable ID',
        ], false, function ($value) {
            $this->crud->addClause('where', 'subscribable_id', $value);
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
            'name' => 'subscribable_type',
            'label' => 'Subscribable Type',
            'type' => 'text',
            'attributes' => ['readonly' => 'readonly'],
        ]);
        
        // subscribable_id is a column, not a relationship - explicitly set as number
        CRUD::addField([
            'name' => 'subscribable_id',
            'label' => 'Subscribable ID',
            'type' => 'number',
            'attributes' => ['readonly' => 'readonly'],
            'entity' => false, // Explicitly tell Backpack this is NOT a relationship
        ]);
        
        // Editable fields
        CRUD::addField([
            'name' => 'billing_frequency',
            'label' => 'Billing Frequency',
            'type' => 'select_from_array',
            'options' => [
                'weekly' => 'Weekly',
                'monthly' => 'Monthly',
                'custom' => 'Custom',
            ],
            'attributes' => ['required' => 'required'],
        ]);
            
        CRUD::addField([
            'name' => 'billing_day',
            'label' => 'Billing Day',
            'type' => 'number',
            'attributes' => [
                'min' => 1,
                'max' => 31,
                'step' => 1,
            ],
            'hint' => 'Day of week (1-7) for weekly, day of month (1-28) for monthly',
        ]);
        
        // Readonly info fields
        CRUD::addField([
            'name' => 'amount',
            'label' => 'Amount',
            'type' => 'number',
            'attributes' => ['readonly' => 'readonly'],
            'decimals' => 2,
        ]);
        
        CRUD::addField([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'text',
            'attributes' => ['readonly' => 'readonly'],
        ]);
        
        CRUD::addField([
            'name' => 'start_date',
            'label' => 'Start Date',
            'type' => 'date',
            'attributes' => ['readonly' => 'readonly'],
        ]);
        
        CRUD::addField([
            'name' => 'end_date',
            'label' => 'End Date',
            'type' => 'date',
            'attributes' => ['readonly' => 'readonly'],
        ]);
    }
}

