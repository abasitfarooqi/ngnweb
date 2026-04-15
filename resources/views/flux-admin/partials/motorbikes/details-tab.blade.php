<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Vehicle Details</h2>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-px bg-zinc-200 dark:bg-zinc-700">
            @php
                $fields = [
                    'VIN Number' => $motorbike->vin_number,
                    'Reg No' => $motorbike->reg_no,
                    'Make' => $motorbike->make,
                    'Model' => $motorbike->model,
                    'Year' => $motorbike->year,
                    'Engine' => $motorbike->engine,
                    'CO2 Emissions' => $motorbike->co2_emissions,
                    'Fuel Type' => $motorbike->fuel_type,
                    'Colour' => $motorbike->color,
                    'Type Approval' => $motorbike->type_approval,
                    'Wheel Plan' => $motorbike->wheel_plan,
                    'First Registration' => $motorbike->month_of_first_registration,
                    'Branch' => $motorbike->branch?->name ?? '—',
                    'Vehicle Profile' => $motorbike->vehicleProfile?->name ?? '—',
                    'Is E-Bike' => $motorbike->is_ebike ? 'Yes' : 'No',
                ];
            @endphp

            @foreach($fields as $label => $value)
                <div class="bg-white dark:bg-zinc-800 px-5 py-4">
                    <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ $label }}</dt>
                    <dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $value ?: '—' }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</div>
