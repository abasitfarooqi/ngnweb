@props([
    'heading' => 'Book Your Basic Service Today',
    'intro' => 'Keep your motorcycle in perfect running condition with our comprehensive basic service package',
])

<section class="bg-gray-900 text-white border border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 text-center">
        <h3 class="text-xl md:text-2xl font-bold text-brand-red mb-3">{{ $heading }}</h3>
        <p class="text-gray-300 text-sm md:text-base mb-6 max-w-3xl mx-auto">{{ $intro }}</p>
        <div class="bg-black/50 p-6 md:p-8 text-left">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach(config('site.branches', []) as $key => $branch)
                    <div>
                        <h4 class="font-semibold text-white mb-2">{{ $branch['name'] ?? ucfirst($key) }} Branch</h4>
                        <p class="text-sm text-gray-300">
                            📞 <a href="tel:{{ preg_replace('/\s+/', '', $branch['phone'] ?? '') }}" class="text-white hover:text-brand-red underline-offset-2">{{ $branch['phone'] ?? '' }}</a>
                        </p>
                        @if(! empty($branch['whatsapp_link']))
                            <p class="text-sm text-gray-300 mt-1">
                                <a href="{{ $branch['whatsapp_link'] }}" target="_blank" rel="noopener noreferrer" class="text-green-400 hover:text-green-300 underline underline-offset-2">WhatsApp</a>
                            </p>
                        @endif
                        <p class="text-xs text-gray-400 mt-2">
                            @if(! empty($branch['map']))
                                <a href="{{ $branch['map'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white underline underline-offset-2">{{ $branch['address'] ?? '' }}</a>
                            @else
                                {{ $branch['address'] ?? '' }}
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
