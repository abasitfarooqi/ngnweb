<div>
    <div class="ngn-page-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-3xl font-black text-white mb-2">Book a Service</h1>
            <p class="text-zinc-400">Fill in the form below and we'll confirm your booking within 2 hours.</p>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($submitted)
            <div class="bg-green-50 border-l-4 border-green-500 p-8 text-center">
                <svg class="w-14 h-14 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h2 class="text-2xl font-black text-zinc-900 mb-2">Booking Request Received!</h2>
                <p class="text-zinc-600 mb-6">We'll confirm your service appointment by email within 2 hours during business hours.</p>
                <a href="{{ route('v2.services') }}" class="btn-ngn text-sm px-6 py-3">Back to Services</a>
            </div>
        @else
            <form wire:submit="submit" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="ngn-label">Full Name *</label>
                        <input wire:model="full_name" type="text" class="ngn-input w-full" placeholder="John Smith">
                        @error('full_name')<span class="ngn-error">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="ngn-label">Phone *</label>
                        <input wire:model="phone" type="tel" class="ngn-input w-full" placeholder="07900 000000">
                        @error('phone')<span class="ngn-error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div>
                    <label class="ngn-label">Email *</label>
                    <input wire:model="email" type="email" class="ngn-input w-full" placeholder="john@example.com">
                    @error('email')<span class="ngn-error">{{ $message }}</span>@enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="ngn-label">Registration Number *</label>
                        <input wire:model="reg_no" type="text" class="ngn-input w-full uppercase" placeholder="AB12 CDE">
                        @error('reg_no')<span class="ngn-error">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="ngn-label">Make &amp; Model *</label>
                        <input wire:model="make_model" type="text" class="ngn-input w-full" placeholder="Honda CB500">
                        @error('make_model')<span class="ngn-error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="ngn-label">Service Type *</label>
                        <select wire:model="service_type" class="ngn-input w-full">
                            <option value="basic">Basic Service (&pound;89+)</option>
                            <option value="full">Full Service (&pound;149+)</option>
                            <option value="repairs">Repairs / Diagnostics</option>
                            <option value="other">Other</option>
                        </select>
                        @error('service_type')<span class="ngn-error">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="ngn-label">Preferred Date *</label>
                        <input wire:model="preferred_date" type="date" class="ngn-input w-full" min="{{ now()->addDay()->format('Y-m-d') }}">
                        @error('preferred_date')<span class="ngn-error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div>
                    <label class="ngn-label">Additional Notes</label>
                    <textarea wire:model="notes" rows="3" class="ngn-input w-full" placeholder="Describe any issues or specific requests..."></textarea>
                </div>
                <button type="submit" wire:loading.attr="disabled" class="btn-ngn w-full justify-center text-sm py-3">
                    <span wire:loading.remove>Submit Booking Request</span>
                    <span wire:loading>Submitting...</span>
                </button>
            </form>
        @endif
    </div>
</div>
