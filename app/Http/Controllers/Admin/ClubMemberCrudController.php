<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ClubMemberRequest;
use App\Models\NgnPartner;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ClubMemberCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ClubMemberCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\ClubMember::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/club-member');
        CRUD::setEntityNameStrings('NGN CLUB MEMBER', 'NGN CLUB MEMBERS');
        CRUD::enableExportButtons();

    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     *
     * @return void
     */
    protected function setupListOperation()
    {
        // Only allow access if the logged-in user has one of the allowed IDs
        if (! in_array(backpack_user()->id, [65, 66, 93])) {
            $this->crud->denyAccess(['list', 'create', 'update', 'delete', 'show']);

            return;
        }

        CRUD::addFilter(
            [
                'name' => 'year',
                'type' => 'select2',
                'label' => 'Year',
            ],
            [
                2025 => '2025',
                2024 => '2024',
                2023 => '2023',
                2022 => '2022',
                2021 => '2021',
                2020 => '2020',
                2019 => '2019',
                2018 => '2018',
                2017 => '2017',
                2016 => '2016',
                2015 => '2015',
                2014 => '2014',
            ],
            function ($value) { // if the filter is active
                \Log::info('Year filter applied: '.$value);
                $this->crud->addClause('where', 'year', $value);
            }
        );

        CRUD::addFilter(
            [
                'name' => 'is_partner',
                'type' => 'select2',
                'label' => 'Partner',
            ],
            [
                1 => 'Yes',
                0 => 'No',
            ],
            function ($value) {
                $this->crud->addClause('where', 'is_partner', $value);
            }
        );

        CRUD::addFilter(
            [
                'name' => 'vrm',
                'type' => 'text',
                'label' => 'VRM',
            ],
            false,
            function ($value) {
                $this->crud->addClause('where', 'vrm', 'LIKE', "%$value%");
            }
        );

        CRUD::addFilter(
            [
                'name' => 'phone',
                'type' => 'text',
                'label' => 'Phone',
            ],
            false,
            function ($value) {
                $this->crud->addClause('where', 'phone', 'LIKE', "%$value%");
            }
        );

        CRUD::addFilter(
            [
                'name' => 'email',
                'type' => 'text',
                'label' => 'Email',
            ],
            false,
            function ($value) {
                $this->crud->addClause('where', 'email', 'LIKE', "%$value%");
            }
        );

        // // Remove or comment out any other year filters
        // CRUD::addFilter([
        //     'name'  => 'year_range',
        //     'type'  => 'range',
        //     'label' => 'Year Range',
        //     'label_from' => 'Year from',
        //     'label_to' => 'Year to'
        // ],
        // false,
        // function($value) {
        //     if (!empty($value)) {
        //         $from = $value['from'] ?? '';
        //         $to = $value['to'] ?? '';

        //         if ($from) {
        //             $this->crud->addClause('where', 'year', '>=', str_pad($from, 4, '0', STR_PAD_LEFT));
        //         }

        //         if ($to) {
        //             $this->crud->addClause('where', 'year', '<=', str_pad($to, 4, '0', STR_PAD_LEFT));
        //         }
        //     }
        // });

        // CRUD::setFromDb(); // set columns from db columns.

        CRUD::column('phone')->label('Phone');
        CRUD::column('vrm')->label('VRM');
        CRUD::column('full_name')->label('Full Name');
        CRUD::column('email')->label('Email');
        CRUD::column('year')->label('Year');
        CRUD::column('is_paid')->label('Paid');
        CRUD::column('make')->label('Make');
        CRUD::column('model')->label('Model');
        CRUD::column('is_partner')->label('Partner');
        CRUD::column('is_active')->label('Active');
        CRUD::column('created_at')->label('Created At');
        CRUD::field('is_partner')->label('Partner')->type('checkbox');
        CRUD::field('ngn_partner_id')->label('NGN Partner')->type('select')->entity('partner')->model(NgnPartner::class)->attribute('companyname')->pivot(false);
        CRUD::column('email_sent')->label('Email Sent');
        CRUD::column('passkey')->label('Passkey');
        CRUD::column('tc_agreed')->label('Terms & Conditions Agreed');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ClubMemberRequest::class);
        // CRUD::setFromDb(); // set fields from db columns.

        // Add fields for create operation
        CRUD::field('full_name')->label('Full Name');
        CRUD::field('vrm')->label('VRM')->type('text');
        CRUD::field('make')->label('Make')->type('text');
        CRUD::field('model')->label('Model')->type('text');
        CRUD::field('year')->label('Year')->type('number');
        CRUD::field('phone')->label('Phone');
        CRUD::field('email')->label('Email');
        CRUD::field('passkey')->label('Passkey');
        CRUD::field('is_active')->type('checkbox')->label('Active');
        CRUD::field('is_partner')->label('Partner')->type('checkbox');
        CRUD::field('ngn_partner_id')->label('NGN Partner')->type('select')->entity('partner')->model(NgnPartner::class)->attribute('companyname')->pivot(false);
        CRUD::field('email_sent')->label('Email Sent');
        CRUD::field('tc_agreed')->type('checkbox')->label('Terms & Conditions Agreed');
        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
