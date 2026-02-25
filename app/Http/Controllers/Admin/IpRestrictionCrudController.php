<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\IpRestrictionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class IpRestrictionCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class IpRestrictionCrudController extends CrudController
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
        CRUD::setModel(\App\Models\IpRestriction::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/ip-restriction');
        CRUD::setEntityNameStrings('ip restriction', 'ip restrictions');
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
        CRUD::setFromDb(); // set columns from db columns.

        CRUD::column('ip_address');
        CRUD::column('status');
        CRUD::column('restriction_type');
        CRUD::column('label');
        CRUD::column('user_id');

        CRUD::filter('status')->type('dropdown')->values(['allowed' => 'Allowed', 'blocked' => 'Blocked']);
        CRUD::filter('restriction_type')->type('dropdown')->values(['admin_only' => 'Admin Only', 'full_site' => 'Full Site']);
        CRUD::filter('label');
        // CRUD::filter('user_id')->type('dropdown')->model(\App\Models\User::class)->attribute('detail');
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
        CRUD::setValidation(IpRestrictionRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        CRUD::field('ip_address');
        CRUD::field('status')->type('enum');
        CRUD::field('restriction_type')->type('enum');
        CRUD::field('label');
        CRUD::field('user_id')->type('select2')->model(\App\Models\User::class)->attribute('detail');

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
