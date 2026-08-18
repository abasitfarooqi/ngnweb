<div class="space-y-6">
    <div class="border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <flux:heading size="xl">Transactional Communication System</flux:heading>
                    @if($emergencyBypass)
                        <flux:badge color="red">Emergency bypass</flux:badge>
                    @elseif($enabled)
                        <flux:badge color="green">Active</flux:badge>
                    @else
                        <flux:badge color="zinc">Legacy mode</flux:badge>
                    @endif
                </div>
                <flux:text class="mt-2 max-w-3xl">
                    When OFF, this control layer is bypassed and existing transactional email behaviour remains active. OFF does not stop customer emails.
                </flux:text>
                @if(\App\Support\FluxAdminAccess::isSuperAdmin())
                    <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                        Hidden from global search. To give one staff member temporary access, assign the <span class="font-mono">manage-communications</span> permission on their user record. Remove it to lock them out again (403).
                    </p>
                @endif
                @unless($schemaReady)
                    <div class="mt-4 border border-amber-300 bg-amber-50 p-3 text-sm text-amber-900 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-100">
                        Communication system tables have not been migrated yet. Legacy transactional email remains unchanged, but this control panel is read-only until the migration runs.
                        <div class="mt-2 font-mono text-xs">{{ implode(', ', $missingCommunicationTables) }}</div>
                    </div>
                @endunless
            </div>

            <div class="w-full lg:max-w-md">
                <flux:textarea wire:model="switchReason" rows="2" placeholder="Reason for global switch change" :disabled="! $canToggleGlobal || $emergencyBypass || ! $schemaReady" />
                <div class="mt-3 flex flex-wrap justify-end gap-2">
                    @if($enabled)
                        <flux:button
                            size="sm"
                            variant="danger"
                            icon="power"
                            wire:click="disableSystem"
                            wire:confirm="Turn the Transactional Communication System OFF? This does not stop customer emails; it returns handling to legacy email behaviour."
                            :disabled="! $canToggleGlobal || $emergencyBypass || ! $schemaReady"
                            class="!rounded-none"
                        >Turn System Off</flux:button>
                    @else
                        <flux:button
                            size="sm"
                            variant="primary"
                            icon="power"
                            wire:click="enableSystem"
                            wire:confirm="Turn the Transactional Communication System ON? Existing definitions preserve Email ON unless staff explicitly changed them."
                            :disabled="! $canToggleGlobal || $emergencyBypass || ! $schemaReady"
                            class="!rounded-none"
                        >Turn System On</flux:button>
                    @endif
                </div>
                @unless($canToggleGlobal)
                    <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Only Super Admin can change the global power switch.</p>
                @endunless
            </div>
        </div>
    </div>

    <x-flux-admin::data-table title="Communication definitions" description="Transactional customer communications only. Campaign and marketing systems are excluded.">
        <x-slot:actions>
            <a href="{{ route('flux-admin.communications.sent.index') }}">
                <flux:button size="sm" variant="ghost" icon="inbox" class="!rounded-none">Sent log</flux:button>
            </a>
            <flux:button size="sm" variant="ghost" icon="arrow-path" wire:click="$refresh" class="!rounded-none">Refresh</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search name, key, category or class...">
                <div class="min-w-0 w-full sm:min-w-[11rem] sm:flex-1 lg:w-44 lg:flex-none">
                    <flux:select wire:model.live="filters.classification" placeholder="Type">
                        <flux:select.option value="">Any type</flux:select.option>
                        <flux:select.option value="transactional">Transactional</flux:select.option>
                        <flux:select.option value="internal">Internal</flux:select.option>
                        <flux:select.option value="auth">Auth</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[11rem] sm:flex-1 lg:w-44 lg:flex-none">
                    <flux:select wire:model.live="filters.channel" placeholder="Channel">
                        <flux:select.option value="">Any channel</flux:select.option>
                        <flux:select.option value="managed">Managed</flux:select.option>
                        <flux:select.option value="legacy">Legacy</flux:select.option>
                        <flux:select.option value="email_on">Email ON</flux:select.option>
                        <flux:select.option value="inbox_on">Internal Inbox ON</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Communication</flux:table.column>
                <flux:table.column>Key</flux:table.column>
                <flux:table.column>Category</flux:table.column>
                <flux:table.column>Mode</flux:table.column>
                <flux:table.column>Priority</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column>Inbox</flux:table.column>
                <flux:table.column>Web push</flux:table.column>
                <flux:table.column>Mobile push</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($definitions as $definition)
                    @php($policy = $definition->policy)
                    <flux:table.row wire:key="comm-def-{{ $definition->id }}">
                        <flux:table.cell>
                            <div class="font-medium text-zinc-900 dark:text-white">{{ $definition->name }}</div>
                            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $definition->classification }}</div>
                            <div class="mt-1 max-w-md truncate font-mono text-[11px] text-zinc-500 dark:text-zinc-500">
                                {{ $definition->email_class ?: $definition->source_class ?: 'No source declared' }}
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $definition->key }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $definition->category }}</flux:table.cell>
                        <flux:table.cell>
                            @if($definition->active)
                                <flux:button
                                    size="xs"
                                    variant="ghost"
                                    wire:click="toggleManaged({{ $definition->id }})"
                                    wire:confirm="Return {{ $definition->name }} to legacy mode? The new communication rules will ignore it and the current email path will behave as before."
                                    class="!rounded-none"
                                >
                                    <flux:badge color="blue">Managed</flux:badge>
                                </flux:button>
                            @else
                                <flux:button
                                    size="xs"
                                    variant="ghost"
                                    wire:click="toggleManaged({{ $definition->id }})"
                                    wire:confirm="Add {{ $definition->name }} back into the Transactional Communication System? It will still keep Email ON by default unless staff changes it."
                                    class="!rounded-none"
                                >
                                    <flux:badge color="zinc">Legacy</flux:badge>
                                </flux:button>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <select
                                class="border border-zinc-300 bg-white px-2 py-1 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                                wire:change="updatePriority({{ $definition->id }}, $event.target.value)"
                            >
                                @foreach(['critical' => 'Critical', 'important' => 'Important', 'normal' => 'Normal', 'informational' => 'Informational'] as $value => $label)
                                    <option value="{{ $value }}" @selected(($policy?->priority ?? $definition->priority) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($policy?->email_enabled ?? true)
                                <flux:button
                                    size="xs"
                                    variant="ghost"
                                    wire:click="togglePolicy({{ $definition->id }}, 'email_enabled')"
                                    wire:confirm="Disable real email for {{ $definition->name }}? Customers will no longer receive this communication through email unless another channel remains enabled."
                                    class="!rounded-none"
                                >
                                    <x-flux-admin::status-badge :status="true" />
                                </flux:button>
                            @else
                                <flux:button
                                    size="xs"
                                    variant="ghost"
                                    wire:click="togglePolicy({{ $definition->id }}, 'email_enabled')"
                                    class="!rounded-none"
                                >
                                    <x-flux-admin::status-badge :status="false" />
                                </flux:button>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" wire:click="togglePolicy({{ $definition->id }}, 'internal_inbox_enabled')" class="!rounded-none">
                                <x-flux-admin::status-badge :status="(bool) ($policy?->internal_inbox_enabled ?? false)" />
                            </flux:button>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" wire:click="togglePolicy({{ $definition->id }}, 'web_push_enabled')" class="!rounded-none">
                                <x-flux-admin::status-badge :status="(bool) ($policy?->web_push_enabled ?? false)" />
                            </flux:button>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" wire:click="togglePolicy({{ $definition->id }}, 'mobile_push_enabled')" class="!rounded-none">
                                <x-flux-admin::status-badge :status="(bool) ($policy?->mobile_push_enabled ?? false)" />
                            </flux:button>
                        </flux:table.cell>
                        <flux:table.cell>
                            <a href="{{ route('flux-admin.communications.show', $definition) }}">
                                <flux:button size="xs" variant="ghost" icon="eye" class="!rounded-none">View</flux:button>
                            </a>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="10" class="py-10 text-center text-zinc-500 dark:text-zinc-400">
                            @if($schemaReady)
                                No transactional communication definitions have been synchronized yet.
                            @else
                                Communication tables are not available yet. Run the migration before synchronizing definitions.
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $definitions->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <x-flux-admin::data-table title="Excluded or legacy email areas" description="These are intentionally not controlled by this transactional customer communication panel.">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Email area</flux:table.column>
                <flux:table.column>Classification</flux:table.column>
                <flux:table.column>Reason</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach($excludedInventory as $item)
                    <flux:table.row wire:key="excluded-{{ md5((string) ($item['name'] ?? '')) }}">
                        <flux:table.cell class="font-medium text-zinc-900 dark:text-white">{{ $item['name'] ?? 'Unknown' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="zinc">{{ $item['type'] ?? 'Excluded' }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $item['reason'] ?? '' }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </x-flux-admin::data-table>
</div>
