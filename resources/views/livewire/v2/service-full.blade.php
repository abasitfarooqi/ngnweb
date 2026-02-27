<div>
    <div class="ngn-page-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <nav class="text-xs text-zinc-400 mb-3">
                <a href="{{ route('v2.services') }}" class="hover:text-orange-400">Servicing</a>
                <span class="mx-2">/</span>
                <span class="text-zinc-300">Full Service</span>
            </nav>
            <h1 class="text-3xl font-black text-white mb-2">Full Motorbike Service</h1>
            <p class="text-zinc-400">A comprehensive inspection and service — everything covered from top to bottom.</p>
        </div>
    </div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="ngn-content-block">
                    <h2 class="text-xl font-black text-zinc-900 mb-4">What's Included</h2>
                    <ul class="space-y-3 text-sm text-zinc-700">
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Everything in the Basic Service</li>
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Spark plug replacement</li>
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Coolant flush and replacement</li>
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Brake fluid flush</li>
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Throttle and cable adjustment</li>
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Suspension inspection</li>
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Battery health check</li>
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Full diagnostic report</li>
                        <li class="flex gap-3"><span class="text-orange-500 font-bold">&#10003;</span> Road test by qualified technician</li>
                    </ul>
                </div>
            </div>
            <div class="lg:col-span-1">
                <div class="ngn-pricing-card sticky top-24 border-2 border-orange-300">
                    <div class="text-xs font-bold tracking-widest uppercase text-orange-600 mb-1">Full Service</div>
                    <div class="text-xs font-bold text-orange-500 mb-2">RECOMMENDED</div>
                    <div class="text-4xl font-black text-zinc-900 mb-1">From &pound;149</div>
                    <p class="text-zinc-500 text-sm mb-6">Labour included. Parts quoted separately.</p>
                    <a href="{{ route('v2.service.booking') }}" class="btn-ngn w-full justify-center text-sm mb-3">Book This Service</a>
                    <a href="{{ route('v2.service.comparison') }}" class="btn-ngn-outline w-full justify-center text-sm">Compare Packages</a>
                </div>
            </div>
        </div>
    </div>
</div>
