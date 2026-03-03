<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EcOrderRequest;
use App\Models\Ecommerce\EcOrder;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class EcOrderCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(EcOrder::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/ec-order');
        CRUD::setEntityNameStrings('ONLINE ORDER', 'ONLINE ORDERS');
    }

    protected function setupListOperation()
    {
        // Remove automatic column loading to prevent FK columns from showing
        // CRUD::setFromDb();
        CRUD::column('id')
            ->label('ORDER NUMBER')
            ->type('text')
            ->wrapper([
                'class' => 'h4 font-weight-bold',
            ]);

        CRUD::column('order_date')->label('ORDER DATETIME')->type('datetime')->wrapper(['class' => 'form-group col-md-4']);

        CRUD::column('order_status')->label('STATUS');
        CRUD::column('payment_status')
            ->label('PAYMENT STATUS')
            ->type('text')
            ->wrapper([
                'class' => function ($crud, $column, $entry) {
                    return $entry->payment_status === 'paid' ? 'text-success font-weight-bold' : 'text-warning';
                },
            ]);
        CRUD::column('shipping_status')->label('SHIPPING STATUS');

        CRUD::column('customer.customer.full_name')
            ->label('CUSTOMER NAME')
            ->type('relationship');

        CRUD::column('grand_total')
            ->label('GRAND TOTAL')
            ->type('number')
            ->decimals(2)
            ->prefix('£');

        CRUD::column('shippingMethod')->type('relationship')
            ->label('SHIPPING METHOD')
            ->attribute('name');

        CRUD::column('branch')->type('relationship')
            ->label('BRANCH')
            ->attribute('name');

        $this->crud->removeColumns([
            'customer_id',
            'shipping_method_id',
            'payment_method_id',
            'customer_address_id',
            'branch_id',
        ]);

        // Filters

        // order number
        CRUD::filter('id')->type('text')->label('Order Number');

        // CRUD::filter('order_date')->type('date_range')->label('Order Date Range');

        CRUD::filter('order_status')->type('dropdown')->label('Order Status')->values([
            'Confirmed' => 'Confirmed',
            'Cancelled' => 'Cancelled',
            'In Progress' => 'In Progress',
            'Pending' => 'Pending',
            'Ready to collect' => 'Ready to collect',
            'Delivered' => 'Delivered',
        ]);

        CRUD::filter('payment_status')->type('dropdown')->label('Payment Status')->values([
            'Paid' => 'Paid',
            'Pending' => 'pending',
        ]);

        CRUD::filter('shipping_status')->type('dropdown')->label('Shipping Status')->values([
            'Shipped' => 'Shipped',
            'Delivered' => 'Delivered',
            'Pending' => 'Pending',
        ]);

        // CRUD::filter('customer.customer.full_name')->type('text')->label('Customer Name');

        // CRUD::filter('shippingMethod.name')->type('dropdown')->label('Shipping Method')->values(
        //     \App\Models\Ecommerce\EcShippingMethod::pluck('name', 'id')->toArray()
        // );
        // CRUD::filter('branch.name')->type('dropdown')->label('Branch')->values(
        //     \App\Models\Branch::pluck('name', 'id')->toArray()
        // );
        // CRUD::filter('grand_total')->type('range')->label('Total Amount Range');
        // CRUD::filter('paymentMethod.title')->type('dropdown')->label('Payment Method')->values(
        //     \App\Models\Ecommerce\EcPaymentMethod::pluck('title', 'id')->toArray()
        // );
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(EcOrderRequest::class);

        // order number - made more prominent
        CRUD::field('id')
            ->type('text')
            ->label('ORDER NUMBER')
            ->attributes(['readonly' => 'readonly'])
            ->wrapper(['class' => 'form-group col-md-5'])
            ->hint('ORDER NUMBER')
            ->size(2);

        CRUD::field('order_date')
            ->type('datetime')
            ->wrapper(['class' => 'form-group col-md-4'])
            ->attributes(['readonly' => 'readonly'])
            ->hint('ORDER DATETIME');

        // Payment Status field with color
        CRUD::field('payment_status')
            ->type('text')
            ->attributes(['readonly' => 'readonly'])
            ->label('Payment Status')
            ->wrapper([
                'class' => function ($crud, $column, $entry) {
                    $baseClass = 'form-group col-md-3';

                    return $entry->payment_status === 'paid'
                        ? $baseClass.' text-success font-weight-bold'
                        : $baseClass.' text-warning';
                },
            ])
            ->hint('PAYMENT STATUS');

        // Order status
        CRUD::field('order_status')
            ->type('select2_from_array')
            ->options([
                'Pending' => 'pending',
                'In Progress' => 'In Progress',
                'Confirmed' => 'Confirmed',
                'Ready to collect' => 'Ready to collect',
                'Delivered' => 'Delivered',
                'Cancelled' => 'Cancelled',
            ])
            ->label('Order Status')
            ->wrapper([
                'class' => function ($crud, $column, $entry) {
                    $baseClass = 'form-group col-md-12';
                    switch ($entry->order_status) {
                        case 'Confirmed': return $baseClass.' text-success font-weight-bold';
                        case 'Cancelled': return $baseClass.' text-danger';
                        case 'Pending': return $baseClass.' text-warning';
                        case 'Ready to collect': return $baseClass.' text-info';
                        case 'Delivered': return $baseClass.' text-success';
                        default: return $baseClass.' text-info';
                    }
                },
            ])
            ->hint('Select the current status of the order');

        CRUD::field('paymentMethod')
            ->type('relationship')
            ->attributes([
                'readonly' => 'readonly',
                'disabled' => 'disabled',
            ])
            ->attribute('title')
            ->hint('Selected payment method')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('total_amount')
            ->type('number')
            ->attributes(['readonly' => 'readonly'])
            ->prefix('£')
            ->hint('Total amount before discounts and taxes')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('grand_total')
            ->type('number')
            ->attributes(['readonly' => 'readonly'])
            ->prefix('£')
            ->hint('Final total including all charges')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('discount')
            ->type('number')
            ->attributes(['readonly' => 'readonly'])
            ->prefix('£')
            ->hint('Applied discount amount')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('tax')
            ->type('number')
            ->attributes(['readonly' => 'readonly'])
            ->prefix('£')
            ->hint('Applied tax amount')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('shipping_cost')
            ->type('number')
            ->attributes(['readonly' => 'readonly'])
            ->prefix('£')
            ->hint('Shipping cost')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('payment_date')
            ->type('hidden')
            ->attributes(['readonly' => 'readonly'])
            ->hint('Date when the payment was processed')
            ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('payment_reference')
            ->type('hidden')
            ->attributes(['readonly' => 'readonly'])
            ->hint('Payment reference number')
            ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('customer')
            ->type('relationship')
            ->attributes([
                'readonly' => 'readonly',
                'disabled' => 'disabled',
            ])
            ->attribute('customer.full_name')
            ->hint('Customer who placed the order');

        CRUD::field('customerAddress')
            ->type('relationship')
            ->attributes([
                'readonly' => 'readonly',
                'disabled' => 'disabled',
            ])
            ->attribute('full_address')
            ->hint('Delivery address')
            ->wrapper(['class' => 'form-group col-md-12']);

        CRUD::field('shippingMethod')
            ->type('relationship')
            ->attributes([
                'readonly' => 'readonly',
                'disabled' => 'disabled',
            ])
            ->attribute('name')
            ->hint('Selected shipping method')
            ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('branch')
            ->type('relationship')
            ->attributes([
                'readonly' => 'readonly',
                'disabled' => 'disabled',
            ])
            ->attribute('name')
            ->hint('Branch handling the order')
            ->wrapper(['class' => 'form-group col-md-3']);

        // Shipping status with color
        CRUD::field('shipping_status')
            ->type('text')
            ->attributes(['readonly' => 'readonly'])
            ->wrapper([
                'class' => function ($crud, $column, $entry) {
                    $baseClass = 'form-group col-md-3';
                    switch ($entry->shipping_status) {
                        case 'Delivered': return $baseClass.' text-success font-weight-bold';
                        case 'Shipped': return $baseClass.' text-info';
                        case 'Processing': return $baseClass.' text-warning';
                        default: return $baseClass.' text-muted';
                    }
                },
            ])
            ->hint('Current shipping status');

        CRUD::field('shipping_date')
            ->type('datetime')
            ->attributes(['readonly' => 'readonly'])
            ->hint('Date when the order was shipped')
            ->wrapper(['class' => 'form-group col-md-3']);

    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    // public function update()
    // {
    //     // Get the old order status before update
    //     $order = $this->crud->getEntry(request()->id);
    //     $oldStatus = $order->order_status;

    //     // Perform the default update operation
    //     $response = $this->crud->update(request()->id, request()->all());

    //     // Get the new status after update
    //     $newStatus = $this->crud->getEntry(request()->id)->order_status;

    //     // Add a flash message about the status change
    //     if ($oldStatus !== $newStatus) {
    //         \Alert::success("Order status updated from {$oldStatus} to {$newStatus}.")->flash();
    //     }

    //     return $response;
    // }
}
