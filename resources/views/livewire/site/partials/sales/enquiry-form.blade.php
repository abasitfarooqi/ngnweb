@props([
    'submitAction' => 'submitEnquiry',
    'heading' => 'Order / Enquiry',
    'enquiryTypeLabel' => null,
    'showRegNo' => false,
    'submitButtonLabel' => 'Submit enquiry',
    'privacyPolicyUrl' => null,
])

@php
    $privacyUrl = $privacyPolicyUrl ?? route('site.cookies');
@endphp

<div class="space-y-4 border border-gray-200 bg-white p-5 dark:border-gray-600 dark:bg-gray-800">
    <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $heading }}</h3>
        @if ($enquiryTypeLabel)
            <span class="inline-flex self-start border border-brand-red px-2 py-0.5 text-xs font-semibold uppercase tracking-wide text-brand-red dark:border-red-500 dark:text-red-200">
                {{ $enquiryTypeLabel }}
            </span>
        @endif
    </div>

    @if (session()->has('enquiry_success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.text>{{ session('enquiry_success') }}</flux:callout.text>
        </flux:callout>
    @endif

    <form wire:submit.prevent="{{ $submitAction }}" class="space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <flux:field>
                <flux:label>Full name</flux:label>
                <flux:input wire:model.defer="name" autocomplete="name" />
                <flux:error name="name" />
            </flux:field>
            <flux:field>
                <flux:label>Phone</flux:label>
                <flux:input wire:model.defer="phone" type="tel" autocomplete="tel" />
                <flux:error name="phone" />
            </flux:field>
        </div>

        <flux:field>
            <flux:label>Email</flux:label>
            <flux:input wire:model.defer="email" type="email" autocomplete="email" />
            <flux:error name="email" />
        </flux:field>

        @if ($showRegNo)
            <flux:field>
                <flux:label>Registration number (optional)</flux:label>
                <flux:input wire:model.defer="reg_no" class="uppercase" maxlength="20" />
                <flux:error name="reg_no" />
            </flux:field>
        @endif

        <flux:field>
            <flux:label>Message</flux:label>
            <flux:textarea wire:model.defer="message" rows="4" />
            <flux:error name="message" />
        </flux:field>

        <label class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-300">
            <input type="checkbox" wire:model="privacy" class="mt-1 border border-gray-300 text-brand-red focus:ring-brand-red focus:ring-offset-0 dark:border-gray-500 dark:bg-gray-900 dark:focus:ring-brand-red">
            <span>
                I have read and agree to the
                <a href="{{ $privacyUrl }}" target="_blank" rel="noopener" class="font-medium text-brand-red underline hover:no-underline dark:text-red-300">cookie and privacy policy</a>.
            </span>
        </label>
        <flux:error name="privacy" />

        <flux:button type="submit" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">
            {{ $submitButtonLabel }}
        </flux:button>
    </form>
</div>
