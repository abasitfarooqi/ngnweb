<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SurveyResponseRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SurveyResponseCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SurveyResponseCrudController extends CrudController
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
        CRUD::setModel(\App\Models\NgnSurveyResponse::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/survey-response');
        CRUD::setEntityNameStrings('survey response', 'survey responses');
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
        CRUD::setValidation(SurveyResponseRequest::class);

        CRUD::field('survey_id')
            ->label('Survey')
            ->type('select')
            ->entity('survey')
            ->model('App\Models\NgnSurvey')
            ->attribute('title')
            ->attributes(['placeholder' => 'Select the survey this response belongs to']);

        CRUD::field('customer_id')
            ->label('Customer')
            ->type('select')
            ->entity('customer')
            ->model('App\Models\Customer')
            ->attribute('full_name')
            ->attributes(['placeholder' => 'Select the customer who responded']);

        CRUD::field('club_member_id')
            ->label('Club Member')
            ->type('select')
            ->entity('club_member')
            ->model('App\Models\ClubMember')
            ->attribute('full_name')
            ->attributes(['placeholder' => 'Select the club member who responded']);

        CRUD::field('contact_name')
            ->label('Contact Name')
            ->type('text')
            ->attributes(['placeholder' => 'Enter the contact name']);

        CRUD::field('contact_email')
            ->label('Contact Email')
            ->type('email')
            ->attributes(['placeholder' => 'Enter the contact email']);

        CRUD::field('contact_phone')
            ->label('Contact Phone')
            ->type('text')
            ->attributes(['placeholder' => 'Enter the contact phone number']);

        CRUD::field('is_contact_opt_in')
            ->label('Contact Opt-In')
            ->type('checkbox');
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
