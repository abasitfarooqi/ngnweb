<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ContractAccessRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ContractAccessCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ContractAccessCrudController extends BaseCrudController
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
        CRUD::setModel(\App\Models\ContractAccess::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/contract-access');
        CRUD::setEntityNameStrings('Contract Link', 'Contract Links');
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
        CRUD::addColumn([
            'name' => 'id',
            'type' => 'text',
            'label' => 'Contract Access ID',
        ]);
        CRUD::addColumn([
            'name' => 'customer_id',
            'type' => 'select',
            'entity' => 'customer',
            'attribute' => 'first_name',
            'model' => "App\\Models\\Customer",
            'label' => 'Customer Name',
        ]);
        CRUD::addColumn([
            'name' => 'application_id',
            'type' => 'select',
            'entity' => 'application',
            'attribute' => 'id',
            'model' => "App\\Models\\FinanceApplication",
            'label' => 'Contract ID',
        ]);
        CRUD::addColumn([
            'name' => 'passcode',
            'type' => 'text',
            'label' => 'Passcode',
        ]);
        CRUD::addColumn([
            'name' => 'expires_at',
            'type' => 'datetime',
            'label' => 'Expires At',
        ]);
        CRUD::enableExportButtons();
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     *
     * @return void
     */
    

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ContractAccessRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

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

        CRUD::addField([
            'name' => 'contract_link',
            'label' => 'Contract Link',
            'type' => 'text',
            'value' => 'https://neguinhomotors.co.uk/sale-ins-latest/'.$this->crud->getCurrentEntry()->customer_id.'/'.$this->crud->getCurrentEntry()->passcode,
            'attributes' => [
                'readonly' => 'readonly',
            ],
        ]);

    }
}
