<?php

namespace App\Http\Controllers;

// EmployeeSchedule
use App\Models\EmployeeSchedule;
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    // /rotas-view
    public function rotas()
    {
        // EMPLOYEE_SCHEDULE MODEL

        // get day of each date as employee schedule contain off_day which is date

        // $employeeSchedules = EmployeeSchedule::orderBy('off_day')
        //     ->join('users', 'employee_schedules.user_id', '=', 'users.id')
        //     ->get();

        $employeeSchedules = EmployeeSchedule::orderBy('off_day')
            ->join('users', 'employee_schedules.user_id', '=', 'users.id')
            ->get()
            ->map(function ($schedule) {
                $schedule->day_of_week = Carbon::createFromFormat('d/m/Y', $schedule->off_day)->format('l');

                return $schedule;
            });

        return view('admin.rotas-view', ['employeeSchedules' => $employeeSchedules]);

        // return view('admin.rotas-view', compact('employeeSchedules'));
    }
}
