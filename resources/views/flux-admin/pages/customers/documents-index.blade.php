<div>
    <x-flux-admin::data-table
        title="Verify customer documents"
        description="Queue of uploaded documents awaiting review, approval, or rejection."
    >
        <x-slot:actions>
            <x-flux-admin::export-button />
        </x-slot:actions>

        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search customer, file or document number…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-48 lg:flex-none">
                    <flux:select wire:model.live="filters.status" placeholder="Status">
                        <flux:select.option value="">All statuses</flux:select.option>
                        <flux:select.option value="pending_review">Pending review</flux:select.option>
                        <flux:select.option value="uploaded">Uploaded</flux:select.option>
                        <flux:select.option value="approved">Approved</flux:select.option>
                        <flux:select.option value="rejected">Rejected</flux:select.option>
                        <flux:select.option value="archived">Archived</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.verified" placeholder="Verified">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Verified</flux:select.option>
                        <flux:select.option value="0">Not verified</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>

        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'created_at'" :direction="$sortField === 'created_at' ? $sortDirection : null" wire:click="sortBy('created_at')">Uploaded</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Document type</flux:table.column>
                <flux:table.column>File</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Verified</flux:table.column>
                <flux:table.column>Valid until</flux:table.column>
                <flux:table.column>Document #</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($docs as $doc)
                    <flux:table.row wire:key="doc-{{ $doc->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $doc->created_at?->format('d M Y') }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">
                            @if($doc->customer)
                                {{ trim(($doc->customer->first_name ?? '').' '.($doc->customer->last_name ?? '')) ?: $doc->customer->email }}
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $doc->documentType?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-[14rem] truncate">
                            @if($doc->file_url)
                                <a href="{{ $doc->file_url }}" target="_blank" class="underline hover:text-zinc-900 dark:hover:text-white">{{ $doc->file_name ?? 'View' }}</a>
                            @else
                                {{ $doc->file_name ?? '—' }}
                            @endif
                        </flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$doc->status" /></flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $doc->is_verified" /></flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $doc->valid_until ? \Carbon\Carbon::parse($doc->valid_until)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ $doc->document_number ?: '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="primary" :href="route('flux-admin.customer-documents.review', $doc)" icon="check-badge" class="!rounded-none">Review</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="9" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No documents match the current filters.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $docs->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
