<div>
    <div class="site-page-hero py-14">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h1 class="text-3xl md:text-4xl font-bold mb-3">Motorcycle rentals</h1>
            @if($referral)
                <p class="text-gray-300">You were referred by a current NGN Motors rental customer.</p>
            @else
                <p class="text-gray-300">That referral code was not found. You can still enquire about a rental.</p>
            @endif
        </div>
    </div>
    <div class="max-w-3xl mx-auto px-4 py-10 space-y-4">
        @if($referral)
            <p class="text-sm text-gray-700 dark:text-gray-300">Code {{ $referral->referral_code }} is saved on this device so we can attribute your enquiry.</p>
        @endif
        <div class="flex flex-wrap gap-3">
            <flux:button href="{{ route('site.rentals') }}" variant="filled" class="bg-brand-red text-white !rounded-none">View rentals</flux:button>
            <flux:button href="{{ route('account.rentals') }}" variant="outline" class="!rounded-none">Customer account</flux:button>
        </div>
    </div>
</div>
