<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ClubMemberPurchaseRequest;
use App\Models\ClubMember;
use App\Models\ClubMemberPurchase;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController; // Import ClubMember model
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD; // Import ClubMemberPurchase model
use Backpack\CRUD\app\Library\Widget; // Import User model
use Illuminate\Http\Request;

class ClubMemberPurchaseCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(ClubMemberPurchase::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/club-member-purchase');
        CRUD::setEntityNameStrings('club member purchase', 'club member purchases');
        CRUD::addFilter(
            [
                'name' => 'phone',
                'type' => 'text',
                'label' => 'Club Member Phone',
            ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas', 'ClubMember', function ($query) use ($value) {
                    $query->where('phone', 'LIKE', "%$value%");
                });
            }
        );

        CRUD::addFilter([
            'name' => 'pos_invoice',
            'type' => 'text',
            'label' => 'POS Invoice',
        ]);
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
            'label' => 'Club Member',
            'type' => 'text',
            'entity' => 'clubMember',
            'attribute' => 'phone',
            'model' => ClubMember::class,
        ]);

        CRUD::addColumn([
            'name' => 'club_member_id',
            'label' => 'Club Member',
            'type' => 'text',
            'entity' => 'clubMember',
            'attribute' => 'email',
            'model' => ClubMember::class,
        ]);

        CRUD::column('date')->label('Purchase Date');
        CRUD::column('percent')->label('Percent Earned')->attributes(['step' => 'any']);
        CRUD::column('total')->label('Total Amount')->attributes(['step' => 'any']);
        CRUD::column('discount')->label('Discount Amount')->attributes(['step' => 'any']);
        CRUD::column('is_redeemed')->label('Redeemed')->type('boolean');
        CRUD::addColumn([
            'name' => 'id',
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
        CRUD::setValidation(ClubMemberPurchaseRequest::class);

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        CRUD::field('date')->label('Purchase Date')->type('date');
        CRUD::field('club_member_id')->label('Club Member')->type('select2')
            ->entity('clubMember')
            ->model(ClubMember::class)
            ->attribute('detail');

        CRUD::field('percent')->label('Percent Earned')->type('number')->hint('Enter the percentage earned (e.g., 10.00)')->attributes(['step' => 'any']);
        CRUD::field('total')->label('Total Amount')->type('number')->hint('Enter the total amount (e.g., 100.00)')->attributes(['step' => 'any']);
        CRUD::field('discount')->label('Discount Amount')->type('number')->hint('This will be calculated automatically based on percentage and total')->attributes(['step' => 'any']);
        CRUD::field('is_redeemed')->label('Redeemed')->type('checkbox');

        CRUD::field('pos_invoice')->label('POS Invoice')->hint('Enter the POS invoice number');

        CRUD::field('branch_id')->label('Branch ID')->hint('e.g., (CATFORD, SUTTON, TOOTING)');
        // root/public\assets\js\admin\forms
        Widget::add()->type('script')->inline()->content('assets/js/admin/forms/pos-duplication-check.js');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation(); // Reuse the create operation setup

        CRUD::field('date')->label('Purchase Date')->type('date');
    }

    public function checkPosInvoice(Request $request)
    {
        $posInvoice = $request->input('pos_invoice');
        $exists = ClubMemberPurchase::where('pos_invoice', $posInvoice)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function fetchPosInvoice()
    {
        // Fetch all pos_invoices or apply any necessary filtering
        $posInvoices = ClubMemberPurchase::select('pos_invoice')->distinct()->get();

        return response()->json($posInvoices);
    }
}
