<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Motorbike;
use App\Models\User;

class FinanceController extends Controller
{
    public function finance_dashboard()
    {
        return view('livewire.agreements.legacy-host', ['legacyView' => 'livewire.agreements.migrated.admin.finance.dashboard']);
    }

    public function finance_applications()
    {
        return view('livewire.agreements.legacy-host', ['legacyView' => 'livewire.agreements.migrated.admin.finance.applications']);
    }

    public function finance_application_new()
    {
        $customers = Customer::all();
        $users = User::all();
        $motorbikes = Motorbike::all();

        return view(('admin.finance.application-new'), [
            'motorbikes' => $motorbikes,
            'customers' => Customer::all(),
        ]);
    }
}
