<div>
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold mb-3">Our London Branches</h1>
        <p class="text-gray-300">Visit us in Catford, Tooting or Sutton</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($branches as $branch)
            @php
                $phone   = $branch->phone    ?? config('site.branches.' . strtolower($branch->name) . '.phone');
                $address = $branch->address  ?? config('site.branches.' . strtolower($branch->name) . '.address');
                $postcode= $branch->postal_code ?? '';
                $email   = 'enquiries@neguinhomotors.co.uk';
                $mapUrl  = 'https://www.google.com/maps?q=' . urlencode($address . ', ' . $postcode);
            @endphp
            <flux:card class="p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 uppercase">{{ $branch->name }}</h2>
                <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex items-start gap-2">
                        <flux:icon name="map-pin" class="h-4 w-4 text-brand-red flex-shrink-0 mt-0.5" />
                        <a href="{{ $mapUrl }}" target="_blank" class="hover:text-brand-red transition">{{ $address }}, {{ $postcode }}</a>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:icon name="phone" class="h-4 w-4 text-brand-red flex-shrink-0 mt-0.5" />
                        <a href="tel:{{ $phone }}" class="hover:text-brand-red transition">{{ $phone }}</a>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:icon name="envelope" class="h-4 w-4 text-brand-red flex-shrink-0 mt-0.5" />
                        <a href="mailto:{{ $email }}" class="hover:text-brand-red transition">{{ $email }}</a>
                    </li>
                </ul>
                <div class="mt-5">
                    <flux:button href="{{ $mapUrl }}" target="_blank" variant="outline" size="sm" class="w-full">
                        Get Directions
                    </flux:button>
                </div>
            </flux:card>
        @endforeach
    </div>

    <div class="mt-10 bg-gray-50 dark:bg-gray-800 p-6">
        <h3 class="font-bold text-gray-900 dark:text-white mb-3">Opening Hours</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm text-gray-600 dark:text-gray-400">
            <div><span class="font-medium text-gray-900 dark:text-white">Monday–Friday</span><br>9:00am – 6:00pm</div>
            <div><span class="font-medium text-gray-900 dark:text-white">Saturday</span><br>9:00am – 5:00pm</div>
            <div><span class="font-medium text-gray-900 dark:text-white">Sunday</span><br>Closed</div>
        </div>
    </div>
</div>
</div>
