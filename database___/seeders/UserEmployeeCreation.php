<?php

namespace Database\Seeders;

use App\Models\EmployeeSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserEmployeeCreation extends Seeder
{
    public function run(): void
    {
        $employees = [
            '2404017' => 'Brendon Stevam Viana Albino',
            '2309014' => 'Joao Batista de Lima Neto',
            '2002006' => 'Deivid Lauer Bautz',
            '2203015' => 'Vagner de Jesus Cardoso',
            '2101007' => 'Luiz Fernando da Silva',
            '2404019' => 'Arthur Caetano Da Fonseca',
            '2402016' => 'Muhammad Shariq Ayaz',
            '1811001' => 'Thiago Fauster Martins',
        ];

        $offDays = ['Wednesday', 'Thursday', 'Friday', 'Saturday'];

        foreach ($employees as $employeeId => $employeeName) {
            $email = $employeeId.'@neguinhomotors.co.uk';

            $user = User::firstOrCreate([
                'employee_id' => $employeeId,
            ], [
                'username' => $employeeName,
                'email' => $email,
                'first_name' => $employeeName,
                'password' => '$2y$10$OwX0LjDh3.XL65iirbqFaetdIb/BD7P/aeTr4U06Y8mv095oR.Q8q',
                'is_admin' => true,
                'is_client' => 0,
                'role_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $startDate = Carbon::parse('next monday');
            // $startDate = Carbon::parse('2024-04-23');

            for ($week = 0; $week < 12; $week++) {
                $offDayIndex = ($week + array_search($employeeId, array_keys($employees))) % count($offDays);

                $offDay = $startDate->copy()->addWeeks($week)->modify('next '.$offDays[$offDayIndex]);

                EmployeeSchedule::create([
                    'user_id' => $user->id,
                    'off_day' => $offDay,
                ]);
            }
        }
    }
}
