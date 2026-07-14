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
            'label' => 'Contract link',
            'type' => 'closure',
            'function' => function ($entry) {
                $links = FinanceContractLinkResolver::linksForContractAccess($entry);
                if ($links === []) {
                    return '<span class="text-muted">No matching latest contract for this application</span>';
                }

                $html = '<ul class="mb-0 ps-3">';
                foreach ($links as $link) {
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
        $links = FinanceContractLinkResolver::linksForContractAccess($entry);

        if ($links === []) {
            CRUD::addField([
                'name' => 'link_missing',
                'label' => 'Contract link',
                'type' => 'text',
                'value' => 'Link appears only for a latest finance application (new, new + subscription, or used + subscription).',
                'attributes' => [
                    'readonly' => 'readonly',
                ],
                'fake' => true,
            ]);

            return;
        }

        foreach ($links as $link) {
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
