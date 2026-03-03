<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\NgnBrandRequest;
use App\Models\NgnBrand;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

/**
 * Class NgnBrandCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class NgnBrandCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
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
        CRUD::setModel(\App\Models\NgnBrand::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/ngn-brand');
        CRUD::setEntityNameStrings('ngn brand', 'ngn brands');
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
        CRUD::column('name')->type('text')->label('Brand Name');                // Name of the brand
        CRUD::column('image_url')->type('text');           // URL to the brand's logo image
        CRUD::column('created_at')->type('datetime');      // Timestamp for when the brand was added
        CRUD::column('updated_at')->type('datetime');      // Timestamp for when the brand was last updated
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
        CRUD::setValidation(NgnBrandRequest::class); // Set validation for the request

        CRUD::field('name')->type('text');             // Name of the brand
        CRUD::field('image_url')->type('text');        // URL to the brand's logo image
        CRUD::field('created_at')->type('hidden');     // Timestamp for when the brand was added (usually hidden on create)
        CRUD::field('updated_at')->type('hidden');     // Timestamp for when the brand was last updated (usually hidden on create)
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

    public function storeInlineCreate(Request $request)
    {
        // Validate the request
        $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);

        // Create the brand
        $brand = NgnBrand::create($request->only('name'));

        // Return a success response
        return response()->json(['id' => $brand->id, 'name' => $brand->name]);
    }

    public function fetchBrand()
    {
        return $this->fetch(\App\Models\NgnBrand::class);
    }

    protected function setupInlineCreateOperation()
    {
        $this->crud->addField([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Brand Name',
        ]);
    }
}
