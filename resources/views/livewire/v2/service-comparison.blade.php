<div>
    <div class="ngn-page-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-3xl font-black text-white mb-2">Service Package Comparison</h1>
            <p class="text-zinc-400">Compare our service packages to find the right one for your bike.</p>
        </div>
    </div>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="border-b-2 border-zinc-200">
                    <th class="text-left py-3 pr-6 font-black text-zinc-900 w-1/2">Service Item</th>
                    <th class="text-center py-3 px-4 font-black text-zinc-900">Basic<br><span class="font-normal text-orange-600">From &pound;89</span></th>
                    <th class="text-center py-3 px-4 font-black text-orange-600 border-b-2 border-orange-500">Full Service<br><span class="font-normal">From &pound;149</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach([
                    ['Oil &amp; Filter Change', true, true],
                    ['Air Filter Check', true, true],
                    ['Chain Adjustment', true, true],
                    ['Tyre Pressure', true, true],
                    ['Brake Pad Inspection', true, true],
                    ['Fluid Level Check', true, true],
                    ['Lights &amp; Electrics Check', true, true],
                    ['Safety Report', true, true],
                    ['Spark Plug Replacement', false, true],
                    ['Coolant Flush', false, true],
                    ['Brake Fluid Flush', false, true],
                    ['Throttle Adjustment', false, true],
                    ['Suspension Inspection', false, true],
                    ['Battery Health Check', false, true],
                    ['Full Diagnostic Report', false, true],
                    ['Road Test', false, true],
                ] as [$item, $basic, $full])
                <tr class="border-b border-zinc-100 hover:bg-zinc-50">
                    <td class="py-3 pr-6 text-zinc-700">{!! $item !!}</td>
                    <td class="text-center py-3 px-4">
                        @if($basic)<span class="text-green-600 font-bold text-base">&#10003;</span>@else<span class="text-zinc-300 text-base">&#8212;</span>@endif
                    </td>
                    <td class="text-center py-3 px-4 bg-orange-50">
                        @if($full)<span class="text-orange-600 font-bold text-base">&#10003;</span>@else<span class="text-zinc-300 text-base">&#8212;</span>@endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('v2.service.booking') }}?type=basic" class="btn-ngn-outline text-sm px-6 py-3">Book Basic Service</a>
            <a href="{{ route('v2.service.booking') }}?type=full" class="btn-ngn text-sm px-6 py-3">Book Full Service</a>
        </div>
    </div>
</div>
