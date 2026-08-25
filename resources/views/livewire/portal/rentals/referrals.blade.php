<div class="space-y-6">
    <div>
        <flux:heading size="xl">Refer a friend (rental)</flux:heading>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Genuine rental customers can refer someone who has not rented with us before.</p>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if(! $eligible && $redeemedFreeWeeks < 1)
        <flux:card class="p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">You can refer a friend after you have paid one weekly rental invoice.</p>
        </flux:card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <flux:card class="p-5">
                <p class="text-xs uppercase tracking-wide text-gray-500">Available points</p>
                <p class="mt-1 text-2xl font-semibold">{{ $availablePoints }}</p>
            </flux:card>
            <flux:card class="p-5">
                <p class="text-xs uppercase tracking-wide text-gray-500">Pending points</p>
                <p class="mt-1 text-2xl font-semibold">{{ $pendingPoints }}</p>
            </flux:card>
            <flux:card class="p-5">
                <p class="text-xs uppercase tracking-wide text-gray-500">Redeemed</p>
                <p class="mt-1 text-2xl font-semibold">{{ $redeemedPoints }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ $redeemedFreeWeeks }} free week{{ $redeemedFreeWeeks === 1 ? '' : 's' }} applied</p>
            </flux:card>
            <flux:card class="p-5">
                <p class="text-xs uppercase tracking-wide text-gray-500">Reward</p>
                <p class="mt-1 text-sm">{{ $pointsPerWeek }} points = 1 free week at your weekly rent when applied.</p>
            </flux:card>
        </div>

        @if(! $eligible)
            <p class="text-sm text-gray-600 dark:text-gray-400">A free week has already been applied on your hire. You can refer a friend after you have paid one weekly rental invoice.</p>
        @endif

        @if($eligible)
            @if($shareCode)
                <flux:card class="p-5">
                    <p class="text-sm font-medium">Your latest code</p>
                    <p class="mt-1 font-mono text-lg">{{ $shareCode }}</p>
                    <p class="mt-1 text-sm break-all">{{ $shareUrl }}</p>
                </flux:card>
            @endif

            <flux:card class="p-5 space-y-3">
                <p class="text-sm font-medium">Refer someone</p>
                <flux:input wire:model="name" placeholder="Their name" class="!rounded-none" />
                @error('name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                <flux:input wire:model="phone" placeholder="UK mobile starting 07" class="!rounded-none" />
                @error('phone') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                <flux:input wire:model="email" placeholder="Email (optional)" class="!rounded-none" />
                @error('email') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                <label class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" wire:model="acceptTerms" class="mt-1">
                    <span>I confirm I have permission to share this person’s details, and I have read and accept the rental referral terms below.</span>
                </label>
                @error('acceptTerms') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                <flux:button variant="primary" wire:click="submit" class="!rounded-none bg-brand-red text-white">Send referral</flux:button>
            </flux:card>
        @endif

        <div class="space-y-3">
            <h2 class="text-sm font-semibold">Your referrals</h2>
            @forelse($rows as $row)
                <flux:card class="p-4">
                    <div class="flex justify-between gap-3 flex-wrap">
                        <div>
                            <p class="font-medium">{{ $row->submitted_name }}</p>
                            <p class="text-xs text-gray-500">{{ $row->created_at?->format('d M Y') }} · {{ $row->referral_code }}</p>
                        </div>
                        <span class="text-sm">{{ $row->friendlyStatus() }}</span>
                    </div>
                </flux:card>
            @empty
                <p class="text-sm text-gray-500">You have not referred anyone yet.</p>
            @endforelse
        </div>
    @endif

    <section class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5 space-y-3 text-sm text-zinc-700 dark:text-zinc-300">
        <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Rental referral terms</h2>
        <p>These terms apply to the motorcycle rental referral programme operated by Neguinho Motors Ltd (trading as NGN Motors) (“we”, “us”). By sending a referral you enter a promotional arrangement. It is not a lottery, prize draw or cash reward scheme. Your hire agreement still applies in full.</p>

        <h3 class="font-semibold text-zinc-900 dark:text-white">1. Who may refer</h3>
        <p>You may refer only if you are a current or recent rental customer and you have at least one paid weekly rental invoice with us. Staff may refuse a referral if that is not shown on our records.</p>

        <h3 class="font-semibold text-zinc-900 dark:text-white">2. Who you may refer</h3>
        <ul class="list-disc pl-5 space-y-1">
            <li>The person must be a new rental customer. They must not have had a posted rental with us before the day you refer them.</li>
            <li>You must not refer yourself, a joint account in your name, or anyone using your phone, email or documents.</li>
            <li>You must not refer someone who is already matched to another live referral.</li>
            <li>You must have their permission to give us their name, UK mobile number and optional email. We will use those details to contact them about rentals and to match the referral. See our privacy notice for how we handle personal data.</li>
        </ul>

        <h3 class="font-semibold text-zinc-900 dark:text-white">3. Confirmation</h3>
        <ul class="list-disc pl-5 space-y-1">
            <li>We decide whether a referral is accepted, using our own rental records. Those records are final.</li>
            <li>Sending a referral does not create a right to a free week. The rentals desk will confirm if a reward applies.</li>
        </ul>

        <h3 class="font-semibold text-zinc-900 dark:text-white">4. The reward</h3>
        <ul class="list-disc pl-5 space-y-1">
            <li>{{ $pointsPerWeek }} points equal one free weekly rental charge on <strong>your</strong> unpaid weekly invoice, at the weekly rent then due. It is not cash and has no cash value. Staff direct free weeks count in the same running total.</li>
            <li>The reward is applied only to the referrer’s hire. It cannot pay the friend’s invoice, a deposit, other charges, PCNs, damage, recovery or arrears.</li>
            <li>One accepted referral = one free week, used once only. It cannot be split, transferred, sold or used on more than one invoice.</li>
            <li>Staff apply the free week. A real “rental referral reward” transaction marks that invoice paid. No money is taken for that week.</li>
        </ul>

        <h3 class="font-semibold text-zinc-900 dark:text-white">5. Refusal, reversal and abuse</h3>
        <p>We may refuse, hold, cancel or reverse a referral or a free week if we reasonably believe there is self-referral, an existing renter, a duplicate claim, a reversed or unpaid invoice, false details, or any abuse of the programme. If a qualifying payment is later reversed, we may cancel unused points or recover the value of a free week already applied, including from later invoices.</p>

        <h3 class="font-semibold text-zinc-900 dark:text-white">6. Changes and law</h3>
        <p>We may change or withdraw this programme at any time. A referral already approved will be honoured on the terms that applied when it was approved, unless we must cancel it under section 5. These terms are governed by the law of England and Wales. The courts of England and Wales have exclusive jurisdiction, except that we may bring a claim in your home court if you live elsewhere in the UK. Nothing here limits any right you cannot legally give up.</p>

        <p class="text-xs text-zinc-500">Last updated 22 August 2026. Questions: the rentals desk or customerservice@neguinhomotors.co.uk.</p>
    </section>
</div>
