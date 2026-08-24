<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AgreementAccessRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class AgreementAccessCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\AgreementAccess::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/agreement-access');
        CRUD::setEntityNameStrings('agreement access', 'agreement accesses');

    }

    protected function setupListOperation()
    {
        CRUD::enableExportButtons();
        CRUD::column('customer.first_name')->label('name');
        CRUD::setFromDb();
        CRUD::column('customer_id')->type('hidden');
        CRUD::orderBy('id', 'desc');

        $this->crud->addColumn([
            'name' => 'rental_agreement_customer',
            'label' => 'Customer signing link',
            'type' => 'closure',
            'function' => function ($entry) {
                $url = \App\Models\AgreementAccess::customerSigningUrl(
                    (int) $entry->customer_id,
                    (string) $entry->passcode
                );

                return '<a href="'.$url.'" target="_blank">'.$url.'</a>';
            },
            'escaped' => false,
        ]);

        $this->crud->addColumn([
            'name' => 'loyalty_scheme_link',
            'label' => 'Loyalty Scheme Link (copy for customer)',
            'type' => 'closure',
            'function' => function ($entry) {
                $url = \App\Models\AgreementAccess::loyaltySchemeSigningUrl(
                    (int) $entry->customer_id,
                    (string) $entry->passcode
                );

                return '<a href="'.$url.'" target="_blank">'.$url.'</a>';
            },
            'escaped' => false,
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(AgreementAccessRequest::class);
        CRUD::setFromDb();
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
