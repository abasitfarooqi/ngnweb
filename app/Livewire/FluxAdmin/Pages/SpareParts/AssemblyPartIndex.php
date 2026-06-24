<?php

namespace App\Livewire\FluxAdmin\Pages\SpareParts;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\SpAssembly;
use App\Models\SpAssemblyPart;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Spare parts — Assembly parts')]
class AssemblyPartIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
    }

    public function delete(int $id): void
    {
        SpAssemblyPart::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = SpAssemblyPart::query()
            ->with(['assembly:id,name', 'part:id,part_number,name'])
            ->when($this->search, fn ($q, $v) => $q->whereHas('part', fn ($q) => $q->where('part_number', 'like', "%{$v}%")->orWhere('name', 'like', "%{$v}%")))
            ->when($this->filter('assembly_id'), fn ($q, $v) => $q->where('assembly_id', $v))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $assemblies = SpAssembly::query()->orderByDesc('id')->limit(500)->get(['id', 'name']);

        return view('flux-admin.pages.spare-parts.assembly-parts-index', compact('rows', 'assemblies'));
    }
}
