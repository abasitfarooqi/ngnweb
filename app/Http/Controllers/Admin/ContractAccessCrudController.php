<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ContractAccessRequest;
use App\Services\FinanceContractLinkResolver;
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
        $this->crud->query->with('application');
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

        $this->crud->addColumn([
            'name' => 'contract_links',
            'label' => 'Contract links',
            'type' => 'closure',
            'function' => function ($entry) {
                $customerId = (int) ($entry->customer_id ?? 0);
                $passcode = (string) ($entry->passcode ?? '');
                if ($customerId < 1 || $passcode === '') {
                    return '<span class="text-muted">Missing customer or passcode</span>';
                }

                $html = '<ul class="mb-0 ps-3">';
                foreach (FinanceContractLinkResolver::accessLinks($customerId, $passcode) as $link) {
                    $html .= '<li class="mb-1"><strong>'.e($link['label']).'</strong><br>'
                        .'<a href="'.e($link['url']).'" target="_blank">'.e($link['url']).'</a></li>';
                }
                $html .= '</ul>';

                return $html;
            },
            'escaped' => false,
        ]);
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
        CRUD::setValidation(ContractAccessRequest::class);
        CRUD::setFromDb(); // set fields from db columns.
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

        $entry = $this->crud->getCurrentEntry();
        $customerId = (int) ($entry->customer_id ?? 0);
        $passcode = (string) ($entry->passcode ?? '');

        if ($customerId < 1 || $passcode === '') {
            CRUD::addField([
                'name' => 'link_missing',
                'label' => 'Contract links',
                'type' => 'text',
                'value' => 'Save a customer ID and passcode to generate the three contract links.',
                'attributes' => [
                    'readonly' => 'readonly',
                ],
                'fake' => true,
            ]);

            return;
        }

        foreach (FinanceContractLinkResolver::accessLinks($customerId, $passcode) as $link) {
            CRUD::addField([
                'name' => 'link_'.$link['key'],
                'label' => $link['label'],
                'type' => 'text',
                'value' => $link['url'],
                'attributes' => [
                    'readonly' => 'readonly',
                ],
                'fake' => true,
            ]);
        }
    }
}
