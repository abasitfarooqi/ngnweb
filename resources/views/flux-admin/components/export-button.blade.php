@props([
    'csvAction' => 'exportCsv',
    'xlsxAction' => null,
])

<flux:dropdown position="bottom" align="end">
    <flux:button size="sm" variant="ghost" icon="arrow-down-tray" class="!rounded-none">Export</flux:button>
    <flux:menu class="min-w-[180px]">
        <flux:menu.item wire:click="{{ $csvAction }}" icon="document-text">
            Download CSV
        </flux:menu.item>
        @if($xlsxAction)
            <flux:menu.item wire:click="{{ $xlsxAction }}" icon="table-cells">
                Download XLSX
            </flux:menu.item>
        @endif
    </flux:menu>
</flux:dropdown>
