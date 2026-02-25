<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MotorbikeListCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel("App\Models\Motorbike");
        CRUD::setRoute(config('backpack.base.route_prefix').'/motorbike-list');
        CRUD::setEntityNameStrings('motorbike list', 'motorbike lists');

        $this->setupListOperation();
    }

    protected function setupColumns()
    {
        CRUD::addColumn([
            'name' => 'MID',
            'label' => 'ID',
            'type' => 'number',
        ]);

        CRUD::addColumn([
            'name' => 'REG_NO',
            'label' => 'Registration Number',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'MAKE',
            'label' => 'Make',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'MODEL',
            'label' => 'Model',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'YEAR',
            'label' => 'Year',
            'type' => 'year',
        ]);

        CRUD::addColumn([
            'name' => 'ENGINE',
            'label' => 'Engine',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'COLOR',
            'label' => 'Color',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'STATUS',
            'label' => 'Status',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'BRANCH',
            'label' => 'Branch',
            'type' => 'text',
        ]);
    }

    protected function setupListOperation()
    {
        $this->setupColumns();
        $this->crud->setModel(\App\Models\Motorbike::class);
        $this->crud->query = \App\Models\Motorbike::select('*')->from(DB::raw("({$this->getRawUnionQuery()}) as motorbikes"));

        $searchTerm = is_array(request()->input('search')) ? head(request()->input('search')) : request()->input('search');
        if (! empty($searchTerm)) {
            $this->crud->addClause('where', function ($query) use ($searchTerm) {
                $query->where('REG_NO', 'like', "%{$searchTerm}%")
                    ->orWhere('MAKE', 'like', "%{$searchTerm}%")
                    ->orWhere('MODEL', 'like', "%{$searchTerm}%")
                    ->orWhere('YEAR', 'like', "%{$searchTerm}%")
                    ->orWhere('ENGINE', 'like', "%{$searchTerm}%")
                    ->orWhere('COLOR', 'like', "%{$searchTerm}%")
                    ->orWhere('STATUS', 'like', "%{$searchTerm}%")
                    ->orWhere('BRANCH', 'like', "%{$searchTerm}%");
            });
        }

        CRUD::enableExportButtons();
    }

    public function getCustomQuery()
    {
        $query = DB::table(DB::raw("({$this->getRawUnionQuery()}) as motorbikes"))
            ->select('*');

        return $query;
    }

    public function getEntries(): Collection
    {
        $entries = $this->getCustomData();

        return collect($entries);
    }

    private function getRawUnionQuery()
    {
        return "
           SELECT DISTINCT REG_NO, MID, id, MAKE, MODEL, YEAR, ENGINE, COLOR, STATUS, BRANCH
            FROM (
                SELECT DISTINCT(m.id) AS MID, m.id id, m.reg_no AS REG_NO, m.make AS MAKE, m.model AS MODEL, m.year AS YEAR,
                    m.engine AS ENGINE, m.color AS COLOR, 'AVAILABLE' AS STATUS, '' AS BRANCH
                FROM motorbikes m
                INNER JOIN renting_pricings rp ON rp.motorbike_id = m.id
                WHERE m.id NOT IN (SELECT motorbike_id FROM renting_booking_items WHERE end_date IS NULL)
                UNION ALL
                SELECT DISTINCT(m.id) AS MID, m.id id, m.reg_no AS REG_NO, m.make AS MAKE, m.model AS MODEL, m.year AS YEAR,
                    m.engine AS ENGINE, m.color AS COLOR, 'AVAILABLE' AS STATUS, '' AS BRANCH
                FROM motorbikes m
                INNER JOIN motorbikes_sale ms ON ms.motorbike_id = m.id AND ms.is_sold = false
                UNION ALL
                SELECT DISTINCT(m.id) AS MID, m.id id, m.reg_no AS REG_NO, m.make AS MAKE, m.model AS MODEL, m.year AS YEAR,
                    m.engine AS ENGINE, m.color AS COLOR, 'AVAILABLE FOR REPAIR' AS STATUS, (SELECT name FROM branches WHERE id=mr.branch_id) AS BRANCH
                FROM motorbikes m
                INNER JOIN motorbikes_repair mr ON mr.motorbike_id = m.id AND mr.is_returned = false
                UNION ALL
                SELECT DISTINCT(m.id) AS MID, m.id id, m.reg_no AS REG_NO, m.make AS MAKE, m.model AS MODEL, m.year AS YEAR,
                    m.engine AS ENGINE, m.color AS COLOR, 'CATEGORY B' AS STATUS, (SELECT name FROM branches WHERE id=mcb.branch_id) AS BRANCH
                FROM motorbikes m
                INNER JOIN motorbikes_cat_b mcb ON mcb.motorbike_id = m.id
                UNION ALL
                SELECT DISTINCT(m.id) AS MID, m.id id, m.reg_no AS REG_NO, m.make AS MAKE, m.model AS MODEL, m.year AS YEAR,
                    m.engine AS ENGINE, m.color AS COLOR, 'CLAIM' AS STATUS, (SELECT name FROM branches WHERE id=cm.branch_id) AS BRANCH
                FROM motorbikes m
                INNER JOIN claim_motorbikes cm ON cm.motorbike_id = m.id AND cm.is_returned = false
            ) AS combined
        ";
    }
}
