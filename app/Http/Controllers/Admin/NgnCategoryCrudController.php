<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\NgnCategoryRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class NgnCategoryCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class NgnCategoryCrudController extends BaseCrudController
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
        CRUD::setModel(\App\Models\NgnCategory::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/ngn-category');
        CRUD::setEntityNameStrings('ngn category', 'ngn categories');
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
        CRUD::column('name')->type('text');                // Name of the category
        CRUD::column('image_url')->type('text');           // URL to the category image
        CRUD::column('created_at')->type('datetime');      // Timestamp for when the category was added
        CRUD::column('updated_at')->type('datetime');      // Timestamp for when the category was last updated
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
        CRUD::setValidation(NgnCategoryRequest::class); // Set validation for the request

        CRUD::field('name')->type('text');                // Name of the category
        CRUD::field('image_url')->type('text');           // URL to the category image
        CRUD::field('created_at')->type('hidden');        // Timestamp for when the category was added (usually hidden on create)
        CRUD::field('updated_at')->type('hidden');        // Timestamp for when the category was last updated (usually hidden on create)
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
