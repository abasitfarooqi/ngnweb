<div class="max-w-4xl mx-auto space-y-6">
    {{-- Page Header --}}
    <div>
        <a href="{{ route('account.rentals.browse') }}" class="text-sm text-brand-red hover:text-red-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Browse
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Complete Your Rental Booking</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Review details and confirm your booking</p>
    </div>

    @if (session()->has('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Motorbike Details --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Selected Motorbike</h2>
        
        <div class="flex flex-col md:flex-row gap-6">
            {{-- Image --}}
            <div class="w-full md:w-1/3">
                @if ($motorbike->images->where('is_primary', true)->first())
                    <img src="{{ Storage::url($motorbike->images->where('is_primary', true)->first()->file_path) }}" 
                        alt="{{ $motorbike->make }} {{ $motorbike->model }}"
                        class="w-full h-48 object-cover rounded-lg">
                @else
                    <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ $motorbike->make }} {{ $motorbike->model }}
                </h3>
                <div class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <p><strong>Registration:</strong> {{ $motorbike->reg_no }}</p>
                    <p><strong>Year:</strong> {{ $motorbike->year }}</p>
                    <p><strong>Engine:</strong> {{ $motorbike->engine_cc }}cc</p>
                    <p><strong>Transmission:</strong> {{ $motorbike->transmission ?? 'Manual' }}</p>
                    <p><strong>Colour:</strong> {{ $motorbike->color ?? 'N/A' }}</p>
                    <p><strong>Branch:</strong> {{ $motorbike->branch->name ?? 'TBC' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Booking Form --}}
    <form wire:submit="createBooking">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Rental Details</h2>

            {{-- Start Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Start Date *
                </label>
                <input type="date" wire:model.live="start_date" min="{{ now()->format('Y-m-d') }}"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                @error('start_date') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            {{-- Rental Period --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Rental Period (Weeks) *
                </label>
                <select wire:model.live="rental_period"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                    <option value="1">1 Week</option>
                    <option value="2">2 Weeks</option>
                    <option value="4">4 Weeks (1 Month)</option>
                    <option value="8">8 Weeks (2 Months)</option>
                    <option value="12">12 Weeks (3 Months)</option>
                    <option value="26">26 Weeks (6 Months)</option>
                    <option value="52">52 Weeks (1 Year)</option>
                </select>
                @error('rental_period') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Additional Notes (Optional)
                </label>
                <textarea wire:model="notes" rows="3"
                    placeholder="Any special requests or information..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white"></textarea>
            </div>
        </div>

        {{-- Pricing Summary --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Summary</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Weekly Rent</span>
                    <span class="font-medium text-gray-900 dark:text-white">£{{ number_format($weekly_rent, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Security Deposit</span>
                    <span class="font-medium text-gray-900 dark:text-white">£{{ number_format($deposit, 2) }}</span>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                    <div class="flex justify-between">
                        <span class="text-base font-semibold text-gray-900 dark:text-white">Initial Payment</span>
                        <span class="text-xl font-bold text-brand-red">£{{ number_format($total_amount, 2) }}</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        First week + deposit. Deposit is refundable.
                    </p>
                </div>
            </div>

            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded">
                <p class="text-sm text-blue-800 dark:text-blue-200">
                    <strong>Recurring Payments:</strong> After the initial payment, £{{ number_format($weekly_rent, 2) }} will be charged weekly via automatic payment.
                </p>
            </div>
        </div>

        {{-- Terms & Conditions --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-start gap-3">
                <input type="checkbox" wire:model="agree_terms" id="agree_terms"
                    class="mt-1 h-4 w-4 text-brand-red border-gray-300 rounded focus:ring-brand-red">
                <label for="agree_terms" class="text-sm text-gray-700 dark:text-gray-300">
                    I agree to the <a href="#" class="text-brand-red hover:underline">rental terms and conditions</a>, 
                    and I understand that I am responsible for the motorbike during the rental period. I confirm that all my 
                    documents are up to date and that I have valid insurance.
                </label>
            </div>
            @error('agree_terms') <span class="text-sm text-red-600 block mt-2">{{ $message }}</span> @enderror
        </div>

        {{-- Submit Button --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('account.rentals.browse') }}" 
                class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Cancel
            </a>
            <button type="submit" 
                class="px-6 py-3 bg-brand-red text-white rounded-md hover:bg-red-700 transition flex items-center gap-2">
                <span>Proceed to Payment</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </form>
</div>
