<?php

namespace Tests\Feature;

use App\Livewire\FluxAdmin\Pages\DirectorCommandCentrePage;
use App\Models\User;
use App\Services\Director\DirectorCommandCentre;
use App\Support\RentingReferralAccess;
use Livewire\Livewire;
use Tests\TestCase;

class DirectorCommandCentreTest extends TestCase
{
    public function test_director_panel_is_visible_to_an_investigator(): void
    {
        $staff = User::query()->where('email', 'thiago@neguinhomotors.co.uk')->first();
        if (! $staff) {
            $staff = User::query()->where('is_admin', 1)->orderBy('id')->get()
                ->first(fn (User $user) => RentingReferralAccess::canInvestigate($user));
        }
        if (! $staff) {
            $this->markTestSkipped('No investigator user.');
        }

        $this->actingAs($staff);

        $this->get(route('flux-admin.director-command-centre.index'))
            ->assertOk()
            ->assertSee('Director panel')
            ->assertSee('Whole business')
            ->assertSee('Coming in this period')
            ->assertSee('Open the desk');

        Livewire::test(DirectorCommandCentrePage::class)
            ->assertSee('Director panel')
            ->assertSee('Coming in this period')
            ->call('setModule', 'rentals')
            ->assertSee('Weekly book')
            ->call('setModule', 'club')
            ->assertSee('Discount given')
            ->call('setPreset', 'month')
            ->assertSee('Discount given');
    }

    public function test_director_panel_is_hidden_from_other_staff(): void
    {
        $blocked = User::query()->where('is_admin', 1)->orderBy('id')->get()
            ->first(fn (User $user) => ! RentingReferralAccess::canInvestigate($user));
        if (! $blocked) {
            $this->markTestSkipped('No non-investigator staff user.');
        }

        $this->actingAs($blocked)
            ->get(route('flux-admin.director-command-centre.index'))
            ->assertForbidden();
    }

    public function test_command_centre_build_returns_module_cards(): void
    {
        $panel = DirectorCommandCentre::make([
            'module' => 'overview',
            'from' => now()->startOfMonth()->toDateString(),
            'to' => now()->toDateString(),
        ])->build();

        $this->assertSame('overview', $panel['module']);
        $this->assertNotEmpty($panel['cards']);
        $this->assertArrayHasKey('weekly_rent', $panel['snapshot']);
        $this->assertArrayHasKey('rental_cash_in', $panel['period']);
        $this->assertArrayHasKey('pounds_given', $panel['referrals']);
    }
}
