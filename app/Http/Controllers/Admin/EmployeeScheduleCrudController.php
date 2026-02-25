<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class EmployeeScheduleCrudController extends CrudController
{
    // use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CalendarOperation\CalendarOperation;

    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\EmployeeSchedule::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/employee-schedule');
        CRUD::setEntityNameStrings('employee schedule', 'employee schedules');
        CRUD::enableExportButtons();
        CRUD::setTitle('Manage Employee Schedules');
        CRUD::denyAccess('create');

    }

    public function setupCalendarOperation()
    {
        CRUD::setOperationSetting('initial-view', 'dayGridMonth');
        CRUD::setOperationSetting('firstDay', 2);
        CRUD::setOperationSetting('views', ['dayGridMonth', 'timeGridWeek', 'timeGridDay']);
        CRUD::setOperationSetting('editable', true);
        CRUD::setOperationSetting('background_color', fn ($event) => $event->active ? 'green' : 'red');
        CRUD::setOperationSetting('text_color', fn ($event) => $event->active ? 'white' : 'black');
        CRUD::setOperationSetting('with-javascript-widget', true);
        CRUD::setOperationSetting('javascript-configuration', [
            'dayMaxEvents' => false,
        ]);
    }

    protected function getCalendarFieldsMap()
    {
        return [
            'title' => 'title',
            'start' => 'start',
            'end' => 'end',
            'background_color' => 'background_color',
            'text_color' => 'text_color',
            'all_day' => 'all_day',
        ];
    }

    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name' => 'user_id',
            'label' => 'USER ID',
            'type' => 'select',
            'entity' => 'user',
            'attribute' => 'id',
            'model' => "App\Models\User",
        ]);

        CRUD::addColumn([
            'name' => 'user.employee_id',
            'label' => 'Employee ID',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'user.first_name',
            'label' => 'First Name',
            'type' => 'text',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('user', function ($query) use ($searchTerm) {
                    $query->where('first_name', 'like', '%'.$searchTerm.'%');
                });
            },
        ]);

        CRUD::addColumn([
            'name' => 'off_day_d_mmm_yyyy',
            'label' => 'DATE OFF',
            'type' => 'date',
            'orderable' => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->orderBy('off_day', $columnDirection);
            },
        ]);

        CRUD::addColumn([
            'name' => 'day_of_week',
            'label' => 'Day of Week',
            'type' => 'text',
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
