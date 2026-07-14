<div>
    <x-flux-admin::data-table title="Club OTP viewer" description="Dev-only view of OTP verification codes for club members.">
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search member name, email or phone…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_used" placeholder="Used">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Used</flux:select.option>
                        <flux:select.option value="0">Unused</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Member</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column>OTP code</flux:table.column>
                <flux:table.column>Expires</flux:table.column>
                <flux:table.column>Used</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="otp-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->clubMember?->full_name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->clubMember?->email ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->clubMember?->phone ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-lg text-zinc-900 dark:text-white font-bold">{{ $r->otp_code }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->expires_at?->format('d M Y H:i') ?? '—' }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->is_used" /></flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="6" class="text-center py-8 text-zinc-500">No OTP records.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
