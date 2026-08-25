<?php

namespace Tests\Unit;

use App\Models\RentingReferral;
use App\Models\RentingReferralPointLedger;
use App\Services\Renting\RentingReferralService;
use App\Support\RentingReferralSettings;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RentingReferralServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('renting_referrals') || ! Schema::hasTable('customers') || ! Schema::hasTable('booking_invoices')) {
            $this->markTestSkipped('Rental referral tables are not migrated.');
        }
    }

    public function test_settings_expose_configurable_wait_and_points(): void
    {
        $this->assertGreaterThan(0, RentingReferralSettings::pointsPerQualifiedReferral());
        $this->assertGreaterThanOrEqual(0, RentingReferralSettings::waitDays());
        $this->assertSame('thiago@neguinhomotors.co.uk', RentingReferralSettings::approvalReportTo());
    }

    public function test_pending_points_are_not_spendable(): void
    {
        $row = new RentingReferralPointLedger([
            'direction' => RentingReferralPointLedger::DIRECTION_CREDIT,
            'status' => RentingReferralPointLedger::STATUS_PENDING,
            'points' => 100,
            'available_from' => now()->subDay(),
        ]);

        $this->assertFalse($row->isSpendable());
    }

    public function test_available_points_wait_until_available_from(): void
    {
        $row = new RentingReferralPointLedger([
            'direction' => RentingReferralPointLedger::DIRECTION_CREDIT,
            'status' => RentingReferralPointLedger::STATUS_AVAILABLE,
            'points' => 100,
            'available_from' => now()->addDays(10),
        ]);

        $this->assertFalse($row->isSpendable());

        $row->released_early_at = now();
        $this->assertTrue($row->isSpendable());
    }

    public function test_friendly_status_hides_staff_language(): void
    {
        $referral = new RentingReferral(['status' => RentingReferral::STATUS_SUBMITTED]);
        $this->assertSame('Sent', $referral->friendlyStatus());

        $referral->status = RentingReferral::STATUS_REVIEW;
        $this->assertSame('Sent', $referral->friendlyStatus());
    }

    public function test_invoice_redemption_lookup_does_not_throw(): void
    {
        $service = app(RentingReferralService::class);

        $this->assertFalse($service->invoiceHasReferralRedemption(0));
        $this->assertCount(0, $service->spendableReferrals(0));
        $this->assertTrue($service->checkIsHealthy('referrer_qualified', true));
        $this->assertFalse($service->checkIsHealthy('referrer_qualified', false));
        $this->assertTrue($service->checkIsHealthy('duplicate', false));
        $this->assertFalse($service->checkIsHealthy('duplicate', true));
    }

    public function test_only_thiago_and_super_admins_can_investigate(): void
    {
        $thiago = new \App\Models\User;
        $thiago->forceFill(['email' => 'thiago@neguinhomotors.co.uk']);
        $thiago->id = 1;
        $this->assertTrue(\App\Support\RentingReferralAccess::canInvestigate($thiago));

        $byStaffId = new \App\Models\User;
        $byStaffId->forceFill(['email' => 'not-the-director@example.test']);
        $byStaffId->id = 66;
        $this->assertTrue(\App\Support\RentingReferralAccess::canInvestigate($byStaffId));

        $other = new \App\Models\User;
        $other->forceFill(['email' => 'front-desk@example.test']);
        $other->id = 999001;
        $this->assertFalse(\App\Support\RentingReferralAccess::canInvestigate($other));
    }
}
