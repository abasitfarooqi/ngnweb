{{--
    INACTIVE ARCHIVE: original portal multi-step finance apply markup (never rendered).
    Kept for reference. Live route redirects to account finance browse enquiry panel.
--}}
<div wire:key="finance-apply-page">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Apply for Finance</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Complete the application to finance your motorbike.</p>
    </div>

    {{-- Progress Steps --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            @foreach ([1 => 'Customer Info', 2 => 'Financial Details', 3 => 'Review'] as $num => $label)
                <div class="flex items-center {{ $num < 3 ? 'flex-1' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step >= $num ? 'bg-brand-red text-white' : 'bg-gray-300 text-gray-600' }}">
                        {{ $num }}
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $step >= $num ? 'text-gray-900 dark:text-white' : 'text-gray-500' }}">{{ $label }}</span>
                    @if ($num < 3)
                        <div class="flex-1 h-1 mx-4 {{ $step > $num ? 'bg-brand-red' : 'bg-gray-300' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Motorbike Summary --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
        <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-2">Selected Motorbike</h3>
        <p class="text-lg font-bold text-blue-900 dark:text-blue-100">{{ $motorbike->make }} {{ $motorbike->model }}</p>
        <p class="text-sm text-blue-800 dark:text-blue-300">{{ $motorbike->reg_no }} • {{ $motorbike->branch->name ?? 'N/A' }}</p>
    </div>

    <form wire:submit.prevent="submit">
        @if ($step === 1)
            {{-- Step 1: Customer Info --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Your Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name *</label>
                        <input type="text" wire:model="first_name" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        @error('first_name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name *</label>
                        <input type="text" wire:model="last_name" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        @error('last_name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email *</label>
                        <input type="email" wire:model="email" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        @error('email') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone *</label>
                        <input type="tel" wire:model="phone" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        @error('phone') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address *</label>
                        <input type="text" wire:model="address" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        @error('address') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Postcode *</label>
                        <input type="text" wire:model="postcode" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        @error('postcode') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City</label>
                        <input type="text" wire:model="city"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" wire:click="nextStep"
                        class="px-6 py-2 bg-brand-red text-white font-medium rounded hover:bg-red-700">
                        Next →
                    </button>
                </div>
            </div>

        @elseif ($step === 2)
            {{-- Step 2: Financial Details --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Financial Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deposit (£) *</label>
                        <input type="number" wire:model.live="deposit" required min="0" step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        @error('deposit') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Minimum 10% of bike price</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Frequency</label>
                        <select wire:model="is_monthly"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                            <option value="0">Weekly</option>
                            <option value="1">Monthly</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Extra Items (Optional)</label>
                        <textarea wire:model="extra_items" rows="2" placeholder="Insurance, accessories, etc."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Extra Cost (£)</label>
                        <input type="number" wire:model="extra" min="0" step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <div class="flex justify-between space-x-3 pt-4">
                    <button type="button" wire:click="previousStep"
                        class="px-6 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                        ← Back
                    </button>
                    <button type="button" wire:click="nextStep"
                        class="px-6 py-2 bg-brand-red text-white font-medium rounded hover:bg-red-700">
                        Next →
                    </button>
                </div>
            </div>

        @elseif ($step === 3)
            {{-- Step 3: Review & Submit --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Review Your Application</h2>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Customer:</span>
                        <span class="text-sm font-medium">{{ $first_name }} {{ $last_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Email:</span>
                        <span class="text-sm font-medium">{{ $email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Deposit:</span>
                        <span class="text-sm font-medium">£{{ number_format($deposit, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Payment Frequency:</span>
                        <span class="text-sm font-medium">{{ $is_monthly ? 'Monthly' : 'Weekly' }}</span>
                    </div>
                </div>

                <div class="flex justify-between space-x-3 pt-4">
                    <button type="button" wire:click="previousStep"
                        class="px-6 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                        ← Back
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white font-medium rounded hover:bg-green-700">
                        Submit Application
                    </button>
                </div>
            </div>
        @endif
    </form>
</div>
