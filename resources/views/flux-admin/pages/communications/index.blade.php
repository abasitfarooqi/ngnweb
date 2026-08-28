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
                @if($canToggleGlobal)
                    <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                        Hidden from global search. Super Admin always has access. Others need manage-communications for this control panel, and view-notifications for the Notifications page. Those rights are separate.
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

    @if($canToggleGlobal)
        <div class="overflow-hidden border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <flux:heading size="lg">Temporary access</flux:heading>
            <flux:text class="mt-2 max-w-3xl">
                Super Admin can grant this to any account in Users — Admin, Super Admin, or a normal person with no admin role. Communications is the control panel. Notifications is the sent/received log. They are separate. It does not make them an Admin. Remove it when the work is finished.
            </flux:text>

            <form wire:submit="grantTemporaryAccess" class="mt-4 space-y-3">
                <flux:input
                    wire:model.live.debounce.400ms="grantUserSearch"
                    placeholder="Search name, email or username…"
                    icon="magnifying-glass"
                />
                <select
                    wire:model="grantAccessKind"
                    class="w-full border border-zinc-300 bg-white px-2 py-2 text-sm text-zinc-900 !rounded-none focus:border-zinc-600 focus:outline-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                >
                    <option value="communications">Communications control panel</option>
                    <option value="notifications">Notifications page only</option>
                </select>
                <select
                    wire:model="grantAccessUserId"
                    size="4"
                    class="comms-grant-user-list w-full border border-zinc-300 bg-white px-2 py-1 text-sm text-zinc-900 !rounded-none focus:border-zinc-600 focus:outline-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                >
                    @forelse($grantableUsers as $grantableUser)
                        <option value="{{ $grantableUser->id }}">
                            {{ trim(($grantableUser->first_name ?? '').' '.($grantableUser->last_name ?? '')) ?: ($grantableUser->name ?: $grantableUser->email) }}
                            — {{ $grantableUser->email }}
                            @if($grantableUser->username) · {{ $grantableUser->username }} @endif
                            · {{ $grantableUser->is_admin ? 'Admin' : 'User' }}
                        </option>
                    @empty
                        <option value="" disabled>No users match that search.</option>
                    @endforelse
                </select>
                @error('grantAccessUserId')
                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <div class="flex flex-wrap items-center justify-between gap-2 bg-white py-1 dark:bg-zinc-900">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Four names visible. Scroll that list, or type to search.
                    </p>
                    <flux:button type="submit" size="sm" variant="primary" class="!rounded-none" :disabled="$grantableUsers->isEmpty()">Grant access</flux:button>
                </div>
            </form>

            @if($temporaryAccessUsers->isNotEmpty())
                <div class="mt-4 divide-y divide-zinc-200 border border-zinc-200 dark:divide-zinc-800 dark:border-zinc-800">
                    @foreach($temporaryAccessUsers as $accessUser)
                        <div class="flex flex-wrap items-center justify-between gap-2 px-3 py-2" wire:key="comms-access-{{ $accessUser->id }}">
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ trim(($accessUser->first_name ?? '').' '.($accessUser->last_name ?? '')) ?: ($accessUser->name ?: $accessUser->email) }}
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $accessUser->email }}</div>
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @foreach($accessUser->permissions as $granted)
                                        <flux:badge color="zinc">{{ $granted->name === 'view-notifications' ? 'Notifications' : 'Communications' }}</flux:badge>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-1">
                                @foreach($accessUser->permissions as $granted)
                                    <flux:button
                                        size="xs"
                                        variant="danger"
                                        class="!rounded-none"
                                        wire:click="revokeTemporaryAccess({{ $accessUser->id }}, '{{ $granted->name }}')"
                                        wire:confirm="Remove {{ $granted->name }} for this user?"
                                    >Remove {{ $granted->name === 'view-notifications' ? 'Notifications' : 'Communications' }}</flux:button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">No temporary access is currently granted.</p>
            @endif
        </div>
    @endif

    <x-flux-admin::data-table title="Communication definitions" description="Transactional one-to-one emails only. Bulk, campaign and Saturday cron reports stay out of this list. The alias is the plain-English name staff should use.">
        <x-slot:actions>
            @if($canViewNotifications)
                <a href="{{ route('flux-admin.communications.sent.index') }}">
                    <flux:button size="sm" variant="ghost" icon="inbox" class="!rounded-none">Notifications</flux:button>
                </a>
            @endif
            <flux:button size="sm" variant="ghost" icon="arrow-path" wire:click="syncCatalogue" class="!rounded-none">Sync list</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search name, key, category or class...">
                <div class="min-w-0 w-full">
                    <select wire:model.live="filters.classification" class="w-full border border-zinc-300 bg-white px-2 py-2 text-sm text-zinc-900 hover:border-zinc-400 focus:border-zinc-600 focus:outline-none !rounded-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:border-zinc-500 dark:focus:border-zinc-400">
                        <option value="">Any type</option>
                        @foreach($filterClassifications as $classification)
                            <option value="{{ $classification }}">{{ ucfirst((string) $classification) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-0 w-full">
                    <select wire:model.live="filters.category" class="w-full border border-zinc-300 bg-white px-2 py-2 text-sm text-zinc-900 hover:border-zinc-400 focus:border-zinc-600 focus:outline-none !rounded-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:border-zinc-500 dark:focus:border-zinc-400">
                        <option value="">Any category</option>
                        @foreach($filterCategories as $category)
                            <option value="{{ $category }}">{{ ucfirst((string) $category) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-0 w-full">
                    <select wire:model.live="filters.priority" class="w-full border border-zinc-300 bg-white px-2 py-2 text-sm text-zinc-900 hover:border-zinc-400 focus:border-zinc-600 focus:outline-none !rounded-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:border-zinc-500 dark:focus:border-zinc-400">
                        <option value="">Any priority</option>
                        <option value="critical">Critical</option>
                        <option value="important">Important</option>
                        <option value="normal">Normal</option>
                        <option value="informational">Informational</option>
                    </select>
                </div>
                <div class="min-w-0 w-full">
                    <select wire:model.live="filters.mode" class="w-full border border-zinc-300 bg-white px-2 py-2 text-sm text-zinc-900 hover:border-zinc-400 focus:border-zinc-600 focus:outline-none !rounded-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:border-zinc-500 dark:focus:border-zinc-400">
                        <option value="">Any mode</option>
                        <option value="managed">Managed</option>
                        <option value="legacy">Legacy</option>
                    </select>
                </div>
                <div class="min-w-0 w-full">
                    <select wire:model.live="filters.email" class="w-full border border-zinc-300 bg-white px-2 py-2 text-sm text-zinc-900 hover:border-zinc-400 focus:border-zinc-600 focus:outline-none !rounded-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:border-zinc-500 dark:focus:border-zinc-400">
                        <option value="">Any email</option>
                        <option value="on">Email ON</option>
                        <option value="off">Email OFF</option>
                    </select>
                </div>
                <div class="min-w-0 w-full">
                    <select wire:model.live="filters.inbox" class="w-full border border-zinc-300 bg-white px-2 py-2 text-sm text-zinc-900 hover:border-zinc-400 focus:border-zinc-600 focus:outline-none !rounded-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:border-zinc-500 dark:focus:border-zinc-400">
                        <option value="">Any inbox</option>
                        <option value="on">Inbox ON</option>
                        <option value="off">Inbox OFF</option>
                    </select>
                </div>
                <div class="min-w-0 w-full">
                    <select wire:model.live="filters.staff_copy" class="w-full border border-zinc-300 bg-white px-2 py-2 text-sm text-zinc-900 hover:border-zinc-400 focus:border-zinc-600 focus:outline-none !rounded-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:border-zinc-500 dark:focus:border-zinc-400">
                        <option value="">Any staff copy</option>
                        <option value="on">Staff copy ON</option>
                        <option value="off">Staff copy OFF</option>
                    </select>
                </div>
                <div class="min-w-0 w-full">
                    <select wire:model.live="filters.web_push" class="w-full border border-zinc-300 bg-white px-2 py-2 text-sm text-zinc-900 hover:border-zinc-400 focus:border-zinc-600 focus:outline-none !rounded-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:border-zinc-500 dark:focus:border-zinc-400">
                        <option value="">Any web push</option>
                        <option value="on">Web push ON</option>
                        <option value="off">Web push OFF</option>
                    </select>
                </div>
                <div class="min-w-0 w-full">
                    <select wire:model.live="filters.mobile_push" class="w-full border border-zinc-300 bg-white px-2 py-2 text-sm text-zinc-900 hover:border-zinc-400 focus:border-zinc-600 focus:outline-none !rounded-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:border-zinc-500 dark:focus:border-zinc-400">
                        <option value="">Any mobile push</option>
                        <option value="on">Mobile push ON</option>
                        <option value="off">Mobile push OFF</option>
                    </select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>

        <div class="divide-y divide-zinc-200 dark:divide-zinc-800 lg:hidden">
            @forelse($definitions as $definition)
                @php($policy = $definition->policy)
                <div class="p-4" wire:key="comm-def-card-{{ $definition->id }}">
                    <div class="font-medium text-zinc-900 dark:text-white">{{ $definition->name }}</div>
                    <div class="mt-1 font-mono text-[11px] text-zinc-500">{{ $definition->key }}</div>
                    @if($definition->description)
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $definition->description }}</p>
                    @endif
                    <p class="mt-1 text-xs text-zinc-500">{{ $definition->category }} · {{ $definition->classification }}</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @if($definition->active)
                            <flux:button size="xs" variant="ghost" wire:click="toggleManaged({{ $definition->id }})" class="!rounded-none">
                                <flux:badge color="blue">Managed</flux:badge>
                            </flux:button>
                        @else
                            <flux:button size="xs" variant="ghost" wire:click="toggleManaged({{ $definition->id }})" class="!rounded-none">
                                <flux:badge color="zinc">Legacy</flux:badge>
                            </flux:button>
                        @endif
                        <select class="border border-zinc-300 bg-white px-2 py-1 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100" wire:change="updatePriority({{ $definition->id }}, $event.target.value)">
                            @foreach(['critical' => 'Critical', 'important' => 'Important', 'normal' => 'Normal', 'informational' => 'Informational'] as $value => $label)
                                <option value="{{ $value }}" @selected(($policy?->priority ?? $definition->priority) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3">
                        <div>
                            <p class="text-[11px] text-zinc-500">Email</p>
                            <flux:button size="xs" variant="ghost" wire:click="togglePolicy({{ $definition->id }}, 'email_enabled')" class="!rounded-none">
                                <x-flux-admin::status-badge :status="(bool) ($policy?->email_enabled ?? true)" />
                            </flux:button>
                        </div>
                        <div>
                            <p class="text-[11px] text-zinc-500">Inbox</p>
                            <flux:button size="xs" variant="ghost" wire:click="togglePolicy({{ $definition->id }}, 'internal_inbox_enabled')" wire:confirm="Inbox off hides this from the customer and from staff. Turn Staff copy on if you still need a redacted staff view." class="!rounded-none">
                                <x-flux-admin::status-badge :status="(bool) ($policy?->internal_inbox_enabled ?? false)" />
                            </flux:button>
                        </div>
                        <div>
                            <p class="text-[11px] text-zinc-500">Staff copy</p>
                            <flux:button size="xs" variant="ghost" wire:click="togglePolicy({{ $definition->id }}, 'staff_copy_enabled')" class="!rounded-none">
                                <x-flux-admin::status-badge :status="(bool) ($policy?->staff_copy_enabled ?? false)" />
                            </flux:button>
                        </div>
                        <div>
                            <p class="text-[11px] text-zinc-500">Web push</p>
                            <flux:button size="xs" variant="ghost" wire:click="togglePolicy({{ $definition->id }}, 'web_push_enabled')" class="!rounded-none">
                                <x-flux-admin::status-badge :status="(bool) ($policy?->web_push_enabled ?? false)" />
                            </flux:button>
                        </div>
                        <div>
                            <p class="text-[11px] text-zinc-500">Mobile push</p>
                            <flux:button size="xs" variant="ghost" wire:click="togglePolicy({{ $definition->id }}, 'mobile_push_enabled')" class="!rounded-none">
                                <x-flux-admin::status-badge :status="(bool) ($policy?->mobile_push_enabled ?? false)" />
                            </flux:button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('flux-admin.communications.show', $definition) }}">
                            <flux:button size="xs" variant="ghost" icon="eye" class="!rounded-none">View</flux:button>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-zinc-500">No definitions yet. Use Sync list to load them.</div>
            @endforelse
        </div>

        <div class="hidden lg:block">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Alias</flux:table.column>
                <flux:table.column>Key</flux:table.column>
                <flux:table.column>Category</flux:table.column>
                <flux:table.column>Mode</flux:table.column>
                <flux:table.column>Priority</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                        <flux:table.column>Inbox</flux:table.column>
                        <flux:table.column>Staff copy</flux:table.column>
                        <flux:table.column>Web push</flux:table.column>
                <flux:table.column>Mobile push</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($definitions as $definition)
                    @php($policy = $definition->policy)
                    <flux:table.row wire:key="comm-def-{{ $definition->id }}">
                        <flux:table.cell>
                            <div class="flex flex-wrap items-center gap-2">
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $definition->name }}</div>
                                @php($definitionVars = is_array($definition->variables) ? $definition->variables : [])
                                @if(in_array('pdf', $definitionVars, true) || in_array('documents', $definitionVars, true))
                                    <flux:badge color="zinc">PDF</flux:badge>
                                @endif
                            </div>
                            @if($definition->description)
                                <div class="mt-1 max-w-md text-xs text-zinc-600 dark:text-zinc-400">{{ $definition->description }}</div>
                            @endif
                            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $definition->classification }}@if($definition->recipient_summary) · {{ $definition->recipient_summary }}@endif</div>
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
                            <flux:button size="xs" variant="ghost" wire:click="togglePolicy({{ $definition->id }}, 'internal_inbox_enabled')" wire:confirm="Inbox off hides this from the customer and from staff. Turn Staff copy on if you still need a redacted staff view. Passwords are never shown to staff." class="!rounded-none">
                                <x-flux-admin::status-badge :status="(bool) ($policy?->internal_inbox_enabled ?? false)" />
                            </flux:button>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" wire:click="togglePolicy({{ $definition->id }}, 'staff_copy_enabled')" class="!rounded-none">
                                <x-flux-admin::status-badge :status="(bool) ($policy?->staff_copy_enabled ?? false)" />
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
                        <flux:table.cell colspan="11" class="py-10 text-center text-zinc-500 dark:text-zinc-400">
                            @if($schemaReady)
                                No definitions yet. Use Sync list to load rental agreements, finance contracts and the other transactional emails.
                            @else
                                Communication tables are not available yet. Run the migration before synchronizing definitions.
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        </div>
        <x-slot:footer>{{ $definitions->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <div class="flex flex-col gap-4">
        <div class="min-w-0">
            <h2 class="flux-admin-page-title text-2xl font-bold text-zinc-900 dark:text-white">Excluded or legacy email areas</h2>
            <p class="mt-1 max-w-3xl text-sm text-zinc-500 dark:text-zinc-400">These are intentionally not controlled by this transactional customer communication panel.</p>
        </div>

        <div class="divide-y divide-zinc-200 border border-zinc-200 bg-white shadow-sm dark:divide-zinc-800 dark:border-zinc-800 dark:bg-zinc-900 md:hidden">
            @foreach($excludedInventory as $item)
                <div class="p-4" wire:key="excluded-mobile-{{ md5((string) ($item['name'] ?? '')) }}">
                    <div class="font-medium text-zinc-900 dark:text-white">{{ $item['name'] ?? 'Unknown' }}</div>
                    <div class="mt-2">
                        <flux:badge color="zinc">{{ $item['type'] ?? 'Excluded' }}</flux:badge>
                    </div>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $item['reason'] ?? '' }}</p>
                </div>
            @endforeach
        </div>

        <div class="flux-admin-table-panel flux-admin-table-readable hidden border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900 md:block">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="w-[28%]">Email area</flux:table.column>
                    <flux:table.column class="w-[14%]">Classification</flux:table.column>
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
        </div>
    </div>
</div>
