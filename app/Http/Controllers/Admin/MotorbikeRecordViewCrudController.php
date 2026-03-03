<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class MotorbikeRecordViewCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\MotorbikeRecordView::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/motorbike-record-view');
        CRUD::setEntityNameStrings('motorbike record view', 'motorbike record views');
        $this->crud->denyAccess(['create', 'update', 'delete']);
    }

    protected function setupListOperation()
    {
        CRUD::enableExportButtons();

        // Filter 1: Sort by Start Date (Recent First / Oldest First)
        $this->crud->addFilter([
            'name' => 'sort_start_date',
            'type' => 'dropdown',
            'label' => 'Sort by Start Date',
        ], [
            'recent' => 'Recent First',
            'oldest' => 'Oldest First',
        ], function ($value) {
            if ($value == 'recent') {
                $this->crud->orderBy('START_DATE', 'desc');
            } elseif ($value == 'oldest') {
                $this->crud->orderBy('START_DATE', 'asc');
            }
        });

        // Filter 2: Sort by End Date (Recent First / Oldest First)
        $this->crud->addFilter([
            'name' => 'sort_end_date',
            'type' => 'dropdown',
            'label' => 'Sort by End Date',
        ], [
            'recent' => 'Recent First',
            'oldest' => 'Oldest First',
        ], function ($value) {
            if ($value == 'recent') {
                $this->crud->orderBy('END_DATE', 'desc');
            } elseif ($value == 'oldest') {
                $this->crud->orderBy('END_DATE', 'asc');
            }
        });

        // Add filter by Start Date
        $this->crud->addFilter([
            'type' => 'date_range',
            'name' => 'START_DATE',
            'label' => 'Start Date Range',
        ],
            false,
            function ($value) {
                $dates = json_decode($value);
                if (isset($dates->from) && isset($dates->to)) {
                    $this->crud->addClause('where', 'START_DATE', '>=', $dates->from);
                    $this->crud->addClause('where', 'START_DATE', '<=', $dates->to);
                }
            });

        CRUD::addColumn(['name' => 'MID', 'label' => 'MID']);
        CRUD::addColumn(['name' => 'VRM', 'label' => 'VRM']);
        CRUD::addColumn(['name' => 'DATABASE', 'label' => 'Database']);
        CRUD::addColumn(['name' => 'DOC_ID', 'label' => 'Document ID']);
        CRUD::addColumn(['name' => 'PERSON', 'label' => 'Person']);
        CRUD::addColumn(['name' => 'START_DATE', 'label' => 'Start Date', 'type' => 'date']);
        CRUD::addColumn(['name' => 'END_DATE', 'label' => 'End Date', 'type' => 'date']);
    }
}
