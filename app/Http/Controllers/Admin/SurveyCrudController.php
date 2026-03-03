<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SurveyRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SurveyCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SurveyCrudController extends BaseCrudController
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
        CRUD::setModel(\App\Models\NgnSurvey::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/survey');
        CRUD::setEntityNameStrings('survey', 'surveys');
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
        CRUD::setValidation(SurveyRequest::class);

        CRUD::field('title')->type('text')->label('Survey Title');
        CRUD::field([   // Text
            'name' => 'slug',
            'target' => 'title', // will turn the title input into a slug
            'label' => 'Slug',
            'type' => 'slug',

            // optional
            'locale' => 'pt', // locale to use, defaults to app()->getLocale()
            'separator' => '', // separator to use
            'trim' => true, // trim whitespace
            'lower' => true, // convert to lowercase
            'strict' => true, // strip special characters except replacement
            'remove' => '/[*+~.()!:@]/g', // remove characters to match regex, defaults to null
        ]);

        CRUD::field('description')->type('summernote')->label('Description');
        CRUD::field('is_active')->type('checkbox')->label('Active');

        // Add a repeatable field for questions
        CRUD::addField([
            'name' => 'questions',
            'label' => 'Questions',
            'type' => 'repeatable',
            'fields' => [
                [
                    'name' => 'question_text',
                    'type' => 'text',
                    'label' => 'Question Text',
                ],
                [
                    'name' => 'question_type',
                    'type' => 'select_from_array',
                    'options' => ['single_choice' => 'Single Choice', 'multiple_choice' => 'Multiple Choice', 'text' => 'Text'],
                    'label' => 'Question Type',
                ],
                [
                    'name' => 'is_required',
                    'type' => 'checkbox',
                    'label' => 'Required',
                ],
                [
                    'name' => 'order',
                    'type' => 'number',
                    'label' => 'Order',
                    'default' => 0, // Set a default value
                ],
                [
                    'name' => 'options',
                    'label' => 'Options',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'option_text',
                            'type' => 'text',
                            'label' => 'Option Text',
                        ],
                    ],
                ],
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
