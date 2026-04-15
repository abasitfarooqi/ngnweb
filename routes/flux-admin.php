<?php

use App\Livewire\FluxAdmin\Pages\Branches\BranchIndex;
use App\Livewire\FluxAdmin\Pages\Branches\BranchShow;
use App\Livewire\FluxAdmin\Pages\Club\ClubIndex;
use App\Livewire\FluxAdmin\Pages\Club\ClubShow;
use App\Livewire\FluxAdmin\Pages\Customers\CustomerIndex;
use App\Livewire\FluxAdmin\Pages\Customers\CustomerShow;
use App\Livewire\FluxAdmin\Pages\Dashboard;
use App\Livewire\FluxAdmin\Pages\Finance\FinanceIndex;
use App\Livewire\FluxAdmin\Pages\Finance\FinanceShow;
use App\Livewire\FluxAdmin\Pages\Motorbikes\MotorbikeIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\MotorbikeShow;
use App\Livewire\FluxAdmin\Pages\Pcn\PcnIndex;
use App\Livewire\FluxAdmin\Pages\Pcn\PcnShow;
use App\Livewire\FluxAdmin\Pages\Rentals\RentalIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\RentalShow;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('flux-admin.dashboard');

Route::get('/motorbikes', MotorbikeIndex::class)->name('flux-admin.motorbikes.index');
Route::get('/motorbikes/{motorbike}', MotorbikeShow::class)->name('flux-admin.motorbikes.show');

Route::get('/customers', CustomerIndex::class)->name('flux-admin.customers.index');
Route::get('/customers/{customer}', CustomerShow::class)->name('flux-admin.customers.show');

Route::get('/rentals', RentalIndex::class)->name('flux-admin.rentals.index');
Route::get('/rentals/{booking}', RentalShow::class)->name('flux-admin.rentals.show');

Route::get('/finance', FinanceIndex::class)->name('flux-admin.finance.index');
Route::get('/finance/{application}', FinanceShow::class)->name('flux-admin.finance.show');

Route::get('/pcn', PcnIndex::class)->name('flux-admin.pcn.index');
Route::get('/pcn/{pcnCase}', PcnShow::class)->name('flux-admin.pcn.show');

Route::get('/club', ClubIndex::class)->name('flux-admin.club.index');
Route::get('/club/{clubMember}', ClubShow::class)->name('flux-admin.club.show');

Route::get('/branches', BranchIndex::class)->name('flux-admin.branches.index');
Route::get('/branches/{branch}', BranchShow::class)->name('flux-admin.branches.show');
