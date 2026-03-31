<div>
    @if (!$qualified_referal)
        <div class="max-w-3xl mx-auto px-4 py-16 text-center">
            <flux:callout variant="warning" icon="exclamation-triangle">
                <flux:callout.text>You need at least one purchase to use referrals. Visit the dashboard for details.</flux:callout.text>
            </flux:callout>
            <a href="{{ route('ngnclub.dashboard') }}" class="mt-6 inline-block text-sm font-medium text-brand-red underline">Back to dashboard</a>
        </div>
    @else
        <div class="bg-gray-900 text-white py-8 px-4">
            <div class="max-w-3xl mx-auto">
                <h1 class="text-2xl font-bold">NGN Club referral</h1>
                <a href="{{ route('ngnclub.dashboard') }}"
                    class="mt-4 inline-flex items-center gap-2 text-sm text-gray-300 hover:text-white border border-gray-600 px-4 py-2">
                    ← Back to dashboard
                </a>
            </div>
        </div>

        <div class="max-w-3xl mx-auto px-4 py-8 space-y-6">
            @if (session('success'))
                <flux:callout variant="success" icon="check-circle">
                    <flux:callout.text>{{ session('success') }}</flux:callout.text>
                </flux:callout>
            @endif

            @if (session('referral_link'))
                <flux:card class="p-6 border border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Your referral link</p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" readonly id="referralLinkField" value="{{ session('referral_link') }}"
                            class="flex-1 font-mono text-sm border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 px-3 py-2 text-gray-900 dark:text-white" />
                        <flux:button type="button" variant="filled" class="bg-brand-red text-white shrink-0"
                            x-on:click="navigator.clipboard.writeText(document.getElementById('referralLinkField').value); window.dispatchEvent(new CustomEvent('toast-show', { detail: { slots: { text: 'Link copied to clipboard.' }, dataset: { variant: 'success' } } }))">
                            Copy link
                        </flux:button>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="https://wa.me/?text={{ urlencode(session('referral_link')) }}" target="_blank" rel="noopener"
                            class="text-sm font-medium text-green-700 dark:text-green-400 underline">WhatsApp</a>
                        <a href="mailto:?subject={{ rawurlencode('Join NGN Club') }}&body={{ rawurlencode(session('referral_link')) }}"
                            class="text-sm font-medium text-brand-red underline">Email</a>
                        <a href="sms:?body={{ rawurlencode(session('referral_link')) }}"
                            class="text-sm font-medium text-gray-700 dark:text-gray-300 underline">SMS</a>
                    </div>
                </flux:card>
            @endif

            @if (! session('referral_link'))
                <flux:card class="p-6 border-2 border-brand-red">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 text-center">Referred person details</h2>
                    <form wire:submit="submit" class="space-y-4 max-w-md mx-auto">
                        <flux:field>
                            <flux:label>Full name</flux:label>
                            <flux:input wire:model="full_name" autocomplete="name" required />
                            <flux:error name="full_name" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Phone number</flux:label>
                            <flux:input wire:model="phone" type="tel" placeholder="07xxxxxxxxx" required />
                            <flux:error name="phone" />
                            <p class="text-xs text-gray-500 mt-1">Format: 07xxxxxxxxx</p>
                        </flux:field>
                        <flux:field>
                            <flux:label>Registration number</flux:label>
                            <flux:input wire:model="reg_number" class="uppercase" maxlength="20" required />
                            <flux:error name="reg_number" />
                        </flux:field>

                        <div class="border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 max-h-48 overflow-y-auto text-left text-sm text-gray-700 dark:text-gray-300">
                            <p class="font-bold mb-2">NGN Club referral — terms &amp; conditions</p>
                            <ul class="list-disc ps-5 space-y-2">
                                <li>Upon successful referral subscription and referred person&apos;s purchases worth £30 or more, you will receive a flat £5 credit in your account.</li>
                                <li>Members cannot refer themselves using alternate phone numbers. Any such attempts will be rejected.</li>
                                <li>Each referral is subject to audit for legitimacy before credit is granted.</li>
                                <li>Once approved, referral credits will be immediately available for use in your account.</li>
                                <li>Referral credits follow the same terms as regular NGN Club loyalty credits (non-transferable, expire after 6 months, exclusions apply).</li>
                                <li>NGN Club reserves the right to modify or terminate the referral programme at any time.</li>
                            </ul>
                        </div>

                        <div class="text-center pt-2">
                            <flux:button type="submit" variant="filled" class="bg-brand-red text-white" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="submit">Submit referral</span>
                                <span wire:loading wire:target="submit">Submitting…</span>
                            </flux:button>
                        </div>
                    </form>
                </flux:card>
            @endif
        </div>
    @endif
</div>
