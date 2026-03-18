<div>
{{-- Breadcrumb --}}
<div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 py-3">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm text-gray-500">
            <a href="/" class="hover:text-brand-red">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('shop.blog') }}" class="hover:text-brand-red">Blog</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800 dark:text-gray-200 line-clamp-1">{{ $post->title }}</span>
        </nav>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Category --}}
    @if($post->category)
        <p class="text-xs font-medium text-brand-red uppercase tracking-wide mb-3">{{ $post->category->name }}</p>
    @endif

    {{-- Title --}}
    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">{{ $post->title }}</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Published {{ $post->created_at->format('d F Y') }}</p>

    {{-- Featured image --}}
    @if($post->images && $post->images->count() > 0)
        <img src="{{ $post->images->first()->image_url }}"
             alt="{{ $post->title }}"
             class="w-full h-72 md:h-96 object-cover mb-8">
    @endif

    {{-- Content --}}
    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
        {!! $post->content !!}
    </div>

    {{-- More images --}}
    @if($post->images && $post->images->count() > 1)
        <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($post->images->skip(1) as $img)
                <img src="{{ $img->image_url }}" alt="{{ $post->title }}"
                     class="w-full h-32 object-cover" loading="lazy">
            @endforeach
        </div>
    @endif

    {{-- Back link --}}
    <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ route('shop.blog') }}"
           class="inline-flex items-center gap-2 text-brand-red hover:underline text-sm font-medium">
            <flux:icon name="arrow-left" class="h-4 w-4" /> Back to Blog
        </a>
    </div>
</div>
</div>
