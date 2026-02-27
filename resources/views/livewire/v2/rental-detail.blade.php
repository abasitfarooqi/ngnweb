<div>
    <div class="ngn-page-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <nav class="text-xs text-zinc-400 mb-3">
                <a href="{{ route('v2.rentals') }}" class="hover:text-orange-400">Rentals</a>
                <span class="mx-2">/</span>
                <span class="text-zinc-300">{{ $bike->make }} {{ $bike->model }}</span>
            </nav>
            <h1 class="text-3xl font-black text-white">{{ $bike->make }} {{ $bike->model }}</h1>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div class="bg-zinc-800 flex items-center justify-center text-zinc-500 text-sm" style="height:360px">
                {{ $bike->make }} {{ $bike->model }} Image
            </div>
            <div>
                <dl class="divide-y divide-zinc-100 mb-8">
                    <div class="ngn-spec-row"><dt>Make</dt><dd>{{ $bike->make }}</dd></div>
                    <div class="ngn-spec-row"><dt>Model</dt><dd>{{ $bike->model }}</dd></div>
                    <div class="ngn-spec-row"><dt>Year</dt><dd>{{ $bike->year }}</dd></div>
                    <div class="ngn-spec-row"><dt>Engine</dt><dd>{{ $bike->engine }}cc</dd></div>
                    <div class="ngn-spec-row"><dt>Colour</dt><dd>{{ $bike->color }}</dd></div>
                </dl>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('v2.rental.qr.booking') }}" class="btn-ngn flex-1 justify-center">Book This Bike</a>
                    <a href="{{ route('v2.rentals') }}" class="btn-ngn-outline flex-1 justify-center">Back to Fleet</a>
                </div>
            </div>
        </div>
    </div>
</div>
