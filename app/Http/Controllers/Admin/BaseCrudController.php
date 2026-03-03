<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\CrudManager;

/**
 * Base CRUD controller for NGN Backpack.
 * Ensures the current operation is set from the route before parent setup runs,
 * so list operation config (e.g. pageLengthMenu) is loaded and search() does not 500.
 */
abstract class BaseCrudController extends CrudController
{
    public function initializeCrudPanel($request, $crudPanel = null): void
    {
        CrudManager::setActiveController(get_class($this));

        $crudPanel ??= CrudManager::getCrudPanel($this);
        $crudPanel = $crudPanel->initialize(get_class($this), $request);

        $operation = $crudPanel->getCurrentOperation();
        if (empty($operation) && $request->route()) {
            $operation = $request->route()->getAction('operation');
        }
        if (empty($operation) && $request->isMethod('POST') && str_ends_with($request->path(), '/search')) {
            $operation = 'list';
        }
        if (! empty($operation)) {
            $crudPanel->setOperation($operation);
            if ($operation === 'list') {
                $listConfig = config('backpack.operations.list', []);
                $defaultMenu = [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'backpack::crud.all']];
                $crudPanel->setOperationSetting('pageLengthMenu', $listConfig['pageLengthMenu'] ?? $defaultMenu, 'list');
            }
        }

        parent::initializeCrudPanel($request, $crudPanel);

        if (! empty($operation) && $operation === 'list') {
            $panel = CrudManager::getCrudPanel($this);
            $panel->setOperation('list');
            $listConfig = config('backpack.operations.list', []);
            $defaultMenu = [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'backpack::crud.all']];
            $menu = $listConfig['pageLengthMenu'] ?? $defaultMenu;
            $panel->setOperationSetting('pageLengthMenu', $menu, 'list');
            $panel->set('list.pageLengthMenu', $menu);
        }
    }
}
