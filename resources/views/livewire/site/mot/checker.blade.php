<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Check Your MOT Status</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Enter your registration to check when your MOT expires.</p>

    <form wire:submit="checkMOT" class="space-y-4">
        <flux:field>
            <flux:label>Registration Number</flux:label>
            <flux:input
                wire:model="regNo"
                placeholder="AB12 CDE"
                class="uppercase tracking-widest font-bold text-center text-xl bg-yellow-100 border-2 border-yellow-400 text-black"
                style="font-size:1.5rem;"
            />
            <flux:error name="regNo" />
        </flux:field>
        <flux:button type="submit" variant="filled" size="base" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">
            Check MOT Status
        </flux:button>
    </form>

    @if($motData)
        <flux:callout variant="info" icon="information-circle" class="mt-5">
            <flux:callout.heading>MOT Status for {{ $motData['registration'] }}</flux:callout.heading>
            <flux:callout.text>{{ $motData['mot_expiry'] }}</flux:callout.text>
        </flux:callout>
    @endif

    @if($error)
        <flux:callout variant="danger" icon="x-circle" class="mt-5">
            <flux:callout.text>{{ $error }}</flux:callout.text>
        </flux:callout>
    @endif
</div>
