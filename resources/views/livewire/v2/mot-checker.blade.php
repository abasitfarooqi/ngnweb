<div>
    <div class="ngn-page-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <h1 class="text-4xl font-black text-white mb-3">Free MOT Checker</h1>
            <p class="text-zinc-400 text-lg max-w-2xl">Check any UK vehicle's MOT status, history and advisories instantly — completely free.</p>
        </div>
    </div>

    <section class="py-16 bg-zinc-50">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-zinc-200 p-8">
                <h2 class="text-xl font-black text-zinc-900 mb-6 text-center">Enter Your Number Plate</h2>

                @if($error)
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-5 text-sm text-red-700">{{ $error }}</div>
                @endif

                <form wire:submit="check" class="space-y-5">
                    <div>
                        <label class="ngn-label">Vehicle Registration *</label>
                        <input wire:model="reg"
                               type="text"
                               class="ngn-input w-full text-center text-2xl font-black uppercase tracking-widest"
                               placeholder="AB12 CDE"
                               maxlength="10"
                               autocomplete="off">
                        @error('reg')<span class="ngn-error text-center block">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" wire:loading.attr="disabled" class="btn-ngn w-full justify-center py-3">
                        <span wire:loading.remove>Check MOT Status</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Checking...
                        </span>
                    </button>
                </form>
            </div>

            @if($result)
            <div class="mt-6 bg-white border border-zinc-200 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-4 h-4 {{ $result['status'] === 'valid' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                    <h3 class="font-black text-xl text-zinc-900">{{ strtoupper($result['registration'] ?? $reg) }}</h3>
                    <span class="ngn-badge-{{ $result['status'] === 'valid' ? 'green' : 'red' }}">
                        {{ $result['status'] === 'valid' ? 'MOT Valid' : 'MOT Expired' }}
                    </span>
                </div>
                <dl class="divide-y divide-zinc-100 text-sm">
                    @if(isset($result['expiryDate']))<div class="ngn-spec-row"><dt>Expiry Date</dt><dd>{{ $result['expiryDate'] }}</dd></div>@endif
                    @if(isset($result['make']))<div class="ngn-spec-row"><dt>Make</dt><dd>{{ $result['make'] }}</dd></div>@endif
                    @if(isset($result['model']))<div class="ngn-spec-row"><dt>Model</dt><dd>{{ $result['model'] }}</dd></div>@endif
                    @if(isset($result['colour']))<div class="ngn-spec-row"><dt>Colour</dt><dd>{{ $result['colour'] }}</dd></div>@endif
                    @if(isset($result['engineSize']))<div class="ngn-spec-row"><dt>Engine</dt><dd>{{ $result['engineSize'] }}cc</dd></div>@endif
                </dl>
            </div>
            @endif
        </div>
    </section>

    {{-- Info section --}}
    <section class="py-12 bg-white border-t border-zinc-100">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-xl font-black text-zinc-900 mb-3">MOT due soon?</h2>
            <p class="text-zinc-500 text-sm mb-5">Don't risk driving with an expired MOT. Book your service at NGN Motors and we'll get your bike roadworthy.</p>
            <a href="{{ route('v2.service.booking') }}" class="btn-ngn text-sm px-6 py-3">Book a Service</a>
        </div>
    </section>
</div>
