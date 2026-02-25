<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ContactQueryRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ContactQueryCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ContactQueryCrudController extends CrudController
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
        CRUD::setModel(\App\Models\ContactQuery::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/contact-query');
        CRUD::setEntityNameStrings('contact query', 'contact queries');
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
        // CRUD::setFromDb(); // set columns from db columns.

        CRUD::column('name');
        CRUD::column('email');
        CRUD::column('subject');
        CRUD::column('phone');
        CRUD::column('message');
        CRUD::column('is_dealt')->type('boolean');
        CRUD::column('notes');
        // CRUD::column('dealt_by_user_id')->type('select')->entity('user')->model('App\Models\User')->attribute('name');
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
        CRUD::setValidation(ContactQueryRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        CRUD::field('is_dealt')->type('checkbox');
        CRUD::field('notes')->type('textarea');

        CRUD::addField(['name' => 'dealt_by_user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);
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
