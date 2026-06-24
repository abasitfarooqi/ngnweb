<div>
    {{-- Flash message --}}
    @if($flashMessage)
        <div class="mx-4 mt-4 p-3 text-sm font-medium border
            {{ $flashType === 'success' ? 'border-emerald-400 bg-emerald-50 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-700' : '' }}
            {{ $flashType === 'error' ? 'border-red-400 bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300 dark:border-red-700' : '' }}
            {{ $flashType === 'info' ? 'border-blue-400 bg-blue-50 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-700' : '' }}">
            {{ $flashMessage }}
        </div>
    @endif

    {{-- Document link display --}}
    @if($docUploadLink)
        <div class="mx-4 mt-3 p-3 border border-amber-400 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-700">
            <p class="text-xs font-bold text-amber-800 dark:text-amber-300 mb-1">DOCUMENT UPLOAD LINK — Share with customer:</p>
            <div class="flex items-center gap-2">
                <code class="text-xs bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 px-2 py-1 flex-1 break-all">{{ $docUploadLink }}</code>
                <flux:button size="xs" variant="ghost" icon="clipboard" x-on:click="navigator.clipboard.writeText('{{ $docUploadLink }}')">Copy</flux:button>
            </div>
        </div>
    @endif

    {{-- Booking state & action buttons --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 border-b border-zinc-200 dark:border-zinc-700">
        <div class="flex-1">
            <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Current State</p>
            <flux:badge
                size="sm"
                :color="str_contains($booking->state ?? '', 'Completed') ? 'emerald' : (str_contains($booking->state ?? '', 'Await') ? 'amber' : 'zinc')"
            >
                {{ $booking->state ?? 'Unknown' }}
            </flux:badge>
            @if($pendingInvoiceAmount > 0)
                <flux:badge size="sm" color="red" class="ml-2">Outstanding: £{{ number_format($pendingInvoiceAmount, 2) }}</flux:badge>
            @endif
        </div>
        <div class="flex flex-wrap gap-2">
            @if($lifecycleStatus === 'intake' && count($missing) === 0)
                <button
                    wire:click="activateRentalToday"
                    wire:confirm="All required documents are approved. Activate this rental for today?"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition"
                >
                    Documents received — start rental today
                </button>
            @elseif($lifecycleStatus === 'intake' && count($missing) > 0)
                <span class="text-xs text-amber-700 dark:text-amber-300 self-center">{{ count($missing) }} mandatory document(s) still pending approval.</span>
            @endif
            @if(in_array($booking->state, ['Awaiting Documents & Payment', 'Awaiting Documents']))
                <button
                    wire:click="markDocumentsComplete"
                    wire:confirm="Have all documents been thoroughly reviewed and verified?"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition"
                >
                    Documents complete (state only)
                </button>
            @endif
            <button
                wire:click="generateDocumentLink"
                class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold bg-brand-red hover:opacity-90 text-white transition"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                Generate Upload Link
            </button>
        </div>
    </div>

    {{-- Required document checklist --}}
    <div class="mx-4 mt-4 border border-zinc-200 dark:border-zinc-700">
        <div class="px-4 py-2 border-b border-zinc-200 dark:border-zinc-700 text-xs font-bold uppercase tracking-wide text-zinc-500">Mandatory documents</div>
        <ul class="divide-y divide-zinc-200 dark:divide-zinc-700">
            @foreach($checklist as $item)
                <li class="flex items-center justify-between px-4 py-2 text-sm">
                    <span>{{ $item['name'] }}</span>
                    <flux:badge size="sm" :color="$item['approved'] ? 'emerald' : 'amber'">{{ $item['approved'] ? 'Approved' : 'Pending' }}</flux:badge>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Documents table --}}
    <div class="touch-pan-x overflow-x-auto">
        <div class="min-w-[48rem] md:min-w-0">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>ID</flux:table.column>
                    <flux:table.column>Type</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Doc Number</flux:table.column>
                    <flux:table.column>Valid Until</flux:table.column>
                    <flux:table.column>Uploaded</flux:table.column>
                    <flux:table.column>&nbsp;</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($documents as $doc)
                        <flux:table.row wire:key="doc-{{ $doc->id }}">
                            <flux:table.cell class="font-medium text-xs">#{{ $doc->id }}</flux:table.cell>
                            <flux:table.cell class="text-sm">{{ $doc->documentType?->name ?? '—' }}</flux:table.cell>
                            <flux:table.cell>
                                @php
                                    $statusColor = match($doc->status ?? '') {
                                        'approved' => 'emerald',
                                        'rejected' => 'red',
                                        'pending_review' => 'amber',
                                        'uploaded' => 'blue',
                                        default => 'zinc',
                                    };
                                @endphp
                                <flux:badge size="sm" :color="$statusColor">{{ ucfirst(str_replace('_', ' ', $doc->status ?? 'unknown')) }}</flux:badge>
                                @if($doc->is_verified)
                                    <flux:badge size="sm" color="emerald" class="ml-1">Verified</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="font-mono text-xs">{{ $doc->document_number ?? '—' }}</flux:table.cell>
                            <flux:table.cell class="text-xs">{{ $doc->valid_until ? \Carbon\Carbon::parse($doc->valid_until)->format('d M Y') : '—' }}</flux:table.cell>
                            <flux:table.cell class="text-xs text-zinc-500">{{ $doc->created_at?->format('d M Y') ?? '—' }}</flux:table.cell>
                            <flux:table.cell>
                                @if($doc->file_url)
                                    <flux:button size="xs" variant="ghost" icon="eye" href="{{ $doc->file_url }}" target="_blank">View</flux:button>
                                @else
                                    <span class="text-xs text-zinc-400">No file</span>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7" class="py-10 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon name="document" variant="outline" class="w-8 h-8 text-zinc-400" />
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">No documents found for this booking or customer.</p>
                                    @if($booking->state && str_contains($booking->state, 'Awaiting'))
                                        <p class="text-xs text-amber-600 dark:text-amber-400">Use "Generate Upload Link" above to send the customer a document upload link.</p>
                                    @endif
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>

    {{-- Important notices --}}
    <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
        <p class="text-xs text-zinc-500 dark:text-zinc-400 italic">
            <strong class="text-zinc-700 dark:text-zinc-300">Note:</strong>
            Before marking documents complete, ensure the insurance policy and certificates are valid and cover the relevant period.
            Cross-reference all documents with the customer's details for accuracy.
        </p>
    </div>
</div>
