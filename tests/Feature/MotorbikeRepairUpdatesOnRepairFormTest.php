<?php

namespace Tests\Feature;

use App\Models\MotorbikeRepair;
use App\Models\MotorbikeRepairUpdate;
use App\Models\User;
use Tests\TestCase;

class MotorbikeRepairUpdatesOnRepairFormTest extends TestCase
{
    public function test_repair_edit_page_shows_the_repeatable_updates(): void
    {
        $staff = User::query()->where('is_admin', 1)->orderBy('id')->first();
        $repair = MotorbikeRepair::query()->orderByDesc('id')->first();
        if (! $staff || ! $repair) {
            $this->markTestSkipped('Need an admin user and a repair.');
        }

        $this->actingAs($staff)
            ->get(route('flux-admin.motorbike-repairs.edit', $repair))
            ->assertOk()
            ->assertSee('Repair updates')
            ->assertSee('Add update');
    }

    public function test_update_edit_opens_the_parent_repair(): void
    {
        $staff = User::query()->where('is_admin', 1)->orderBy('id')->first();
        $update = MotorbikeRepairUpdate::query()->whereNotNull('motorbike_repair_id')->orderByDesc('id')->first();
        if (! $staff || ! $update) {
            $this->markTestSkipped('Need an admin user and a repair update.');
        }

        $this->actingAs($staff)
            ->get(route('flux-admin.motorbike-repair-updates.edit', $update))
            ->assertRedirect(route('flux-admin.motorbike-repairs.edit', $update->motorbike_repair_id));
    }

    public function test_update_create_asks_for_a_searchable_repair(): void
    {
        $staff = User::query()->where('is_admin', 1)->orderBy('id')->first();
        if (! $staff) {
            $this->markTestSkipped('Need an admin user.');
        }

        $this->actingAs($staff)
            ->get(route('flux-admin.motorbike-repair-updates.create'))
            ->assertOk()
            ->assertSee('Search repair')
            ->assertDontSee('Motorbike repair ID');
    }
}
