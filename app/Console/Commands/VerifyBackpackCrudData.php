<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyBackpackCrudData extends Command
{
    protected $signature = 'backpack:verify-crud-data
                            {--segment= : Single CRUD segment to check (e.g. customer)}
                            {--limit=10 : Max CRUDs to check (default 10)}';

    protected $description = 'Verify Backpack CRUD list/search data: DB row counts and search endpoint response.';

    /** @var string[] segment => controller class */
    protected $crudMap = [
        'user' => \App\Http\Controllers\Admin\UserCrudController::class,
        'role' => \App\Http\Controllers\Admin\RoleCrudController::class,
        'permission' => \App\Http\Controllers\Admin\PermissionCrudController::class,
        'customer' => \App\Http\Controllers\Admin\CustomerCrudController::class,
        'motorbikes' => \App\Http\Controllers\Admin\MotorbikesCrudController::class,
        'branch' => \App\Http\Controllers\Admin\BranchCrudController::class,
        'finance-application' => \App\Http\Controllers\Admin\FinanceApplicationCrudController::class,
        'mot-booking' => \App\Http\Controllers\Admin\MOTBookingCrudController::class,
        'customer-appointments' => \App\Http\Controllers\Admin\CustomerAppointmentsCrudController::class,
        'motorbikes-sale' => \App\Http\Controllers\Admin\MotorbikesSaleCrudController::class,
        'pcn-case' => \App\Http\Controllers\Admin\PcnCaseCrudController::class,
        'contact-query' => \App\Http\Controllers\Admin\ContactQueryCrudController::class,
        'blog-post' => \App\Http\Controllers\Admin\BlogPostCrudController::class,
        'ec-order' => \App\Http\Controllers\Admin\EcOrderCrudController::class,
        'ngn-career' => \App\Http\Controllers\Admin\NgnCareerCrudController::class,
    ];

    /** @var string[] segment => Eloquent model class for DB count (avoids GET request that caches panel) */
    protected $segmentModelMap = [
        'user' => null, // from config
        'role' => null,
        'permission' => null,
        'customer' => \App\Models\Customer::class,
        'motorbikes' => \App\Models\Motorbike::class,
        'branch' => \App\Models\Branch::class,
        'finance-application' => \App\Models\FinanceApplication::class,
        'mot-booking' => \App\Models\MOTBooking::class,
        'customer-appointments' => \App\Models\CustomerAppointments::class,
        'motorbikes-sale' => \App\Models\MotorbikesSale::class,
        'pcn-case' => \App\Models\PcnCase::class,
        'contact-query' => \App\Models\ContactQuery::class,
        'blog-post' => \App\Models\BlogPost::class,
        'ec-order' => \App\Models\Ecommerce\EcOrder::class,
        'ngn-career' => \App\Models\NgnCareer::class,
    ];

    public function handle(): int
    {
        $prefix = config('backpack.base.route_prefix', 'ngn-admin');
        $segmentFilter = $this->option('segment');
        $limit = (int) $this->option('limit');

        $segments = $segmentFilter
            ? (isset($this->crudMap[$segmentFilter]) ? [$segmentFilter => $this->crudMap[$segmentFilter]] : [])
            : array_slice($this->crudMap, 0, $limit, true);

        if (empty($segments)) {
            $this->error('No CRUDs to check. Use --segment=customer or ensure crudMap has entries.');
            return 1;
        }

        $user = $this->getAdminUser();
        if (! $user) {
            $this->error('No admin user found. Create a user that can log in to Backpack.');
            return 1;
        }

        Auth::guard('web')->login($user);
        $this->info('Logged in as: ' . ($user->email ?? $user->name ?? $user->id));
        $this->newLine();

        $rows = [];
        foreach ($segments as $segment => $controllerClass) {
            $result = $this->verifyOne($prefix, $segment, $controllerClass);
            $rows[] = $result;
        }

        $this->table(
            ['Segment', 'Model/Table', 'DB count', 'Search HTTP', 'Search records', 'Issue'],
            array_map(fn ($r) => [
                $r['segment'],
                $r['table'],
                $r['db_count'],
                $r['search_status'],
                $r['search_records'] ?? '—',
                $r['issue'] ?? '',
            ], $rows)
        );

        $blank = array_filter($rows, fn ($r) => ($r['search_records'] ?? 0) === 0 && is_int($r['db_count'] ?? null) && $r['db_count'] > 0);
        if (! empty($blank)) {
            $this->newLine();
            $this->warn('CRUDs with DB data but empty search response (likely frontend/JS or search route):');
            foreach ($blank as $r) {
                $this->line('  - ' . $r['segment'] . ' (DB: ' . $r['db_count'] . ')');
            }
        }

        return 0;
    }

    protected function getAdminUser()
    {
        $guard = config('backpack.base.guard', 'web');
        $provider = config("auth.guards.{$guard}.provider");
        $model = config("auth.providers.{$provider}.model", \App\Models\User::class);

        return $model::first();
    }

    protected function verifyOne(string $prefix, string $segment, string $controllerClass): array
    {
        $searchUrl = '/' . trim($prefix, '/') . '/' . $segment . '/search';
        $dbCount = null;
        $out = [
            'segment' => $segment,
            'table' => '—',
            'db_count' => '—',
            'search_status' => '—',
            'search_records' => null,
            'issue' => '',
        ];

        $modelClass = $this->segmentModelMap[$segment] ?? null;
        if ($modelClass === null && in_array($segment, ['user', 'role', 'permission'], true)) {
            $modelClass = $segment === 'user'
                ? config('backpack.permissionmanager.models.user', \App\Models\User::class)
                : config('backpack.permissionmanager.models.' . $segment, $segment === 'role' ? \Spatie\Permission\Models\Role::class : \Spatie\Permission\Models\Permission::class);
        }
        if ($modelClass && class_exists($modelClass)) {
            try {
                $model = new $modelClass;
                $out['table'] = $model->getTable();
                $dbCount = $model->count();
                $out['db_count'] = $dbCount;
            } catch (\Throwable $e) {
                $out['issue'] = 'DB: ' . $e->getMessage();
            }
        } else {
            $out['db_count'] = '?';
        }

        $this->clearCrudPanelForController($controllerClass);

        try {
            $session = app('session.store');
            $session->start();
            $csrf = $session->token();

            $request = Request::create($searchUrl, 'POST', [
                'draw' => 1,
                'start' => 0,
                'length' => 10,
                'search' => ['value' => '', 'regex' => 'false'],
                'totalEntryCount' => $dbCount,
                '_token' => $csrf,
            ]);
            $request->headers->set('Accept', 'application/json');
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');
            $request->headers->set('X-CSRF-TOKEN', $csrf);
            $request->setLaravelSession($session);

            $response = app()->handle($request);
            $status = $response->getStatusCode();
            $out['search_status'] = $status;

            if ($status === 200) {
                $body = json_decode($response->getContent(), true);
                $records = $body['data'] ?? [];
                $out['search_records'] = is_array($records) ? count($records) : 0;
                if ($out['search_records'] === 0 && (is_int($out['db_count']) && $out['db_count'] > 0)) {
                    $out['issue'] = 'Search returns 0 rows; check columns/query or JS';
                }
            } else {
                $out['issue'] = 'HTTP ' . $status . ': ' . substr(strip_tags($response->getContent() ?: ''), 0, 120);
            }
        } catch (\Throwable $e) {
            $out['search_status'] = 'err';
            $out['issue'] = $e->getMessage();
        }

        return $out;
    }

    protected function clearCrudPanelForController(string $controllerClass): void
    {
        $manager = app('CrudManager');
        $ref = new \ReflectionClass($manager);
        $prop = $ref->getProperty('cruds');
        $prop->setAccessible(true);
        $prop->setValue($manager, []);
        $initProp = $ref->getProperty('initializedOperations');
        $initProp->setAccessible(true);
        $initProp->setValue($manager, []);
        if (method_exists($manager, 'unsetActiveController')) {
            $manager->unsetActiveController();
        }
    }
}
