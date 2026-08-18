<div class="space-y-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
        <div>
            <flux:heading size="xl">Sent communications</flux:heading>
            <flux:text class="mt-1">Actual messages recorded when Email or Internal Inbox is on. Policy changes stay on the definitions list.</flux:text>
        </div>
        <a href="{{ route('flux-admin.communications.index') }}">
            <flux:button size="sm" variant="ghost" icon="arrow-left" class="!rounded-none">Definitions</flux:button>
        </a>
    </div>

    @unless($schemaReady)
        <div class="border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">Communication tables are not migrated yet.</div>
    @else
        <x-flux-admin::data-table title="Message log" description="Email sent, skipped or failed, plus portal inbox delivery.">
            <x-slot:toolbar>
                <x-flux-admin::filter-bar search-placeholder="Search title, email or key..." />
            </x-slot:toolbar>

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Sent</flux:table.column>
                    <flux:table.column>Communication</flux:table.column>
                    <flux:table.column>Customer</flux:table.column>
                    <flux:table.column>Email</flux:table.column>
                    <flux:table.column>Inbox</flux:table.column>
                    <flux:table.column>Read</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($rows as $row)
                        @php($email = $row->deliveries->firstWhere('channel', 'email'))
                        @php($inbox = $row->deliveries->firstWhere('channel', 'internal_inbox'))
                        @php($recipient = $row->recipients->first())
                        <flux:table.row wire:key="sent-{{ $row->id }}">
                            <flux:table.cell class="text-sm text-zinc-600 dark:text-zinc-400">{{ $row->created_at?->format('d M Y H:i') }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $row->title }}</div>
                                <div class="mt-1 font-mono text-[11px] text-zinc-500">{{ $row->communication_key }}</div>
                            </flux:table.cell>
                            <flux:table.cell class="text-sm">{{ $row->recipient_email ?: '—' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="{{ ($email?->status ?? '') === 'sent' ? 'green' : (($email?->status ?? '') === 'failed' ? 'red' : 'zinc') }}">{{ $email?->status ?? 'none' }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="{{ ($inbox?->status ?? '') === 'delivered' ? 'green' : (($inbox?->status ?? '') === 'failed' ? 'red' : 'zinc') }}">{{ $inbox?->status ?? 'off' }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $recipient?->read_at?->format('d M Y H:i') ?? '—' }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <a href="{{ route('flux-admin.communications.sent.show', $row) }}">
                                    <flux:button size="xs" variant="ghost" class="!rounded-none">View</flux:button>
                                </a>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7" class="py-8 text-center text-sm text-zinc-500">No sent communications yet. Turn the system ON and send a transactional email with Email or Inbox enabled.</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

            <x-slot:pagination>
                {{ $rows->links() }}
            </x-slot:pagination>
        </x-flux-admin::data-table>
    @endunless
</div>
