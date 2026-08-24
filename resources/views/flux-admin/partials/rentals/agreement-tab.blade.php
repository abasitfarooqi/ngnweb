<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 border-b border-zinc-200 dark:border-zinc-700">
        <div>
            <p class="text-sm font-semibold text-zinc-900 dark:text-white">Rental agreement</p>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Generate / email the customer signing link, then verify signed PDFs (same as legacy Documents tab “Verified: FILE”).</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <flux:button size="sm" variant="primary" wire:click="generateAgreement" wire:loading.attr="disabled" class="!rounded-none">
                Generate agreement &amp; QR
            </flux:button>
            <flux:button
                size="sm"
                variant="outline"
                wire:click="sendAgreementLinkEmail"
                wire:loading.attr="disabled"
                wire:target="sendAgreementLinkEmail,generateAgreement"
                class="!rounded-none"
            >
                Email signing link
            </flux:button>
        </div>
    </div>

    @if($flashMessage)
        <div class="mx-4 mt-4 p-3 text-sm font-medium border
            {{ $flashType === 'success' ? 'border-emerald-400 bg-emerald-50 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300' : 'border-red-400 bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300' }}">
            {{ $flashMessage }}
        </div>
    @endif

    @if($agreementUrl)
        <div class="mx-4 mt-4 p-4 border border-zinc-200 dark:border-zinc-700">
            <p class="text-xs font-bold text-zinc-500 mb-2">Customer signing link (email / copy-paste)</p>
            <a href="{{ $agreementUrl }}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 break-all hover:underline">{{ $agreementUrl }}</a>
            @if($qrImage)
                <div class="mt-3">
                    <img src="{{ $qrImage }}" alt="Agreement QR code" class="w-48 h-48 border border-zinc-200 dark:border-zinc-700" />
                </div>
            @endif
        </div>
    @endif

    {{-- Signed PDFs after customer signs (customer_agreements) --}}
    <div class="mx-4 mt-4 border border-zinc-200 dark:border-zinc-700">
        <div class="px-4 py-2 border-b border-zinc-200 dark:border-zinc-700 text-xs font-bold uppercase tracking-wide text-zinc-500">
            Signed rental agreements
        </div>
        @if($signedAgreements->isEmpty())
            <p class="p-4 text-sm text-zinc-500 dark:text-zinc-400">No signed PDF yet. Send the signing link — files appear here after the customer signs.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 p-4">
                @foreach($signedAgreements as $signed)
                    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-3" wire:key="signed-{{ $signed->id }}">
                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">Rental Agreement</p>
                        <p class="text-xs text-zinc-500 mt-1 truncate" title="{{ $signed->file_name }}">{{ $signed->file_name ?: 'PDF' }}</p>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            @if($signed->is_verified)
                                <span class="inline-flex items-center border border-emerald-600 px-2 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">Verified: FILE ✓</span>
                            @else
                                <span class="inline-flex items-center border border-red-500 px-2 py-1 text-xs font-semibold text-red-600">Not verified</span>
                                <flux:button size="xs" variant="primary" wire:click="verifySignedAgreement({{ $signed->id }})" class="!rounded-none">Verify</flux:button>
                            @endif
                            @if($signed->public_url)
                                <a href="{{ $signed->public_url }}" target="_blank" class="text-xs text-blue-600 dark:text-blue-400 underline">Open file</a>
                            @elseif($signed->sent_private)
                                <span class="text-xs text-zinc-500">Archived — secure storage only</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if($agreements->isNotEmpty())
        <div class="divide-y divide-zinc-200 dark:divide-zinc-700 mt-2">
            @foreach($agreements as $agreement)
                @php
                    $customerUrl = \App\Models\AgreementAccess::customerSigningUrl((int) $agreement->customer_id, (string) $agreement->passcode);
                    $loyaltyUrl = \App\Models\AgreementAccess::loyaltySchemeSigningUrl((int) $agreement->customer_id, (string) $agreement->passcode);
                @endphp
                <div class="p-5" wire:key="agreement-{{ $agreement->id }}">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1">Customer</p>
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                                {{ $agreement->customer?->first_name }} {{ $agreement->customer?->last_name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1">Passcode</p>
                            <p class="text-sm font-mono font-semibold text-zinc-900 dark:text-white">{{ $agreement->passcode }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1">Expires At</p>
                            <p class="text-sm text-zinc-900 dark:text-white">
                                {{ $agreement->expires_at ? \Carbon\Carbon::parse($agreement->expires_at)->format('d M Y H:i') : 'No expiry' }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-1 gap-4">
                        <div>
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1">Customer signing link</p>
                            <a href="{{ $customerUrl }}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline break-all">{{ $customerUrl }}</a>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1">Loyalty Scheme (optional)</p>
                            <a href="{{ $loyaltyUrl }}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline break-all">{{ $loyaltyUrl }}</a>
                        </div>
                    </div>
                    <div class="mt-4">
                        <flux:button
                            size="sm"
                            variant="outline"
                            wire:click="sendAgreementLinkEmail({{ $agreement->id }})"
                            wire:loading.attr="disabled"
                            class="!rounded-none"
                        >
                            Email this signing link
                        </flux:button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-8 text-center">
            <flux:icon name="document-text" variant="outline" class="w-8 h-8 mx-auto text-zinc-400 dark:text-zinc-500 mb-3" />
            <p class="text-sm text-zinc-500 dark:text-zinc-400">No agreement access records yet. Generate one above.</p>
        </div>
    @endif
</div>
