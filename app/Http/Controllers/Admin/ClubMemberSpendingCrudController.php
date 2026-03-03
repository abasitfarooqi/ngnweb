<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ClubMemberSpendingRequest;
use App\Models\ClubMember;
use App\Models\ClubMemberSpending;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\Request;

class ClubMemberSpendingCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(ClubMemberSpending::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/club-member-spending');
        CRUD::setEntityNameStrings('club member spending', 'club member spendings');
        CRUD::addFilter(
            [
                'name' => 'phone',
                'type' => 'text',
                'label' => 'Club Member Phone',
            ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas', 'clubMember', function ($query) use ($value) {
                    $query->where('phone', 'LIKE', "%$value%");
                });
            }
        );

        CRUD::addFilter([
            'name' => 'pos_invoice',
            'type' => 'text',
            'label' => 'POS Invoice',
        ]);

        CRUD::addFilter([
            'name' => 'is_paid',
            'type' => 'dropdown',
            'label' => 'Payment Status',
        ], [
            0 => 'Unpaid/Partial',
            1 => 'Fully Paid',
        ], function ($value) {
            if ($value == 1) {
                $this->crud->addClause('where', 'is_paid', true);
            } else {
                $this->crud->addClause('where', function ($query) {
                    $query->where('is_paid', false)
                        ->orWhereRaw('(total - COALESCE(paid_amount, 0)) > 0');
                });
            }
        });
    }

    protected function setupListOperation()
    {
        CRUD::enableExportButtons();
        CRUD::addColumn([
            'name' => 'club_member_id',
            'label' => 'Club Member',
            'type' => 'select2',
            'entity' => 'clubMember',
            'attribute' => 'full_name',
            'model' => ClubMember::class,
        ]);

        CRUD::addColumn([
            'name' => 'club_member_id',
            'label' => 'Phone',
            'type' => 'text',
            'entity' => 'clubMember',
            'attribute' => 'phone',
            'model' => ClubMember::class,
        ]);

        CRUD::addColumn([
            'name' => 'club_member_id',
            'label' => 'Email',
            'type' => 'text',
            'entity' => 'clubMember',
            'attribute' => 'email',
            'model' => ClubMember::class,
        ]);

        CRUD::column('date')->label('Spending Date');
        CRUD::addColumn([
            'name' => 'total',
            'label' => 'Total Amount',
            'type' => 'closure',
            'function' => function ($entry) {
                return '£' . number_format($entry->total, 2);
            },
        ]);
        CRUD::addColumn([
            'name' => 'paid_amount',
            'label' => 'Paid Amount',
            'type' => 'closure',
            'function' => function ($entry) {
                return '£' . number_format($entry->paid_amount ?? 0, 2);
            },
        ]);
        CRUD::addColumn([
            'name' => 'unpaid_amount',
            'label' => 'Unpaid Amount',
            'type' => 'closure',
            'function' => function ($entry) {
                $unpaid = round($entry->total, 2) - round($entry->paid_amount ?? 0, 2);
                return '£' . number_format(max(0, $unpaid), 2);
            },
        ]);
        CRUD::addColumn([
            'name' => 'is_paid',
            'label' => 'Status',
            'type' => 'closure',
            'function' => function ($entry) {
                $unpaid = round($entry->total, 2) - round($entry->paid_amount ?? 0, 2);
                if ($unpaid <= 0.01) {
                    return '<span class="badge badge-success">Paid</span>';
                } elseif (round($entry->paid_amount ?? 0, 2) > 0) {
                    return '<span class="badge badge-warning">Partial</span>';
                } else {
                    return '<span class="badge badge-danger">Unpaid</span>';
                }
            },
        ]);
        CRUD::addColumn([
            'name' => 'user_id',
            'label' => 'User',
            'type' => 'select2',
            'entity' => 'user',
            'attribute' => 'name',
            'model' => User::class,
        ]);
        CRUD::column('pos_invoice')->label('POS Invoice');
        CRUD::column('branch_id')->label('Branch ID');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(ClubMemberSpendingRequest::class);

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        CRUD::field('date')->label('Spending Date')->type('date');
        CRUD::field('club_member_id')->label('Club Member')->type('select2')
            ->entity('clubMember')
            ->model(ClubMember::class)
            ->attribute('detail');

        CRUD::field('total')->label('Total Amount')->type('number')->hint('Enter the total spending amount (e.g., 100.00)')->attributes(['step' => 'any']);

        CRUD::field('pos_invoice')->label('POS Invoice')->hint('Enter the POS invoice number');

        CRUD::field('branch_id')->label('Branch ID')->hint('e.g., (CATFORD, SUTTON, TOOTING)');
        // root/public\assets\js\admin\forms
        Widget::add()->type('script')->inline()->content('assets/js/admin/forms/pos-duplication-check.js');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation(); // Reuse the create operation setup

        CRUD::field('date')->label('Spending Date')->type('date');
    }

    public function checkPosInvoice(Request $request)
    {
        $posInvoice = $request->input('pos_invoice');
        $exists = ClubMemberSpending::where('pos_invoice', $posInvoice)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function fetchPosInvoice()
    {
        // Fetch all pos_invoices or apply any necessary filtering
        $posInvoices = ClubMemberSpending::select('pos_invoice')->distinct()->get();

        return response()->json($posInvoices);
    }
}
