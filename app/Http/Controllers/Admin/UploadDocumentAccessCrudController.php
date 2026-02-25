<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UploadDocumentAccessRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class UploadDocumentAccessCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\UploadDocumentAccess::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/upload-document-access');
        CRUD::setEntityNameStrings('upload document access', 'upload document accesses');
    }

    protected function setupListOperation()
    {
        CRUD::setFromDb();

        $this->crud->addColumn([
            'name' => 'customer_name',
            'label' => 'Customer Name',
            'type' => 'model_function',
            'function_name' => 'getCustomerName',
        ]);

        $this->crud->addColumn([
            'name' => 'link_html',
            'label' => 'Link',
            'type' => 'closure',
            'function' => function ($entry) {
                $url = url("/upload-doc/{$entry->customer_id}/{$entry->passcode}");

                return '<a href="'.$url.'" target="_blank">'.$url.'</a>';
            },
            'escaped' => false, // this ensures the HTML is not escaped
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(UploadDocumentAccessRequest::class);
        CRUD::setFromDb();
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
