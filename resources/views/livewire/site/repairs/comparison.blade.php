<div>
{{-- Hero --}}
<div class="bg-gradient-to-r from-brand-red to-red-700 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Service Comparison</h1>
        <p class="text-xl text-red-100">Choose the right service for your motorcycle</p>
    </div>
</div>

{{-- Comparison Table --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="overflow-x-auto">
        <table class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700">Service Item</th>
                    <th class="px-6 py-4 text-center text-sm font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700">Basic<br><span class="text-brand-red font-bold">£80</span></th>
                    <th class="px-6 py-4 text-center text-sm font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 bg-red-50 dark:bg-red-900/20">Full<br><span class="text-brand-red font-bold">£150</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach([
                    ['item' => 'Engine Oil Change',               'basic' => true,  'full' => true],
                    ['item' => 'Oil Filter Replacement',          'basic' => true,  'full' => true],
                    ['item' => 'Air Filter Replacement',          'basic' => false, 'full' => true],
                    ['item' => 'Spark Plug Inspection/Replacement','basic' => false, 'full' => true],
                    ['item' => 'Brake System Inspection',         'basic' => true,  'full' => true],
                    ['item' => 'Brake Fluid Check/Top-up',        'basic' => false, 'full' => true],
                    ['item' => 'Tyre Pressure & Condition',       'basic' => true,  'full' => true],
                    ['item' => 'Chain Clean, Adjust & Lube',      'basic' => true,  'full' => true],
                    ['item' => 'Sprocket Inspection',             'basic' => false, 'full' => true],
                    ['item' => 'Suspension Inspection',           'basic' => false, 'full' => true],
                    ['item' => 'Electrical System Testing',       'basic' => true,  'full' => true],
                    ['item' => 'Battery Test & Charge Check',     'basic' => false, 'full' => true],
                    ['item' => 'Safety Inspection',               'basic' => true,  'full' => true],
                    ['item' => 'Coolant Level Check',             'basic' => false, 'full' => true],
                    ['item' => 'Road Test',                       'basic' => false, 'full' => true],
                ] as $i => $row)
                    <tr class="{{ $i % 2 === 1 ? 'bg-gray-50 dark:bg-gray-900/40' : '' }}">
                        <td class="px-6 py-3.5 text-sm text-gray-900 dark:text-white">{{ $row['item'] }}</td>
                        <td class="px-6 py-3.5 text-center">
                            @if($row['basic'])
                                <flux:icon name="check-circle" class="h-5 w-5 text-green-500 mx-auto" />
                            @else
                                <span class="text-gray-300 dark:text-gray-600 text-lg">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-center bg-red-50/30 dark:bg-red-900/10">
                            <flux:icon name="check-circle" class="h-5 w-5 text-green-500 mx-auto" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Book CTA --}}
<div class="bg-brand-red text-white py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Book Your Service?</h2>
        <p class="text-xl text-red-100 mb-8">Choose the service that's right for your bike</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <flux:button href="/contact/service-booking" variant="filled" class="bg-white text-brand-red hover:bg-gray-100">Book Basic Service — £80</flux:button>
            <flux:button href="/contact/service-booking" variant="outline" class="border-white text-white hover:bg-white hover:text-brand-red">Book Full Service — £150</flux:button>
        </div>
    </div>
</div>
</div>
