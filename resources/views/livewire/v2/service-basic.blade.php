<div>
    <div class="ngn-page-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <nav class="text-xs text-zinc-400 mb-3">
                <a href="{{ route('v2.services') }}" class="hover:text-orange-400">Servicing</a>
                <span class="mx-2">/</span>
                <span class="text-zinc-300">Basic Service</span>
            </nav>
            <h1 class="text-3xl font-black text-white mb-2">Basic Motorbike Service</h1>
            <p class="text-zinc-400">Essential maintenance to keep your bike running safely and reliably.</p>
        </div>
    </div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="ngn-content-block">
                    <h2 class="text-xl font-black text-zinc-900 mb-4">What's Included</h2>
                    <ul class="space-y-3 text-sm text-zinc-700">
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Engine oil and oil filter replacement</li>
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Air filter inspection and replacement (if needed)</li>
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Chain tension adjustment and lubrication</li>
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Tyre pressure check</li>
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Brake pad inspection</li>
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Fluid level check (brake, coolant, clutch)</li>
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Lights and electrics check</li>
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Visual safety inspection report</li>
                    </ul>
                </div>
                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 text-sm text-zinc-700">
                    <strong>Note:</strong> Parts not included in the base price. We'll always obtain your approval before any additional work is carried out.
                </div>
            </div>
            <div class="lg:col-span-1">
                <div class="ngn-pricing-card sticky top-24">
                    <div class="text-xs font-bold tracking-widest uppercase text-orange-600 mb-2">Basic Service</div>
                    <div class="text-4xl font-black text-zinc-900 mb-1">From &pound;89</div>
                    <p class="text-zinc-500 text-sm mb-6">Labour included. Parts quoted separately.</p>
                    <a href="{{ route('v2.service.booking') }}" class="btn-ngn w-full justify-center text-sm mb-3">Book This Service</a>
                    <a href="{{ route('v2.service.comparison') }}" class="btn-ngn-outline w-full justify-center text-sm">Compare Packages</a>
                </div>
            </div>
        </div>
    </div>
</div>
