<div>
<div class="bg-gray-900 text-white py-10">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-amber-400 text-2xl mb-2">★</p>
        <h1 class="text-3xl font-bold mb-2">NGN Club — Terms &amp; Conditions</h1>
        <p class="text-gray-400 text-sm">Last updated: January 2025</p>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <flux:card class="p-8">
        <ul class="space-y-4">
            @foreach([
                'NGN Club loyalty credits (£) are non-transferable.',
                'Each person is limited to one account.',
                'Loyalty credits earned will be assigned to your account after each qualifying purchase. Previous purchases made before joining the NGN Club are not eligible for credit.',
                'Members are responsible for keeping their account details safe.',
                'Credits will expire after 6 months of being added into a member\'s account.',
                'Credits cannot be used towards PCNs, Instalments, or Rentals.',
                'Loyalty credits earned will be available after 48 hours.',
                'Members will earn 10% credit on each £1 spent on repairs, maintenances, accessories and MOT to be used at any NGN stores.',
                'Members will earn 2% credit on each £1 spent on all motorbike purchases to be used at any NGN stores.',
                'Loyalty credits earned can only be used against your next purchase.',
                'Members will need a verification code to use their credits.',
                'NGN Club reserves the right to change or alter the terms and conditions of the loyalty scheme.',
                'All personal data is processed in accordance with the Data Protection Act 2018 based on General Data Protection Regulation (GDPR).',
                'NGN may contact you for special offers and schemes.',
            ] as $term)
                <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
                    <span class="text-amber-500 font-bold mt-0.5 flex-shrink-0">•</span>
                    <span>{{ $term }}</span>
                </li>
            @endforeach
        </ul>

        <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('ngnclub.register') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-amber-500 text-white font-semibold hover:bg-amber-600 transition">
                ★ Join NGN Club for Free
            </a>
            <a href="{{ route('ngnclub.login') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:border-brand-red hover:text-brand-red transition">
                Member Login
            </a>
        </div>
    </flux:card>
</div>
</div>
