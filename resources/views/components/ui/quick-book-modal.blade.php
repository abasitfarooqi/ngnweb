<flux:modal name="quick-book" class="md:w-[28rem]">
    <flux:heading size="lg">Quick book</flux:heading>
    <flux:subheading class="mb-4">Choose a booking option below.</flux:subheading>

    <div class="flex flex-col gap-2">
        <flux:button href="{{ route('site.contact.service-booking') }}" variant="primary" class="w-full">
            Service / MOT booking
        </flux:button>
        <flux:button href="{{ route('site.contact.callback') }}" variant="ghost" class="w-full">
            Request a call back
        </flux:button>
        <flux:button href="{{ route('site.rentals') }}" variant="ghost" class="w-full">
            Motorcycle rental
        </flux:button>
    </div>
</flux:modal>
