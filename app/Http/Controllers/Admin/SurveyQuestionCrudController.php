<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SurveyQuestionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SurveyQuestionCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SurveyQuestionCrudController extends CrudController
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
        CRUD::setModel(\App\Models\NgnSurveyQuestion::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/survey-question');
        CRUD::setEntityNameStrings('survey question', 'survey questions');
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

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
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
        CRUD::setValidation(SurveyQuestionRequest::class);

        CRUD::field('survey_id')
            ->label('Survey')
            ->type('select')
            ->entity('survey')
            ->model('App\Models\NgnSurvey')
            ->attribute('title')
            ->attributes(['placeholder' => 'Select the survey this question belongs to']);

        CRUD::field('question_text')
            ->label('Question Text')
            ->type('textarea')
            ->attributes(['placeholder' => 'Enter the question text']);

        CRUD::field('question_type')
            ->label('Question Type')
            ->type('enum')
            ->attributes(['placeholder' => 'Select the type of question']);

        CRUD::field('is_required')
            ->label('Required')
            ->type('checkbox');

        CRUD::field('order')
            ->label('Order')
            ->type('number')
            ->attributes(['placeholder' => 'Enter the order of the question']);
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
