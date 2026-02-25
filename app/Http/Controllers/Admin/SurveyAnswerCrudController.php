<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SurveyAnswerRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SurveyAnswerCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SurveyAnswerCrudController extends CrudController
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
        CRUD::setModel(\App\Models\NgnSurveyAnswer::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/survey-answer');
        CRUD::setEntityNameStrings('survey answer', 'survey answers');
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
        CRUD::setValidation(SurveyAnswerRequest::class);

        CRUD::field('response_id')
            ->label('Response')
            ->type('select')
            ->entity('response')
            ->model('App\Models\NgnSurveyResponse')
            ->attribute('id')
            ->attributes(['placeholder' => 'Select the response this answer belongs to']);

        CRUD::field('question_id')
            ->label('Question')
            ->type('select')
            ->entity('question')
            ->model('App\Models\NgnSurveyQuestion')
            ->attribute('question_text')
            ->attributes(['placeholder' => 'Select the question this answer is for']);

        CRUD::field('option_id')
            ->label('Option')
            ->type('select')
            ->entity('option')
            ->model('App\Models\NgnSurveyOption')
            ->attribute('option_text')
            ->attributes(['placeholder' => 'Select the option chosen for this answer']);

        CRUD::field('answer_text')
            ->label('Answer Text')
            ->type('textarea')
            ->attributes(['placeholder' => 'Enter the answer text if applicable']);
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
