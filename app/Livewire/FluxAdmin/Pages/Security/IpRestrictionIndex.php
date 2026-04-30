<?php

namespace App\Livewire\FluxAdmin\Pages\Security;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\IpRestriction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('IP restrictions — Flux Admin')]
class IpRestrictionIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public bool $editorOpen = false;

    public ?int $editingId = null;

    /** @var array<string, mixed> */
    public array $form = [
        'ip_address' => '',
        'status' => 'blocked',
        'restriction_type' => 'full_site',
        'label' => '',
        'user_id' => null,
    ];

    public function mount(): void
    {
        $this->authorizeModule('see-menu-security');
        $this->sortField = 'updated_at';
    }

    public function render()
    {
        $restrictions = $this->baseQuery()
            ->with('user:id,first_name,last_name,email')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.security.ip-restrictions-index', [
            'restrictions' => $restrictions,
        ]);
    }

    private function baseQuery(): Builder
    {
        return IpRestriction::query()
            ->when($this->search, function ($q): void {
                $q->where(function ($q): void {
                    $q->where('ip_address', 'like', "%{$this->search}%")
                        ->orWhere('label', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filter('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($this->filter('restriction_type'), fn ($q, $v) => $q->where('restriction_type', $v));
    }

    public function openCreate(): void
    {
        $this->reset('form', 'editingId');
        $this->form = [
            'ip_address' => '',
            'status' => 'blocked',
            'restriction_type' => 'full_site',
            'label' => '',
            'user_id' => null,
        ];
        $this->editorOpen = true;
    }

    public function openEdit(int $id): void
    {
        $restriction = IpRestriction::findOrFail($id);
        $this->editingId = $restriction->id;
        $this->form = [
            'ip_address' => $restriction->ip_address,
            'status' => $restriction->status,
            'restriction_type' => $restriction->restriction_type,
            'label' => $restriction->label,
            'user_id' => $restriction->user_id,
        ];
        $this->editorOpen = true;
    }

    protected function rules(): array
    {
        return [
            'form.ip_address' => ['required', 'string', 'max:45'],
            'form.status' => ['required', Rule::in(['allowed', 'blocked'])],
            'form.restriction_type' => ['required', Rule::in(['admin_only', 'full_site'])],
            'form.label' => ['nullable', 'string', 'max:255'],
            'form.user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $restriction = $this->editingId
            ? IpRestriction::findOrFail($this->editingId)
            : new IpRestriction;

        $restriction->fill($this->form)->save();

        $this->editorOpen = false;
        $this->editingId = null;

        session()->flash('flux-admin.flash', $this->editingId ? 'Restriction updated.' : 'Restriction created.');
    }

    public function deleteRestriction(int $id): void
    {
        IpRestriction::findOrFail($id)->delete();
        session()->flash('flux-admin.flash', 'Restriction deleted.');
    }
}
