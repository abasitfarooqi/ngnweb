@php
    $sections = [
        'Frame & build' => [
            'Frame' => 'Steel',
            'Design' => 'Step-through / commuter style',
            'Front suspension' => 'Hydraulic',
            'Rear suspension' => 'Shock absorbers',
            'Tyres' => '16 × 3.0 tubeless',
            'Tyre style' => 'Puncture-resistant tubeless',
            'Rear rack' => 'Yes',
            'Rear storage box' => '62L',
        ],
        'Motor & performance' => [
            'Motor power' => '250W',
            'Voltage system' => '48V',
            'Top speed' => '15.5 mph',
            'Throttle' => 'Yes (twist throttle)',
            'Pedal assist' => 'Yes',
            'Controller' => 'Smart controller',
        ],
        'Battery & range' => [
            'Battery type' => '48V 40.5Ah lithium battery',
            'Estimated range' => '70–80 miles per charge',
            'Battery life expectancy' => 'Up to 5 years',
            'Charger' => '8A smart charger',
        ],
        'Braking system' => [
            'Front brake' => 'Disc brake',
            'Rear brake' => 'Drum brake shoes',
        ],
        'Electronics & display' => [
            'Display' => 'MPH LCD screen (speed, battery level, trip info, assist mode)',
            'Lights' => 'Front and rear LED lights',
            'Alarm system' => 'Yes',
        ],
        'Additional features & info' => [
            'Water resistance' => 'IPX4 (suitable for light rain)',
            'Maximum load' => '120 kg',
            'Overall riding style' => 'Designed for everyday commuting, deliveries, and urban use',
        ],
    ];
@endphp

<div class="overflow-hidden border border-gray-200 bg-white shadow-sm dark:border-gray-600 dark:bg-gray-900 dark:shadow-none">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <tbody>
                @foreach ($sections as $title => $rows)
                    <tr class="bg-gray-100 dark:bg-gray-800">
                        <th class="border-b border-gray-200 px-4 py-3 text-xs font-bold uppercase tracking-wide text-gray-900 dark:border-gray-600 dark:text-gray-100" colspan="2">
                            <span class="border-l-[3px] border-brand-red pl-3">{{ $title }}</span>
                        </th>
                    </tr>
                    @foreach ($rows as $label => $value)
                        @php $odd = $loop->odd; @endphp
                        <tr class="{{ $odd ? 'bg-white dark:bg-gray-400' : 'bg-gray-50 dark:bg-gray-800/80' }} border-b border-gray-100 last:border-b-0 dark:border-gray-700">
                            <th scope="row" class="w-[min(40%,14rem)] border-r border-gray-200 px-4 py-2.5 align-top font-semibold text-gray-800 dark:border-gray-600 dark:text-gray-900">
                                {{ $label }}
                            </th>
                            <td class="px-4 py-2.5 align-top text-gray-900 dark:text-gray-900">
                                {{ $value }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
