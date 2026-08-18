<div class="space-y-6">
    @unless($schemaReady)
        <div class="border border-amber-300 bg-amber-50 p-5 text-sm text-amber-900 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-100">
            <flux:heading size="lg">Communication system tables are not migrated</flux:heading>
            <p class="mt-2">Legacy transactional email remains unchanged. This detail page is unavailable until the communication migration has run.</p>
            <div class="mt-3 font-mono text-xs">{{ implode(', ', $missingCommunicationTables) }}</div>
            <a href="{{ route('flux-admin.communications.index') }}" class="mt-4 inline-block">
                <flux:button size="sm" variant="ghost" icon="arrow-left" class="!rounded-none">Back</flux:button>
            </a>
        </div>
    @else
    @php($policy = $communicationDefinition->policy)

    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <flux:heading size="xl">{{ $communicationDefinition->name }}</flux:heading>
                @if($communicationDefinition->active)
                    <flux:badge color="blue">Managed</flux:badge>
                @else
                    <flux:badge color="zinc">Legacy</flux:badge>
                @endif
            </div>
            <flux:text class="mt-1 font-mono text-xs">{{ $communicationDefinition->key }}</flux:text>
            @if($communicationDefinition->email_class)
                <flux:text class="mt-1 break-all font-mono text-[11px] text-zinc-500 dark:text-zinc-400">{{ $communicationDefinition->email_class }}</flux:text>
            @endif
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('flux-admin.communications.sent.index') }}">
                <flux:button size="sm" variant="ghost" icon="inbox" class="!rounded-none">Sent log</flux:button>
            </a>
            <a href="{{ route('flux-admin.communications.index') }}">
                <flux:button size="sm" variant="ghost" icon="arrow-left" class="!rounded-none">Back to list</flux:button>
            </a>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-3">
        <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900 xl:col-span-2">
            <flux:heading size="lg">Template preview</flux:heading>
            <flux:text class="mt-1">
                Sample customer email. Uses a recent rental booking when one exists, otherwise dummy data — not an exact live send.
            </flux:text>
            @if($emailPreview['available'] ?? false)
                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm text-zinc-600 dark:text-zinc-400">
                    <div><span class="text-zinc-500">Subject:</span> {{ $emailPreview['subject'] }}</div>
                    <div class="font-mono text-xs">{{ $emailPreview['source'] }}</div>
                </div>
                <x-communication-email-snapshot class="mt-4" :html="$emailPreview['html'] ?? ''" />
            @else
                <div class="mt-4 border border-zinc-200 p-4 text-sm text-zinc-600 dark:border-zinc-700 dark:text-zinc-400">
                    {{ $emailPreview['error'] ?? 'Preview is not available for this communication yet.' }}
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <flux:heading size="lg">Policy controls</flux:heading>
                <flux:text class="mt-1 text-sm">Change how this communication behaves when the global system is ON.</flux:text>

                @unless($canManageCommunications)
                    <p class="mt-3 text-xs text-amber-700 dark:text-amber-300">You can view this page but cannot change policy.</p>
                @endunless

                <div class="mt-4 space-y-4 text-sm">
                    <div class="border-b border-zinc-200 pb-4 dark:border-zinc-800">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-medium text-zinc-900 dark:text-white">Mode</div>
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                    <strong>Managed</strong> — this panel controls channels.
                                    <strong>Legacy</strong> — ignored; old email code runs unchanged.
                                </p>
                            </div>
                            @if($canManageCommunications)
                                @if($communicationDefinition->active)
                                    <flux:button size="xs" variant="ghost" wire:click="toggleManaged" wire:confirm="Return to legacy mode? The communication system will ignore this email." class="!rounded-none">
                                        <flux:badge color="blue">Managed</flux:badge>
                                    </flux:button>
                                @else
                                    <flux:button size="xs" variant="ghost" wire:click="toggleManaged" wire:confirm="Add back into the communication system?" class="!rounded-none">
                                        <flux:badge color="zinc">Legacy</flux:badge>
                                    </flux:button>
                                @endif
                            @else
                                <flux:badge color="{{ $communicationDefinition->active ? 'blue' : 'zinc' }}">{{ $communicationDefinition->active ? 'Managed' : 'Legacy' }}</flux:badge>
                            @endif
                        </div>
                    </div>

                    @foreach([
                        'email_enabled' => ['label' => 'Email', 'help' => 'Sends a real email to the customer. Turning OFF stops delivery through email (Super Admin only).'],
                        'internal_inbox_enabled' => ['label' => 'Internal Inbox', 'help' => 'Stores a copy in the customer portal inbox. Does not send email by itself.'],
                        'web_push_enabled' => ['label' => 'Web push', 'help' => 'Browser push notification when that channel is wired up.'],
                        'mobile_push_enabled' => ['label' => 'Mobile push', 'help' => 'Mobile app push when that channel is wired up.'],
                    ] as $field => $meta)
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $meta['label'] }}</div>
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $meta['help'] }}</p>
                            </div>
                            @if($canManageCommunications)
                                @php($isOn = (bool) ($policy?->{$field} ?? ($field === 'email_enabled')))
                                @if($field === 'email_enabled' && $isOn && ! $canDisableEmail)
                                    <x-flux-admin::status-badge :status="true" />
                                @elseif($field === 'email_enabled' && $isOn)
                                    <flux:button size="xs" variant="ghost" wire:click="togglePolicy('{{ $field }}')" wire:confirm="Disable real email for this communication?" class="!rounded-none">
                                        <x-flux-admin::status-badge :status="true" />
                                    </flux:button>
                                @else
                                    <flux:button size="xs" variant="ghost" wire:click="togglePolicy('{{ $field }}')" class="!rounded-none">
                                        <x-flux-admin::status-badge :status="$isOn" />
                                    </flux:button>
                                @endif
                            @else
                                <x-flux-admin::status-badge :status="(bool) ($policy?->{$field} ?? ($field === 'email_enabled'))" />
                            @endif
                        </div>
                    @endforeach

                    <div class="border-t border-zinc-200 pt-4 dark:border-zinc-800">
                        <div class="font-medium text-zinc-900 dark:text-white">Priority</div>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Inbox ordering and urgency when multiple messages exist.</p>
                        @if($canManageCommunications)
                            <select
                                class="mt-2 w-full border border-zinc-300 bg-white px-2 py-1.5 text-sm text-zinc-900 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                                wire:change="updatePriority($event.target.value)"
                            >
                                @foreach([
                                    'critical' => 'Critical — legal, safety, payment failure',
                                    'important' => 'Important — core transactional (bookings, receipts)',
                                    'normal' => 'Normal — standard updates',
                                    'informational' => 'Informational — low urgency',
                                ] as $value => $label)
                                    <option value="{{ $value }}" @selected(($policy?->priority ?? $communicationDefinition->priority) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        @else
                            <div class="mt-2 font-medium capitalize text-zinc-900 dark:text-white">{{ $policy?->priority ?? $communicationDefinition->priority }}</div>
                        @endif
                    </div>

                    @if($policy?->mandatory)
                        <p class="text-xs text-amber-700 dark:text-amber-300">Mandatory — at least Email or Internal Inbox must stay ON.</p>
                    @endif
                </div>
            </div>

            <div class="border border-zinc-200 bg-zinc-50 p-4 text-xs text-zinc-600 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400">
                <div class="font-medium text-zinc-900 dark:text-white">When settings apply</div>
                <ul class="mt-2 list-disc space-y-1 pl-4">
                    <li>Global system <strong>OFF</strong> — legacy email always runs; this panel is bypassed.</li>
                    <li>Global system <strong>ON</strong> + <strong>Managed</strong> — toggles above control delivery.</li>
                    <li><strong>Legacy</strong> mode — this email is excluded even if global is ON.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900 lg:col-span-2">
            <flux:heading size="lg">Technical metadata</flux:heading>
            <dl class="mt-4 grid gap-3 text-sm md:grid-cols-2">
                <div>
                    <dt class="text-zinc-500 dark:text-zinc-400">Classification</dt>
                    <dd class="mt-1 text-zinc-900 dark:text-white">{{ $communicationDefinition->classification }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500 dark:text-zinc-400">Category</dt>
                    <dd class="mt-1 text-zinc-900 dark:text-white">{{ $communicationDefinition->category }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500 dark:text-zinc-400">Template shell</dt>
                    <dd class="mt-1 break-all font-mono text-xs text-zinc-900 dark:text-white">{{ $communicationDefinition->template_view ?: 'Not declared' }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500 dark:text-zinc-400">Recipient rule</dt>
                    <dd class="mt-1 text-zinc-900 dark:text-white">{{ $communicationDefinition->recipient_summary ?: 'Not declared' }}</dd>
                </div>
            </dl>
            @if($communicationDefinition->description)
                <div class="mt-5 border-t border-zinc-200 pt-4 text-sm text-zinc-700 dark:border-zinc-800 dark:text-zinc-300">
                    {{ $communicationDefinition->description }}
                </div>
            @endif
        </div>

        <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <flux:heading size="lg">Priority guide</flux:heading>
            <dl class="mt-4 space-y-3 text-sm">
                <div>
                    <dt class="font-medium text-red-700 dark:text-red-400">Critical</dt>
                    <dd class="mt-1 text-zinc-600 dark:text-zinc-400">Payment failures, legal notices, safety. Shown first in inbox.</dd>
                </div>
                <div>
                    <dt class="font-medium text-amber-700 dark:text-amber-400">Important</dt>
                    <dd class="mt-1 text-zinc-600 dark:text-zinc-400">Bookings, agreements, receipts — core transactional messages.</dd>
                </div>
                <div>
                    <dt class="font-medium text-zinc-900 dark:text-white">Normal</dt>
                    <dd class="mt-1 text-zinc-600 dark:text-zinc-400">Standard customer updates they expect but are not urgent.</dd>
                </div>
                <div>
                    <dt class="font-medium text-zinc-500 dark:text-zinc-400">Informational</dt>
                    <dd class="mt-1 text-zinc-600 dark:text-zinc-400">Low urgency; can sit behind other messages.</dd>
                </div>
            </dl>
        </div>
    </div>

    <x-flux-admin::data-table title="Recent policy audit" description="Latest staff-controlled changes for this communication definition.">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Staff</flux:table.column>
                <flux:table.column>Event</flux:table.column>
                <flux:table.column>Field</flux:table.column>
                <flux:table.column>Old</flux:table.column>
                <flux:table.column>New</flux:table.column>
                <flux:table.column>Reason</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($audits as $audit)
                    <flux:table.row wire:key="comm-audit-{{ $audit->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $audit->created_at?->format('d M Y H:i') }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $audit->actorLabel() }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $audit->event }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $audit->field }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $audit->old_value }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $audit->new_value }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $audit->reason }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="py-8 text-center text-zinc-500 dark:text-zinc-400">No policy audit entries yet.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </x-flux-admin::data-table>
    @endunless
</div>
