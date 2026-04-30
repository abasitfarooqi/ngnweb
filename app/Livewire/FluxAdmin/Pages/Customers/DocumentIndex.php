<?php

namespace App\Livewire\FluxAdmin\Pages\Customers;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\CustomerDocument;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Verify documents — Flux Admin')]
class DocumentIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->sortField = 'created_at';
        $this->exportable = true;
        $this->exportFilename = 'customer-documents';

        if (! isset($this->filters['status']) || $this->filters['status'] === null) {
            $this->filters['status'] = 'pending_review';
        }
    }

    public function render()
    {
        $docs = $this->baseQuery()
            ->with(['customer:id,first_name,last_name,email', 'documentType:id,name'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.customers.documents-index', [
            'docs' => $docs,
        ]);
    }

    protected function baseQuery(): Builder
    {
        return CustomerDocument::query()
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(function ($q) use ($term): void {
                    $q->where('file_name', 'like', "%{$term}%")
                        ->orWhere('document_number', 'like', "%{$term}%")
                        ->orWhereHas('customer', function ($q) use ($term): void {
                            $q->where('first_name', 'like', "%{$term}%")
                                ->orWhere('last_name', 'like', "%{$term}%")
                                ->orWhere('email', 'like', "%{$term}%");
                        });
                });
            })
            ->when($this->filter('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($this->filter('verified') !== '', function ($q): void {
                $q->where('is_verified', $this->filter('verified') === '1');
            })
            ->when($this->filter('document_type_id'), fn ($q, $v) => $q->where('document_type_id', $v));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery()->with(['customer:id,first_name,last_name,email', 'documentType:id,name']);
    }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'Customer' => fn ($d) => trim(($d->customer?->first_name ?? '').' '.($d->customer?->last_name ?? '')) ?: ($d->customer?->email ?? '—'),
            'Document type' => fn ($d) => $d->documentType?->name ?? '—',
            'Status' => 'status',
            'Verified' => fn ($d) => $d->is_verified ? 'Yes' : 'No',
            'Document number' => 'document_number',
            'Valid until' => fn ($d) => $d->valid_until ? \Carbon\Carbon::parse($d->valid_until)->format('Y-m-d') : '',
            'Uploaded' => fn ($d) => $d->created_at?->format('Y-m-d H:i'),
        ];
    }
}
