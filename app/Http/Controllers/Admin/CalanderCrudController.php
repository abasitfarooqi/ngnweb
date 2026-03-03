<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CalanderRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;

class CalanderCrudController extends BaseCrudController
{
    // use \Backpack\CalendarOperation\CalendarOperation; // removed - L12 incompatible
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Calander::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/calander');
        CRUD::setEntityNameStrings('calander', 'calanders');

    }

    protected function setupListOperation()
    {
        CRUD::setFromDb();
    }

    // CalendarOperation methods removed - L12 incompatible

    protected function setupCreateOperation()
    {
        CRUD::setValidation(CalanderRequest::class);

        $start = request()->has('start') ? Carbon::parse(request('start')) : null;
        $end = request()->has('end') ? Carbon::parse(request('end')) : null;

        CRUD::field('start')
            ->type('datetime')
            ->wrapper(['class' => 'form-group col-md-6'])
            ->value($start);

        CRUD::field('end')
            ->type('datetime')
            ->wrapper(['class' => 'form-group col-md-6'])
            ->value($end);

        CRUD::setFromDb();
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
