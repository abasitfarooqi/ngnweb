<?php

namespace App\Livewire\FluxAdmin\Pages\Security;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\AccessLog;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Access logs — Flux Admin')]
class AccessLogIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('manage_access_logs');
        $this->sortField = 'created_at';
    }

    public function render()
    {
        $logs = $this->baseQuery()
            ->with('user:id,first_name,last_name,email')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.security.access-logs-index', [
            'logs' => $logs,
        ]);
    }

    private function baseQuery(): Builder
    {
        return AccessLog::query()
            ->when($this->search, function ($q): void {
                $q->where(function ($q): void {
                    $q->where('ip_address', 'like', "%{$this->search}%")
                        ->orWhere('area_attempted', 'like', "%{$this->search}%")
                        ->orWhere('message', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filter('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($this->filter('user_id'), fn ($q, $v) => $q->where('user_id', $v))
            ->when($this->filter('from'), fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($this->filter('to'), fn ($q, $v) => $q->whereDate('created_at', '<=', $v));
    }
}
