<?php

namespace App\Livewire\FluxAdmin\Concerns;

use Livewire\Attributes\Url;

trait WithDataTable
{
    #[Url(as: 'q', history: true, except: '')]
    public string $search = '';

    #[Url(as: 'sort', history: true, except: 'id')]
    public string $sortField = 'id';

    #[Url(as: 'dir', history: true, except: 'desc')]
    public string $sortDirection = 'desc';

    #[Url(as: 'pp', history: true, except: 20)]
    public int $perPage = 20;

    /** @var array<string, mixed> Persistent filter bag keyed by filter name. */
    #[Url(as: 'f', history: true, except: [])]
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
        $defaults = (new \ReflectionClass($this))->getDefaultProperties();

        foreach (array_keys(get_object_vars($this)) as $property) {
            if ($property === 'filters' || $property === 'search'
                || str_starts_with($property, 'filter')
                || in_array($property, [
                    'status', 'isPolice', 'contractType', 'branch', 'activeOnly',
                    'startDateFrom', 'startDateTo', 'contractDateFrom', 'contractDateTo',
                ], true)) {
                $this->{$property} = $defaults[$property] ?? (is_bool($this->{$property}) ? false : '');
            }
        }

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
