<div>
    <x-flux-admin::data-table title="PCN TOL requests" description="Transfer-of-liability letters generated for PCN cases.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/pcn-tol-request/create')" class="!rounded-none">New TOL</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search by PCN number…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-44 lg:flex-none">
                    <flux:select wire:model.live="filters.status" placeholder="Status">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="pending">Pending</flux:select.option>
                        <flux:select.option value="sent">Sent</flux:select.option>
                        <flux:select.option value="approved">Approved</flux:select.option>
                        <flux:select.option value="rejected">Rejected</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>ID</flux:table.column>
                <flux:table.column>PCN</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'request_date'" :direction="$sortField === 'request_date' ? $sortDirection : null" wire:click="sortBy('request_date')">Requested</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Letter sent</flux:table.column>
                <flux:table.column>Raised by</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="tol-{{ $r->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->id }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->pcnCaseUpdate?->pcnCase?->pcn_number }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->request_date ? \Carbon\Carbon::parse($r->request_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <x-flux-admin::status-badge :status="$r->status" :map="[
                                'pending' => ['yellow', 'Pending'],
                                'sent' => ['blue', 'Sent'],
                                'approved' => ['green', 'Approved'],
                                'rejected' => ['red', 'Rejected'],
                            ]" />
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->letter_sent_at ? \Carbon\Carbon::parse($r->letter_sent_at)->format('d M Y H:i') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->user ? $r->user->first_name.' '.$r->user->last_name : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="generatePdf({{ $r->id }})" icon="document-arrow-down" class="!rounded-none">PDF</flux:button>
                                <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/pcn-tol-request/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No requests.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
