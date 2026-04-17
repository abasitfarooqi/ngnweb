<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Case Details</h2>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-px bg-zinc-200 dark:bg-zinc-700">
            @php
                $fields = [
                    'PCN Number' => $pcnCase->pcn_number,
                    'Customer' => $pcnCase->customer
                        ? '<a href="' . route('flux-admin.customers.show', $pcnCase->customer_id) . '" class="text-blue-600 dark:text-blue-400 hover:underline">' . e($pcnCase->customer->first_name . ' ' . $pcnCase->customer->last_name) . '</a>'
                        : '—',
                    'Motorbike' => $pcnCase->motorbike
                        ? '<a href="' . route('flux-admin.motorbikes.show', $pcnCase->motorbike_id) . '" class="text-blue-600 dark:text-blue-400 hover:underline">' . e($pcnCase->motorbike->reg_no) . '</a>'
                        : '—',
                    'Date of Contravention' => $pcnCase->date_of_contravention?->format('d M Y') ?? '—',
                    'Time of Contravention' => $pcnCase->time_of_contravention ?? '—',
                    'Council Link' => $pcnCase->council_link
                        ? '<a href="' . e($pcnCase->council_link) . '" target="_blank" rel="noopener" class="text-blue-600 dark:text-blue-400 hover:underline">View ↗</a>'
                        : '—',
                    'Full Amount' => '£' . number_format($pcnCase->full_amount ?? 0, 2),
                    'Reduced Amount' => '£' . number_format($pcnCase->reduced_amount ?? 0, 2),
                    'Police?' => $pcnCase->is_police ? 'Yes' : 'No',
                    'Note' => $pcnCase->note ?? '—',
                    'Picture URL' => $pcnCase->picture_url
                        ? '<a href="' . e($pcnCase->picture_url) . '" target="_blank" rel="noopener" class="text-blue-600 dark:text-blue-400 hover:underline">View ↗</a>'
                        : '—',
                    'Date of Letter Issued' => $pcnCase->date_of_letter_issued ?? '—',
                    'Assigned User' => $pcnCase->user?->first_name . ' ' . ($pcnCase->user?->last_name ?? '') ?: '—',
                    'Status' => $pcnCase->isClosed ? 'Closed' : 'Open',
                ];
            @endphp

            @foreach($fields as $label => $value)
                <div class="bg-white dark:bg-zinc-900 px-5 py-4">
                    <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ $label }}</dt>
                    <dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{!! $value ?: '—' !!}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</div>
