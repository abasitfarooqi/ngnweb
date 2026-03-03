<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\NgnCareerRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class NgnCareerCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class NgnCareerCrudController extends BaseCrudController
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
        CRUD::setModel(\App\Models\NgnCareer::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/ngn-career');
        CRUD::setEntityNameStrings('career', 'careers');
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
        CRUD::setColumns([
            [
                'name' => 'job_title',
                'label' => 'Job Title',
            ],
            [
                'name' => 'description',
                'label' => 'Description',
            ],
            [
                'name' => 'description',
                'label' => 'Description',
                'type' => 'summernote',  // Change from textarea to Summernote
                'options' => [
                    'toolbar' => [
                        ['font', ['bold', 'underline', 'italic', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'picture']],
                    ],
                    'height' => 300, // Optional customization for editor height
                ],
            ],
            [
                'name' => 'location',
                'label' => 'Location',
            ],
            [
                'name' => 'salary',
                'label' => 'Salary',
            ],
            [
                'name' => 'contact_email',
                'label' => 'Contact Email',
            ],
            [
                'name' => 'job_posted',
                'label' => 'Job Posted',
                'type' => 'date',
            ],
            [
                'name' => 'expire_date',
                'label' => 'Expire Date',
                'type' => 'date',
            ],
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
        CRUD::setValidation(NgnCareerRequest::class);

        CRUD::addFields([
            [
                'name' => 'job_title',
                'label' => 'Job Title',
                'type' => 'text',
            ],
            [
                'name' => 'description',
                'label' => 'Description',
                'type' => 'summernote',  // Change from textarea to Summernote
            ],
            [
                'name' => 'employment_type',
                'label' => 'Employment Type',
                'type' => 'text',
            ],
            [
                'name' => 'location',
                'label' => 'Location',
                'type' => 'text',
            ],
            [
                'name' => 'salary',
                'label' => 'Salary',
                'type' => 'text',
                'attributes' => [
                    'step' => '£36,000 to £40,000',
                ],
            ],
            [
                'name' => 'contact_email',
                'label' => 'Contact Email',
                'type' => 'email',
            ],
            [
                'name' => 'job_posted',
                'label' => 'Job Posted',
                'type' => 'date',
            ],
            [
                'name' => 'expire_date',
                'label' => 'Expire Date',
                'type' => 'date',
            ],
        ]);
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
