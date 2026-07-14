<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="mb-1 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                <a href="{{ route('flux-admin.surveys.index') }}" class="transition hover:text-zinc-700 dark:hover:text-zinc-200">Surveys</a>
                <span>/</span>
                <span>Campaign</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Campaign — {{ $survey->title }}</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">WhatsApp and SMS reminder actions for this survey’s recipient list.</p>
        </div>
        <a href="{{ route('flux-admin.surveys.index') }}">
            <flux:button size="sm" variant="ghost" class="!rounded-none">Back</flux:button>
        </a>
    </div>

    <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column>SMS</flux:table.column>
                <flux:table.column>WhatsApp</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $row)
                    <flux:table.row wire:key="campaign-{{ $row['id'] }}">
                        <flux:table.cell>{{ $row['fullname'] }}</flux:table.cell>
                        <flux:table.cell class="text-xs">{{ $row['email'] }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs">{{ $row['phone'] }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$row['is_email_sent']" /></flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$row['is_sms_sent']" /></flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$row['is_whatsapp_sent']" /></flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-wrap items-center gap-1">
                                <a href="{{ $row['url_whatsapp'] }}" target="_blank" rel="noopener"
                                   wire:click="markWhatsAppSent({{ $row['id'] }})">
                                    <flux:button size="xs" variant="ghost" class="!rounded-none">WhatsApp</flux:button>
                                </a>
                                @unless($row['is_sms_sent'])
                                    <flux:button
                                        size="xs"
                                        variant="ghost"
                                        class="!rounded-none"
                                        wire:click="sendSmsReminder({{ $row['id'] }})"
                                        wire:confirm="Send a live SMS reminder to this contact?"
                                    >SMS</flux:button>
                                @endunless
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="py-8 text-center text-zinc-500">No campaign recipients.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="border-t border-zinc-200 p-3 dark:border-zinc-800">{{ $campaigns->links() }}</div>
    </div>
</div>
