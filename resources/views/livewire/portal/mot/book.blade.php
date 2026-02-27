<div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Book MOT Appointment</h1>

    @if (session()->has('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Appointment Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date of Appointment *</label>
                    <input type="date" wire:model="date_of_appointment"
                        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                    @error('date_of_appointment') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time Slot *</label>
                    <select wire:model="time_slot"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                        @foreach($timeSlots as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('time_slot') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Branch Location *</label>
                <select wire:model="branch_id"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                    <option value="">Select a branch</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                @error('branch_id') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4">Vehicle Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Registration Number *</label>
                        <input type="text" wire:model="motorbike_reg_no" placeholder="AB12 CDE"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white uppercase">
                        @error('motorbike_reg_no') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Make *</label>
                        <input type="text" wire:model="motorbike_make" placeholder="Honda"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                        @error('motorbike_make') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Model *</label>
                        <input type="text" wire:model="motorbike_model" placeholder="PCX 125"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                        @error('motorbike_model') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional Notes</label>
                <textarea wire:model="notes" rows="4" placeholder="Any special requirements or concerns..."
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white"></textarea>
                @error('notes') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-between items-center pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('account.bookings') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </a>
                <button type="submit" wire:loading.attr="disabled"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-brand-red hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-red disabled:opacity-50">
                    <span wire:loading.remove>Book Appointment</span>
                    <span wire:loading>Booking…</span>
                </button>
            </div>
        </div>
    </form>
</div>
