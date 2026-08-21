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
        $this->assertSame('Under review', $referral->friendlyStatus());
    }

    public function test_invoice_redemption_lookup_does_not_throw(): void
    {
        $service = app(RentingReferralService::class);

        $this->assertFalse($service->invoiceHasReferralRedemption(0));
    }
}
