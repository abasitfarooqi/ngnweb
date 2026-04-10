<div class="space-y-6">
    <div>
        <flux:heading size="xl">Repair enquiry</flux:heading>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            This page only sends a <strong>general repair enquiry</strong> (same flow as other portal service enquiries). It does not reserve a workshop date.
        </p>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            To request a <strong>dated repairs appointment</strong> (saved to customer appointments and emailed), use
            <a href="{{ route('account.repairs.appointment') }}" class="text-brand-red font-medium underline decoration-brand-red/80 hover:text-brand-red-dark">Repairs appointment</a>.
        </p>
    </div>

    <div id="repair-enquiry" class="scroll-mt-24">
        <livewire:site.contact.service-booking
            :embedded="true"
            :repairs-enquiry-compact-mode="true"
            :portal-repairs-enquiry="true"
            embeddedHeading="Repair enquiry"
            initialServiceType="Motorcycle Repairs Enquiry"
            wire:key="portal-repairs-shared-enquiry-v2"
        />
    </div>
</div>
