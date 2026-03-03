<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AccessLogRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AccessLogCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AccessLogCrudController extends BaseCrudController
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
        CRUD::setModel(\App\Models\AccessLog::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/access-log');
        CRUD::setEntityNameStrings('access log', 'access logs');
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

        CRUD::column('user_id')->label('User');
        CRUD::column('ip_address');
        CRUD::column('area_attempted');
        CRUD::column('status');
        CRUD::column('message');
        CRUD::column('created_at')->label('Timestamp');

        CRUD::filter('user_id')->label('User');
        CRUD::filter('ip_address');
        CRUD::filter('status')->type('dropdown')->values(['allowed' => 'Allowed', 'blocked' => 'Blocked']);
        CRUD::filter('created_at')->type('date_range')->label('Time Range');
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
        CRUD::setValidation(AccessLogRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        CRUD::field('user_id');
        CRUD::field('ip_address');
        CRUD::field('area_attempted');
        CRUD::field('status')->type('enum');
        CRUD::field('message');
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
