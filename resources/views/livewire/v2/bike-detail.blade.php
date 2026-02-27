<div>
    @php $mb = $bike->getMotorbike(); $imgs = $bike->motorbikeImage ?? collect(); @endphp

    <div class="ngn-page-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <nav class="text-xs text-zinc-400 mb-3">
                <a href="{{ route('v2.home') }}" class="hover:text-orange-400">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ route('v2.bikes.sale') }}" class="hover:text-orange-400">Bikes for Sale</a>
                <span class="mx-2">/</span>
                <span class="text-zinc-300">{{ $mb?->make }} {{ $mb?->model }}</span>
            </nav>
            <h1 class="text-3xl font-black text-white">{{ $mb?->make }} {{ $mb?->model }}</h1>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {{-- Images --}}
            <div>
                @if($imgs->isNotEmpty())
                    <div x-data="{ active: 0 }">
                        <div class="bg-zinc-100 overflow-hidden mb-3" style="height:380px">
                            @foreach($imgs as $i => $img)
                                <img src="{{ Storage::url($img->image_path ?? '') }}"
                                     x-show="active === {{ $i }}"
                                     alt="{{ $mb?->make }} {{ $mb?->model }} - image {{ $i + 1 }}"
                                     class="w-full h-full object-cover">
                            @endforeach
                        </div>
                        @if($imgs->count() > 1)
                        <div class="flex gap-2 overflow-x-auto pb-1">
                            @foreach($imgs as $i => $img)
                                <button @click="active = {{ $i }}"
                                        :class="active === {{ $i }} ? 'border-2 border-orange-500' : 'border-2 border-transparent'"
                                        class="flex-shrink-0 w-16 h-14 overflow-hidden">
                                    <img src="{{ Storage::url($img->image_path ?? '') }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                        @endif
                    </div>
                @else
                    <div class="bg-zinc-800 flex items-center justify-center text-zinc-500 text-sm" style="height:380px">No image available</div>
                @endif
            </div>

            {{-- Details --}}
            <div>
                <div class="flex items-center gap-3 mb-4">
                    @if($bike->condition)
                        <span class="ngn-badge-{{ $bike->condition === 'new' ? 'orange' : 'zinc' }}">{{ ucfirst($bike->condition) }}</span>
                    @endif
                    @if($bike->is_sold ?? false)
                        <span class="ngn-badge-red">Sold</span>
                    @endif
                </div>

                <div class="text-4xl font-black text-orange-600 mb-6">&pound;{{ number_format($bike->price) }}</div>

                <dl class="divide-y divide-zinc-100 mb-8">
                    @if($mb)
                    <div class="ngn-spec-row"><dt>Make</dt><dd>{{ $mb->make }}</dd></div>
                    <div class="ngn-spec-row"><dt>Model</dt><dd>{{ $mb->model }}</dd></div>
                    <div class="ngn-spec-row"><dt>Year</dt><dd>{{ $mb->year }}</dd></div>
                    <div class="ngn-spec-row"><dt>Engine</dt><dd>{{ $mb->engine }}cc</dd></div>
                    <div class="ngn-spec-row"><dt>Colour</dt><dd>{{ $mb->color }}</dd></div>
                    <div class="ngn-spec-row"><dt>Fuel Type</dt><dd>{{ $mb->fuel_type }}</dd></div>
                    @if($mb->reg_no)<div class="ngn-spec-row"><dt>Reg</dt><dd>{{ $mb->reg_no }}</dd></div>@endif
                    @endif
                    @if($bike->mileage)<div class="ngn-spec-row"><dt>Mileage</dt><dd>{{ number_format($bike->mileage) }} miles</dd></div>@endif
                    @if($bike->v5_available !== null)<div class="ngn-spec-row"><dt>V5 Available</dt><dd>{{ $bike->v5_available ? 'Yes' : 'No' }}</dd></div>@endif
                </dl>

                @if($bike->note)
                <div class="bg-zinc-50 border-l-4 border-orange-500 p-4 mb-6 text-sm text-zinc-700 leading-relaxed">
                    {!! nl2br(e($bike->note)) !!}
                </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('v2.contact') }}?interest={{ urlencode(($mb?->make ?? '').' '.($mb?->model ?? '')) }}"
                       class="btn-ngn flex-1 justify-center">Enquire About This Bike</a>
                    <a href="tel:+447907600611" class="btn-ngn-dark flex-1 justify-center">Call Us: 07907 600 611</a>
                </div>
            </div>
        </div>

        {{-- Related --}}
        @if($related->isNotEmpty())
        <div class="mt-16 pt-12 border-t border-zinc-100">
            <h2 class="text-xl font-black text-zinc-900 mb-6">More Bikes You Might Like</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach($related as $sale)
                    @php $rmb = $sale->getMotorbike(); @endphp
                    @if($rmb)
                    <a href="{{ route('v2.bike.detail', $rmb->slug ?? $rmb->id) }}" class="ngn-bike-card group">
                        <div class="overflow-hidden bg-zinc-100">
                            @php $rimg = $sale->getMotorbikeImage(); @endphp
                            @if($rimg)
                                <img src="{{ Storage::url($rimg->image_path ?? '') }}" alt="{{ $rmb->make }} {{ $rmb->model }}" class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                            @else
                                <div class="w-full h-40 bg-zinc-200 flex items-center justify-center text-zinc-400 text-xs">No image</div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-sm text-zinc-900">{{ $rmb->make }} {{ $rmb->model }}</h3>
                            <p class="text-orange-600 font-black mt-1">&pound;{{ number_format($sale->price) }}</p>
                        </div>
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
