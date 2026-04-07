<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Check Your MOT Status</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Enter your registration to check when your MOT expires.</p>

    <form wire:submit="checkMOT" class="space-y-4">
        <flux:field>
            <flux:label>Registration number</flux:label>
            <flux:input
                wire:model="regNo"
                placeholder="AB12 CDE"
                class="uppercase tracking-widest font-bold text-center text-xl bg-yellow-100 border-2 border-yellow-400 text-black"
                style="font-size:1.5rem;"
            />
            <flux:error name="regNo" />
        </flux:field>
        <flux:field>
            <flux:label>Email (optional)</flux:label>
            <flux:input type="email" wire:model="notifyEmail" placeholder="Save this check to our records" autocomplete="email" />
            <flux:error name="notifyEmail" />
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">If you enter an email, we store your registration and next MOT date after a successful DVLA lookup.</p>
        </flux:field>
        <flux:button type="submit" variant="filled" size="base" class="w-full bg-brand-red text-white hover:bg-brand-red-dark" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="checkMOT">Check MOT status</span>
            <span wire:loading wire:target="checkMOT">Checking…</span>
        </flux:button>
    </form>

    @if($motData)
        <flux:callout variant="info" icon="information-circle" class="mt-5">
            <flux:callout.heading>MOT status for {{ $motData['registration'] }}</flux:callout.heading>
            <flux:callout.text class="text-sm space-y-1">
                @if(!empty($motData['make']))
                    <p><span class="font-semibold">Make:</span> {{ $motData['make'] }}</p>
                @endif
                <p><span class="font-semibold">MOT status:</span> {{ $motData['mot_status'] }}</p>
                <p><span class="font-semibold">MOT expires:</span> {{ $motData['mot_expiry'] }}</p>
                <p><span class="font-semibold">Road tax status:</span> {{ $motData['tax_status'] }}</p>
                @if(!empty($motData['tax_due']))
                    <p><span class="font-semibold">Tax due:</span> {{ $motData['tax_due'] }}</p>
                @endif
            </flux:callout.text>
        </flux:callout>
    @endif

    @if($error)
        <flux:callout variant="danger" icon="x-circle" class="mt-5">
            <flux:callout.text>{{ $error }}</flux:callout.text>
        </flux:callout>
    @endif
</div>
