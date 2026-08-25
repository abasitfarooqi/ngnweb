<?php

namespace Tests\Feature;

use App\Livewire\FluxAdmin\Pages\Rentals\WeeklyUpdateReport;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class RentingWeeklyUpdateReportPageTest extends TestCase
{
    public function test_staff_chase_report_renders(): void
    {
        $staff = User::query()->where('is_admin', 1)->orderBy('id')->first();
        if (! $staff) {
            $this->markTestSkipped('No admin user.');
        }

        $this->actingAs($staff);

        Livewire::test(WeeklyUpdateReport::class)
            ->assertOk()
            ->assertSee('Weekly chase report')
            ->assertSee('Still unpaid')
            ->assertSee('Download PDF');
    }

    public function test_account_card_keeps_white_title_on_the_dark_header(): void
    {
        $html = view('flux-admin.pages.rentals.weekly-update-report', [
            'periods' => [['key' => 'week', 'label' => 'This week']],
            'report' => [
                'start' => now()->startOfWeek(),
                'end' => now(),
                'intro' => 'This is the rentals chase report.',
                'summary' => [
                    'customers' => 1,
                    'entries' => 1,
                    'by_staff' => collect([['name' => 'Tahir Shakoor', 'count' => 1]]),
                ],
                'accounts' => collect([[
                    'booking_id' => 239,
                    'customer' => 'MR NAJIBULLAH SAFI',
                    'customer_id' => 261,
                    'phone' => '+44 7848 140236',
                    'email' => 'najibullahsafi617@gmail.com',
                    'registration' => 'AO23HVL',
                    'weekly_rent' => 75,
                    'outstanding' => 150,
                    'oldest_due' => '15 Aug 2026',
                    'story' => 'Tahir Shakoor followed this up once this week.',
                    'notes' => collect([[]]),
                    'rental_notes' => collect(),
                    'invoices' => [[
                        'id' => 6716,
                        'date' => '15 Aug 2026',
                        'amount' => 75,
                        'notes' => [],
                    ]],
                ]]),
            ],
        ])->render();

        $this->assertStringContainsString('flux-admin-on-dark', $html);
        $this->assertStringContainsString('!text-white', $html);
        $this->assertStringContainsString('Rental #239', $html);
        $this->assertStringContainsString('Invoice #6716', $html);
        $this->assertStringContainsString('Unpaid', $html);
    }
}
