<div>
    <div class="ngn-page-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-3xl font-black text-white mb-2">QR Rental Booking</h1>
            <p class="text-zinc-400">Complete your motorbike rental booking in a few simple steps.</p>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        @if($submitted)
            {{-- Success state --}}
            <div class="bg-green-50 border-l-4 border-green-500 p-8 text-center">
                <svg class="w-14 h-14 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h2 class="text-2xl font-black text-zinc-900 mb-2">Booking Request Sent!</h2>
                <p class="text-zinc-600 mb-6">We've received your request and will contact you shortly to confirm your rental. Check your email for a summary.</p>
                <a href="{{ route('v2.rentals') }}" class="btn-ngn text-sm px-6 py-3">Back to Rentals</a>
            </div>
        @else
            {{-- Step indicator --}}
            <div class="flex items-center gap-2 mb-10">
                @foreach([1 => 'Your Details', 2 => 'Booking Info', 3 => 'Confirm'] as $n => $label)
                <div class="flex items-center gap-2 {{ !$loop->last ? 'flex-1' : '' }}">
                    <div class="w-8 h-8 flex items-center justify-center font-bold text-sm flex-shrink-0
                        {{ $step >= $n ? 'bg-orange-600 text-white' : 'bg-zinc-200 text-zinc-500' }}">
                        {{ $n }}
                    </div>
                    <span class="hidden sm:block text-xs {{ $step >= $n ? 'text-zinc-900 font-semibold' : 'text-zinc-400' }}">{{ $label }}</span>
                    @if(!$loop->last)<div class="flex-1 h-px bg-zinc-200 mx-2"></div>@endif
                </div>
                @endforeach
            </div>

            @if($bike)
            <div class="bg-orange-50 border-l-4 border-orange-500 p-4 mb-6 flex items-center gap-3">
                <svg class="w-5 h-5 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm text-zinc-700">Booking for: <strong>{{ $bike->make }} {{ $bike->model }}</strong> ({{ $bike->reg_no }})</span>
            </div>
            @endif

            {{-- Step 1: Personal Details --}}
            @if($step === 1)
            <div>
                <h2 class="text-xl font-black text-zinc-900 mb-6">Your Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="ngn-label">Full Name *</label>
                        <input wire:model="full_name" type="text" class="ngn-input w-full" placeholder="John Smith">
                        @error('full_name')<span class="ngn-error">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="ngn-label">Email Address *</label>
                        <input wire:model="email" type="email" class="ngn-input w-full" placeholder="john@example.com">
                        @error('email')<span class="ngn-error">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="ngn-label">Phone Number *</label>
                        <input wire:model="phone" type="tel" class="ngn-input w-full" placeholder="07900 000000">
                        @error('phone')<span class="ngn-error">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="ngn-label">Address *</label>
                        <input wire:model="address" type="text" class="ngn-input w-full" placeholder="123 High Street, London, E1 1AA">
                        @error('address')<span class="ngn-error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="mt-8 flex justify-end">
                    <button wire:click="nextStep" class="btn-ngn text-sm px-6 py-3">Continue &rarr;</button>
                </div>
            </div>
            @endif

            {{-- Step 2: Booking Details --}}
            @if($step === 2)
            <div>
                <h2 class="text-xl font-black text-zinc-900 mb-6">Booking Details</h2>

                @if(!$bike)
                <div class="mb-4">
                    <label class="ngn-label">Select a Bike (optional)</label>
                    <select wire:model="code" class="ngn-input w-full">
                        <option value="">-- Select a bike --</option>
                        @foreach($availableBikes as $ab)
                            <option value="{{ $ab->id }}">{{ $ab->make }} {{ $ab->model }} ({{ $ab->reg_no }})</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="space-y-4">
                    <div>
                        <label class="ngn-label">Start Date *</label>
                        <input wire:model="start_date" type="date" class="ngn-input w-full" min="{{ now()->addDay()->format('Y-m-d') }}">
                        @error('start_date')<span class="ngn-error">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="ngn-label">Rental Duration *</label>
                        <select wire:model="duration" class="ngn-input w-full">
                            <option value="1">1 Day</option>
                            <option value="3">3 Days</option>
                            <option value="7">1 Week</option>
                            <option value="14">2 Weeks</option>
                            <option value="30">1 Month</option>
                        </select>
                        @error('duration')<span class="ngn-error">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="ngn-label">Additional Notes</label>
                        <textarea wire:model="notes" rows="3" class="ngn-input w-full" placeholder="Any special requirements..."></textarea>
                    </div>
                </div>
                <div class="mt-8 flex justify-between">
                    <button wire:click="prevStep" class="btn-ngn-outline text-sm px-6 py-3">&larr; Back</button>
                    <button wire:click="nextStep" class="btn-ngn text-sm px-6 py-3">Continue &rarr;</button>
                </div>
            </div>
            @endif

            {{-- Step 3: Confirm --}}
            @if($step === 3)
            <div>
                <h2 class="text-xl font-black text-zinc-900 mb-6">Confirm Booking</h2>
                <div class="bg-zinc-50 border border-zinc-200 p-6 mb-6 space-y-3">
                    <div class="ngn-spec-row border-b border-zinc-200 pb-3"><dt class="font-semibold text-zinc-700">Name</dt><dd>{{ $full_name }}</dd></div>
                    <div class="ngn-spec-row border-b border-zinc-200 pb-3"><dt class="font-semibold text-zinc-700">Email</dt><dd>{{ $email }}</dd></div>
                    <div class="ngn-spec-row border-b border-zinc-200 pb-3"><dt class="font-semibold text-zinc-700">Phone</dt><dd>{{ $phone }}</dd></div>
                    <div class="ngn-spec-row border-b border-zinc-200 pb-3"><dt class="font-semibold text-zinc-700">Start Date</dt><dd>{{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d M Y') : '-' }}</dd></div>
                    <div class="ngn-spec-row"><dt class="font-semibold text-zinc-700">Duration</dt><dd>{{ $duration }} day(s)</dd></div>
                </div>
                <div class="flex justify-between">
                    <button wire:click="prevStep" class="btn-ngn-outline text-sm px-6 py-3">&larr; Back</button>
                    <button wire:click="submit" wire:loading.attr="disabled" class="btn-ngn text-sm px-6 py-3">
                        <span wire:loading.remove>Confirm Booking</span>
                        <span wire:loading>Submitting...</span>
                    </button>
                </div>
            </div>
            @endif
        @endif
    </div>
</div>
