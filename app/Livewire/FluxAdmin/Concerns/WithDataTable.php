<?php

namespace App\Livewire\FluxAdmin\Concerns;

use Livewire\Attributes\Url;

trait WithDataTable
{
    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'sort', except: 'id')]
    public string $sortField = 'id';

    #[Url(as: 'dir', except: 'desc')]
    public string $sortDirection = 'desc';

    #[Url(as: 'pp', except: 20)]
    public int $perPage = 20;

    /** @var array<string, mixed> Persistent filter bag keyed by filter name. */
    #[Url(as: 'f', except: [])]
    public array $filters = [];

    public bool $exportable = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatingFilters(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->filters = [];
        $this->search = '';
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    /**
     * Read a single filter value with a typed fallback.
     */
    protected function filter(string $key, mixed $default = ''): mixed
    {
        return $this->filters[$key] ?? $default;
    }
}
