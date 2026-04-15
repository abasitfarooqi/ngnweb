<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Flux Admin Routes
|--------------------------------------------------------------------------
|
| New admin panel built with Livewire 4 + Flux Pro + Tailwind.
| Middleware: web, auth, admin, check.admin.access (same as Backpack).
| Prefix: flux-admin
|
*/

// Dashboard
Route::get('/', fn () => redirect()->route('flux-admin.dashboard'));
Route::get('/dashboard', \App\Livewire\FluxAdmin\Pages\Dashboard::class)
    ->name('flux-admin.dashboard');

// Motorbikes
Route::prefix('motorbikes')->name('flux-admin.motorbikes.')->group(function () {
    Route::get('/', \App\Livewire\FluxAdmin\Pages\Motorbikes\MotorbikeIndex::class)->name('index');
    Route::get('/{motorbike}', \App\Livewire\FluxAdmin\Pages\Motorbikes\MotorbikeShow::class)->name('show');
});

// Customers
Route::prefix('customers')->name('flux-admin.customers.')->group(function () {
    Route::get('/', \App\Livewire\FluxAdmin\Pages\Customers\CustomerIndex::class)->name('index');
    Route::get('/{customer}', \App\Livewire\FluxAdmin\Pages\Customers\CustomerShow::class)->name('show');
});

// Rentals
Route::prefix('rentals')->name('flux-admin.rentals.')->group(function () {
    Route::get('/', \App\Livewire\FluxAdmin\Pages\Rentals\RentalIndex::class)->name('index');
    Route::get('/{booking}', \App\Livewire\FluxAdmin\Pages\Rentals\RentalShow::class)->name('show');
});

// Finance
Route::prefix('finance')->name('flux-admin.finance.')->group(function () {
    Route::get('/', \App\Livewire\FluxAdmin\Pages\Finance\FinanceIndex::class)->name('index');
    Route::get('/{application}', \App\Livewire\FluxAdmin\Pages\Finance\FinanceShow::class)->name('show');
});

// PCN
Route::prefix('pcn')->name('flux-admin.pcn.')->group(function () {
    Route::get('/', \App\Livewire\FluxAdmin\Pages\Pcn\PcnIndex::class)->name('index');
    Route::get('/{pcnCase}', \App\Livewire\FluxAdmin\Pages\Pcn\PcnShow::class)->name('show');
});

// Club Members
Route::prefix('club')->name('flux-admin.club.')->group(function () {
    Route::get('/', \App\Livewire\FluxAdmin\Pages\Club\ClubIndex::class)->name('index');
    Route::get('/{clubMember}', \App\Livewire\FluxAdmin\Pages\Club\ClubShow::class)->name('show');
});

// Branches
Route::prefix('branches')->name('flux-admin.branches.')->group(function () {
    Route::get('/', \App\Livewire\FluxAdmin\Pages\Branches\BranchIndex::class)->name('index');
    Route::get('/{branch}', \App\Livewire\FluxAdmin\Pages\Branches\BranchShow::class)->name('show');
});
