<div>
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold mb-3">Careers at NGN</h1>
        <p class="text-gray-300 text-lg">Join London's growing motorcycle specialist team</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    @if($careers->isEmpty())
        <flux:callout variant="info" icon="information-circle">
            <flux:callout.heading>No current vacancies</flux:callout.heading>
            <flux:callout.text>We don't have any open positions right now but we're always on the lookout for great talent. Send your CV to <a href="mailto:enquiries@neguinhomotors.co.uk" class="underline">enquiries@neguinhomotors.co.uk</a></flux:callout.text>
        </flux:callout>
    @else
        <div class="space-y-4">
            @foreach($careers as $career)
                <flux:card class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <a href="{{ route('site.careers.show', $career->id) }}" class="text-lg font-bold text-gray-900 dark:text-white hover:text-brand-red transition">
                                {{ $career->job_title }}
                            </a>
                            <p class="text-sm text-gray-500 mt-1">
                                Posted: {{ $career->job_posted ? $career->job_posted->format('d M Y') : 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                {!! \Illuminate\Support\Str::limit(strip_tags($career->description), 180, '…') !!}
                            </p>
                        </div>
                        <flux:button href="{{ route('site.careers.show', $career->id) }}" variant="outline" size="sm" class="flex-shrink-0">
                            Apply →
                        </flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif
</div>
</div>
