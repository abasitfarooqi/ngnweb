<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="flex size-12 items-center justify-center rounded-2xl bg-zinc-950 text-white shadow-sm dark:bg-white dark:text-zinc-950"><flux:icon name="chat-bubble-left-right" class="size-6" /></div>
            <div><flux:heading size="xl">Start a customer chat</flux:heading><flux:text class="mt-1">Find an active portal user and continue their permanent NGN conversation.</flux:text></div>
        </div>
        <a href="{{ route('flux-admin.support-inbox.index') }}"><flux:button variant="ghost" icon="inbox">Open inbox</flux:button></a>
    </div>

    <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <flux:input wire:model.live.debounce.350ms="search" icon="magnifying-glass" placeholder="Search name, email, phone, WhatsApp, licence or username…" autofocus />
        <p class="mt-2 text-xs text-zinc-500">Only active customers with active portal authentication are shown. Document sharing remains controlled by NGN upload access.</p>
    </div>

    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <flux:table>
            <flux:table.columns><flux:table.column>Customer</flux:table.column><flux:table.column>Contact</flux:table.column><flux:table.column>Portal</flux:table.column><flux:table.column>Documents</flux:table.column><flux:table.column>Action</flux:table.column></flux:table.columns>
            <flux:table.rows>
                @forelse($customers as $customer)
                    <flux:table.row wire:key="chat-customer-{{ $customer->id }}">
                        <flux:table.cell><div class="font-medium text-zinc-900 dark:text-white">{{ $customer->full_name }}</div><div class="text-xs text-zinc-500">Customer #{{ $customer->id }} · Portal #{{ $customer->customerAuth?->id }}</div></flux:table.cell>
                        <flux:table.cell><div>{{ $customer->customerAuth?->email ?: $customer->email ?: '—' }}</div><div class="text-xs text-zinc-500">{{ $customer->phone ?: $customer->whatsapp ?: 'No phone' }}</div></flux:table.cell>
                        <flux:table.cell><flux:badge color="green">Active</flux:badge><div class="mt-1 text-xs text-emerald-600">{{ $customer->customerAuth?->email_verified_at ? 'Email verified' : 'Email not verified' }}</div></flux:table.cell>
                        <flux:table.cell><flux:badge color="{{ $customer->portal_upload_access ? 'green' : 'zinc' }}">{{ $customer->portal_upload_access ? 'Allowed' : 'Restricted' }}</flux:badge></flux:table.cell>
                        <flux:table.cell><flux:button size="sm" wire:click="startChat({{ $customer->customerAuth->id }})" icon="chat-bubble-left-right" variant="primary">Open chat</flux:button></flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="5" class="py-12 text-center text-zinc-500">{{ $term !== '' ? 'No active portal users match your search.' : 'Search for a portal user to start a chat.' }}</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
