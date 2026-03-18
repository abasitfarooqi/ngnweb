<div>
{{-- Hero --}}
<div class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-2">Motorcycle Blog</h1>
        <p class="text-gray-300">Tips, guides, news and updates from the NGN Motors team</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($posts->isEmpty())
        <div class="text-center py-20">
            <flux:icon name="newspaper" class="h-12 w-12 text-gray-300 mx-auto mb-4" />
            <p class="text-gray-500 dark:text-gray-400">No blog posts yet. Check back soon!</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($posts as $post)
                <article class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-brand-red transition group flex flex-col">
                    @if($post->images && $post->images->count() > 0)
                        <a href="{{ route('shop.blog.show', $post->slug) }}" class="block overflow-hidden">
                            <img src="{{ $post->images->first()->image_url }}"
                                 alt="{{ $post->title }}"
                                 class="w-full h-48 object-cover group-hover:scale-105 transition duration-300"
                                 loading="lazy">
                        </a>
                    @else
                        <a href="{{ route('shop.blog.show', $post->slug) }}"
                           class="block w-full h-32 bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                            <flux:icon name="newspaper" class="h-10 w-10 text-gray-300" />
                        </a>
                    @endif
                    <div class="p-5 flex flex-col flex-1">
                        @if($post->category)
                            <p class="text-xs font-medium text-brand-red uppercase tracking-wide mb-1">{{ $post->category->name }}</p>
                        @endif
                        <a href="{{ route('shop.blog.show', $post->slug) }}"
                           class="text-base font-semibold text-gray-900 dark:text-white hover:text-brand-red line-clamp-2 mb-2 flex-1">
                            {{ $post->title }}
                        </a>
                        <div class="flex items-center justify-between mt-auto pt-3 border-t border-gray-100 dark:border-gray-700">
                            <p class="text-xs text-gray-400">{{ $post->created_at->format('d M Y') }}</p>
                            <a href="{{ route('shop.blog.show', $post->slug) }}"
                               class="text-xs text-brand-red hover:underline font-medium">Read more →</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $posts->links() }}
        </div>
    @endif
</div>
</div>
