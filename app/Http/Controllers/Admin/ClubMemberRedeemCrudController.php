<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ClubMemberRedeemRequest;
use App\Models\ClubMember;
use App\Models\ClubMemberPurchase;
use App\Models\ClubMemberRedeem;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

class ClubMemberRedeemCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }

    public function setup()
    {
        CRUD::setModel(ClubMemberRedeem::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/club-member-redeem');
        CRUD::setEntityNameStrings('club member redeem', 'club member redeems');

    }

    protected function setupListOperation()
    {
        CRUD::column('date')->label('Redeem Date')->type('date');
        CRUD::addColumn([
            'name' => 'club_member_id',
            'label' => 'Club Member',
            'type' => 'select',
            'entity' => 'clubMember',
            'attribute' => 'full_name',
            'model' => ClubMember::class,
        ]);
        CRUD::column('redeem_total')->label('Total Redeemed')->type('number')->attributes(['step' => 'any']);

        CRUD::column('note')->label('Note')->type('textarea');
        CRUD::column('pos_invoice')->label('POS Invoice');
        CRUD::column('user_id')->label('User ID');
        CRUD::column('branch_id')->label('Branch ID');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(ClubMemberRedeemRequest::class);

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        CRUD::field('club_member_id')->label('Club Member')->type('select2')
            ->entity('clubMember')
            ->model(ClubMember::class)
            ->attribute('detail');

        CRUD::field('date')->label('Redeem Date')->type('date');

        CRUD::field('redeem_total')->label('Total Redeemed')->type('number')->attributes(['step' => 'any']);

        CRUD::field('pos_invoice')->label('POS Invoice');
        CRUD::field('note')->label('Note')->type('textarea');

        CRUD::field('branch_id')->label('Branch ID')->hint('e.g., (CATFORD, SUTTON, TOOTING)');

        CRUD::addField([
            'name' => 'include_today',
            'type' => 'hidden',
            'default' => 0,
        ]);
        CRUD::addField([
            'name' => 'remaining_balance',
            'label' => 'Available Redeemable Balance',
            'type' => 'text',
            'attributes' => ['readonly' => 'readonly'],
            'hint' => 'This is calculated automatically based on purchases and redemptions.',
        ]);

        Widget::add()->type('script')->inline()->content('assets/js/admin/forms/remaining-balance-check.js');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation(); // Reuse the create operation setup
        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        CRUD::field('club_member_id')->label('Club Member')->type('select2')
            ->entity('clubMember')
            ->model(ClubMember::class)
            ->attribute('detail');

        // Ensure the date field is included and handled
        CRUD::field('date')->label('Redeem Date')->type('date');
    }

    public function store()
    {
        $response = $this->traitStore(); // run Backpack’s normal save
        $this->updatePurchases($this->crud->entry);

        return $response;
    }

    public function update()
    {
        $response = $this->traitUpdate(); // run Backpack’s normal update
        $this->updatePurchases($this->crud->entry);

        return $response;
    }

    protected function updatePurchases($entry)
    {
        if (! $entry) {
            return;
        }

        $clubMemberId = $entry->club_member_id;
        $today = now()->toDateString();

        $query = ClubMemberPurchase::where('club_member_id', $clubMemberId)
            ->where('is_redeemed', false);

        if (request('include_today') != 1) {
            $query->whereDate('date', '<>', $today);
        }

        $purchases = $query->get();

        if ($purchases->isEmpty()) {
            return;
        }

        $purchaseIds = $purchases->pluck('id')->toArray();
        $totalRedeemed = 0;

        foreach ($purchases as $purchase) {
            $redeemValue = $purchase->discount ?? 0;   // ✅ base redemption on discount

            $purchase->redeem_amount = $redeemValue;
            $purchase->is_redeemed = true;
            $purchase->save();

            $totalRedeemed += $redeemValue;
        }

        $noteLine = "Redeemed £{$totalRedeemed} from purchase IDs: ".implode(', ', $purchaseIds);

        $entry->redeem_total = $totalRedeemed;
        $entry->note = trim(($entry->note ?? '')."\n".$noteLine);
        $entry->save();
    }
}
