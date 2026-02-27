<div>
{{-- Hero Section --}}
<div class="bg-gradient-to-r from-brand-red to-red-700 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $title }}</h1>
    </div>
</div>

{{-- Content --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="prose prose-lg dark:prose-invert max-w-none">
        {!! $content !!}
    </div>
    
    <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-600 dark:text-gray-400">Last updated: {{ date('F Y') }}</p>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            If you have any questions, please <a href="/contact" class="text-brand-red hover:text-red-700">contact us</a>.
        </p>
    </div>
</div>

<style>
    .prose h3 {
        @apply text-2xl font-bold text-gray-900 dark:text-white mt-8 mb-4;
    }
    
    .prose p {
        @apply text-gray-600 dark:text-gray-400 mb-4;
    }
    
    .prose ul {
        @apply list-disc list-inside text-gray-600 dark:text-gray-400 mb-4;
    }
    
    .prose li {
        @apply mb-2;
    }
</style>
</div>
