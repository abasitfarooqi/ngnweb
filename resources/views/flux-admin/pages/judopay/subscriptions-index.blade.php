<div>
    <x-flux-admin::data-table title="Judopay subscriptions" description="Recurring card-on-file billing subscriptions.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.judopay-subscriptions.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New subscription</flux:button></a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search consumer ref, card, receipt or auth code…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.status" placeholder="Status">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="paused">Paused</flux:select.option>
                        <flux:select.option value="cancelled">Cancelled</flux:select.option>
                        <flux:select.option value="completed">Completed</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.billing_frequency" placeholder="Frequency">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="weekly">Weekly</flux:select.option>
                        <flux:select.option value="monthly">Monthly</flux:select.option>
                        <flux:select.option value="annually">Annually</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filterSubscribableType" placeholder="Type">
                        <flux:select.option value="">All types</flux:select.option>
                        <flux:select.option value="App\Models\RentingBooking">Rental</flux:select.option>
                        <flux:select.option value="App\Models\FinanceApplication">Finance</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'date'" :direction="$sortField === 'date' ? $sortDirection : null" wire:click="sortBy('date')">Date</flux:table.column>
                <flux:table.column>Consumer</flux:table.column>
                <flux:table.column>Card</flux:table.column>
                <flux:table.column>Frequency</flux:table.column>
                <flux:table.column>Amount</flux:table.column>
                <flux:table.column>Start / End</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="js-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->date ? \Carbon\Carbon::parse($r->date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->consumer_reference }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">•••• {{ $r->card_last_four }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->billing_frequency }} · day {{ $r->billing_day }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">£{{ number_format((float) $r->amount, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-600 dark:text-zinc-400">
                            {{ $r->start_date ? \Carbon\Carbon::parse($r->start_date)->format('d M Y') : '—' }}
                            @if($r->end_date) → {{ \Carbon\Carbon::parse($r->end_date)->format('d M Y') }} @endif
                        </flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$r->status" /></flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1">
                                <a href="{{ route('flux-admin.judopay-subscriptions.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                @if($r->status === 'active')
                                    <flux:button size="xs" variant="ghost" wire:click="fireMit({{ $r->id }})" wire:confirm="Fire a direct MIT payment for this subscription?" icon="bolt" class="!rounded-none text-amber-600 dark:text-amber-400">Fire MIT</flux:button>
                                    <flux:button size="xs" variant="ghost" wire:click="openBillingForm({{ $r->id }})" icon="calendar-days" class="!rounded-none">Billing day</flux:button>
                                    <flux:button size="xs" variant="ghost" wire:click="openAmountForm({{ $r->id }})" icon="currency-pound" class="!rounded-none">Amount</flux:button>
                                    <flux:button size="xs" variant="ghost" wire:click="openAuthForm({{ $r->id }})" icon="link" class="!rounded-none text-blue-600 dark:text-blue-400">Auth link</flux:button>
                                    <flux:button size="xs" variant="ghost" wire:click="sendAuthEmail({{ $r->id }})" icon="envelope" class="!rounded-none text-blue-600 dark:text-blue-400">Send auth email</flux:button>
                                    <flux:button size="xs" variant="ghost" wire:click="killPreviousLinks({{ $r->id }})" wire:confirm="Kill all active auth links and CIT sessions for this subscription?" icon="trash" class="!rounded-none text-orange-600 dark:text-orange-400">Kill links</flux:button>
                                    <flux:button size="xs" variant="ghost" wire:click="closeSubscription({{ $r->id }})" wire:confirm="Close and cancel this subscription?" icon="x-circle" class="!rounded-none text-red-600 dark:text-red-400">Close</flux:button>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showBillingForm" class="md:w-[480px]">
        <form wire:submit.prevent="saveBillingDay" class="space-y-4" novalidate>
            <flux:heading size="lg">Update billing day</flux:heading>
            <x-flux-admin::field-group label="Billing frequency" :error="$errors->first('billingFrequency')" required>
                <flux:select wire:model.live="billingFrequency">
                    <flux:select.option value="weekly">Weekly</flux:select.option>
                    <flux:select.option value="monthly">Monthly</flux:select.option>
                </flux:select>
            </x-flux-admin::field-group>
            @if($billingFrequency === 'monthly')
                <x-flux-admin::field-group label="Billing day of month" :error="$errors->first('billingDay')" required>
                    <flux:select wire:model="billingDay">
                        <flux:select.option value="1">1st</flux:select.option>
                        <flux:select.option value="15">15th</flux:select.option>
                        <flux:select.option value="28">28th</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
            @else
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Weekly billing runs every Saturday (day 6).</p>
            @endif
            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showBillingForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal wire:model.self="showAmountForm" class="md:w-[400px]">
        <form wire:submit.prevent="saveAmount" class="space-y-4" novalidate>
            <flux:heading size="lg">Update amount</flux:heading>
            <x-flux-admin::field-group label="New amount (£)" :error="$errors->first('newAmount')" required>
                <flux:input type="number" step="0.01" min="0.01" wire:model="newAmount" />
            </x-flux-admin::field-group>
            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showAmountForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>


    <flux:modal wire:model.self="showAuthForm" class="md:w-[600px]">
        <div class="space-y-4">
            <flux:heading size="lg">Generate authorization link</flux:heading>
            <x-flux-admin::field-group label="Customer name">
                <flux:input wire:model="authCustomerName" />
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Customer email">
                <flux:input type="email" wire:model="authCustomerEmail" />
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Expires in (hours)">
                <flux:input type="number" wire:model="authExpiresInHours" min="1" max="168" />
            </x-flux-admin::field-group>
            @if($generatedAuthLink)
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-3">
                    <p class="text-xs font-semibold text-green-800 dark:text-green-200 mb-1">Generated link:</p>
                    <p class="text-xs font-mono break-all text-green-700 dark:text-green-300">{{ $generatedAuthLink }}</p>
                </div>
            @endif
            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showAuthForm', false)" class="!rounded-none">Close</flux:button>
                <flux:button type="button" variant="primary" wire:click="generateAuthAccess" class="!rounded-none">Generate link</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
