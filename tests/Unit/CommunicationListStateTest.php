<?php

namespace Tests\Unit;

use App\Livewire\FluxAdmin\Pages\Communications\CommunicationIndex;
use ReflectionMethod;
use Tests\TestCase;

class CommunicationListStateTest extends TestCase
{
    public function test_restores_search_filters_and_page_size_from_session(): void
    {
        session([
            'flux_admin.communications.list' => [
                'search' => 'deposit',
                'filters' => [
                    'category' => 'rentals',
                    'email' => 'on',
                    'mode' => 'managed',
                ],
                'perPage' => 50,
            ],
        ]);

        $component = new CommunicationIndex;
        $component->search = '';
        $component->filters = [];
        $component->perPage = 20;

        $method = new ReflectionMethod(CommunicationIndex::class, 'restoreListState');
        $method->invoke($component);

        $this->assertSame('deposit', $component->search);
        $this->assertSame('rentals', $component->filters['category']);
        $this->assertSame('on', $component->filters['email']);
        $this->assertSame('managed', $component->filters['mode']);
        $this->assertSame(50, $component->perPage);
    }

    public function test_reset_clears_persisted_list_state(): void
    {
        session([
            'flux_admin.communications.list' => [
                'search' => 'deposit',
                'filters' => ['category' => 'rentals'],
                'perPage' => 50,
            ],
        ]);

        $component = new CommunicationIndex;
        $component->search = 'deposit';
        $component->filters = ['category' => 'rentals'];
        $component->perPage = 50;

        $component->resetFilters();

        $this->assertSame('', $component->search);
        $this->assertSame([], $component->filters);
        $this->assertSame(20, $component->perPage);
        $this->assertNull(session('flux_admin.communications.list'));
    }
}
