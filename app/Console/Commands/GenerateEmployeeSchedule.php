<?php

namespace App\Console\Commands;

use App\Models\EmployeeSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateEmployeeSchedule extends Command
{
    protected $signature = 'app:generate-employee-schedule';

    protected $description = 'Generates employee schedules sequentially based on their last off day.';

    public function handle()
    {
        $baseOffDays = ['Wednesday', 'Thursday', 'Friday', 'Saturday'];

        // WHENEVER I FOUND SATURDAY I SKIP NEXT WEEK NEED TO FIX //
        $employees = DB::table('employee_schedules as es')
            ->join('users as u', 'u.id', '=', 'es.user_id')
            ->select('u.id', 'u.first_name', DB::raw('MAX(es.off_day) as last_off_day'))
            ->groupBy('u.id', 'u.first_name')
            ->get();

        \Log::info('Starting to generate new off-day schedules...');
        \Log::info('Employees: '.json_encode($employees));

        foreach ($employees as $employee) {
            $lastOffDay = Carbon::parse($employee->last_off_day);
            $dayOfWeekIndex = array_search($lastOffDay->format('l'), $baseOffDays);
            $nextOffDay = $lastOffDay->copy();

            // WHENEVER I FOUND SATURDAY I SKIP NEXT WEEK NEED TO FIX //
            \Log::info("Scheduling new off days for employee {$employee->id} ({$employee->first_name}):");

            for ($i = 1; $i <= 4; $i++) {
                $nextDayIndex = ($dayOfWeekIndex + $i) % count($baseOffDays);
                $nextOffDayName = $baseOffDays[$nextDayIndex];

                if ($lastOffDay->format('l') === 'Saturday' && $nextOffDayName === 'Wednesday') {
                    $nextOffDay = $lastOffDay->copy()->addWeek()->next($nextOffDayName);
                } else {
                    $nextOffDay = $nextOffDay->next($nextOffDayName);
                }

                if ($nextOffDay->diffInDays($lastOffDay) < 7) {
                    $nextOffDay->addWeek();
                }

                if ($nextOffDay->diffInDays($lastOffDay) < 7) {
                    $nextOffDay->addWeek();
                }

                // WHENEVER I FOUND SATURDAY I SKIP NEXT WEEK NEED TO FIX //

                EmployeeSchedule::create([
                    'user_id' => $employee->id,
                    'off_day' => $nextOffDay->format('Y-m-d'),
                ]);

                \Log::info(" - Week {$i}: Off day scheduled for ".$nextOffDay->toDateString());

                $lastOffDay = $nextOffDay->copy();
            }
        }

        \Log::info('Finished generating off-day schedules.');
        // WHENEVER I FOUND SATURDAY I SKIP NEXT WEEK NEED TO FIX //

    }
}
