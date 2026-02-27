<div>
    <div class="ngn-page-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <nav class="text-xs text-zinc-400 mb-3">
                <a href="{{ route('v2.services') }}" class="hover:text-orange-400">Servicing</a>
                <span class="mx-2">/</span>
                <span class="text-zinc-300">Repairs</span>
            </nav>
            <h1 class="text-3xl font-black text-white mb-2">Motorbike Repair Services</h1>
            <p class="text-zinc-400">From minor faults to full engine rebuilds — our technicians handle it all.</p>
        </div>
    </div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach([
                ['Engine Diagnostics', 'Fault code reading and root-cause analysis for all engine issues.'],
                ['Electrical Faults', 'Wiring, ECU, battery, starter motor and charging system repairs.'],
                ['Brake System', 'Pad replacement, disc skimming, brake fluid changes and ABS diagnostics.'],
                ['Suspension', 'Fork seals, shock absorber servicing and geometry alignment.'],
                ['Tyre Fitting', 'All tyre sizes, balancing and valve replacement.'],
                ['Body & Cosmetic', 'Fairing repair, painting, decal replacement and bodywork restoration.'],
            ] as [$title, $desc])
            <div class="ngn-service-card">
                <h3 class="font-bold text-zinc-900 mb-2">{{ $title }}</h3>
                <p class="text-zinc-500 text-sm leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
        <div class="bg-orange-50 border border-orange-200 p-6 text-center">
            <p class="text-zinc-700 text-sm mb-4">Not sure what's wrong? Bring your bike in for a free visual inspection.</p>
            <a href="{{ route('v2.service.booking') }}" class="btn-ngn text-sm px-6 py-3">Book an Inspection</a>
        </div>
    </div>
</div>
