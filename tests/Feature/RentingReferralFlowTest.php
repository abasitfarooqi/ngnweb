<?php

namespace Tests\Feature;

use App\Livewire\FluxAdmin\Pages\Rentals\ReferralIndex;
use App\Models\BookingInvoice;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\RentingBooking;
use App\Models\RentingReferral;
use App\Models\RentingReferralPointLedger;
use App\Models\RentingTransaction;
use App\Models\TransactionType;
use App\Models\User;
use App\Services\Renting\RentingReferralService;
use App\Support\RentingReferralAccess;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class RentingReferralFlowTest extends TestCase
{
    use DatabaseTransactions;

    private RentingReferralService $service;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('renting_referrals')
            || ! Schema::hasTable('customers')
            || ! Schema::hasTable('renting_bookings')
            || ! Schema::hasTable('booking_invoices')) {
            $this->markTestSkipped('Rental referral tables are not migrated.');
        }

        Mail::fake();
        $this->service = app(RentingReferralService::class);
    }

    public function test_public_landing_stores_the_referral_code_in_session(): void
    {
        $referrer = $this->makeEligibleReferrer();
        $referral = $this->service->create($referrer, [
            'name' => 'Alex Friend',
            'phone' => $this->uniqueMobile(),
            'email' => 'alex-'.$this->uniq().'@example.test',
        ]);

        $this->get('/rentals/refer/'.$referral->referral_code)
            ->assertOk()
            ->assertSessionHas('renting_referral_code', $referral->referral_code);
    }

    public function test_staff_index_stats_stay_the_same_on_every_tab(): void
    {
        $staff = User::query()->where('is_admin', 1)->orderBy('id')->first();
        if (! $staff) {
            $this->markTestSkipped('No admin user.');
        }

        $this->actingAs($staff);

        $page = Livewire::test(ReferralIndex::class);

        foreach (['programme', 'direct', 'all'] as $view) {
            $page->call('setView', $view)
                ->assertSee('Weeks given')
                ->assertSee('£ given')
                ->assertSee('Programme weeks')
                ->assertSee('Direct weeks')
                ->assertSee('Waiting for staff')
                ->assertSee('Points ready')
                ->assertSee('Points not ready')
                ->assertSee('Need a look')
                ->assertSee('These boxes stay the same')
                ->assertDontSee('Direct applied')
                ->assertDontSee('Redeemed value');
        }
    }

    public function test_investigation_dashboard_is_visible_to_an_investigator(): void
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

        $this->get(route('flux-admin.rental-referral-investigation.index'))
            ->assertOk()
            ->assertSee('Referral investigation');

        Livewire::test(\App\Livewire\FluxAdmin\Pages\Rentals\ReferralInvestigation::class)
            ->assertSee('Referral investigation')
            ->assertSee('£ given')
            ->assertSee('Staff direct')
            ->call('setKind', 'direct')
            ->assertSee('£ given')
            ->call('setKind', 'programme')
            ->assertSee('Programme chain');

        $blocked = User::query()->where('is_admin', 1)->orderBy('id')->get()
            ->first(fn (User $user) => ! RentingReferralAccess::canInvestigate($user));
        if ($blocked) {
            $this->actingAs($blocked)
                ->get(route('flux-admin.rental-referral-investigation.index'))
                ->assertForbidden();
        }
    }

    public function test_ineligible_renter_cannot_refer(): void
    {
        $customer = $this->makeCustomer('Pat', 'Solo', $this->uniqueMobile());

        $this->expectException(ValidationException::class);
        $this->service->create($customer, [
            'name' => 'Alex Friend',
            'phone' => $this->uniqueMobile(),
        ]);
    }

    public function test_self_referral_is_rejected(): void
    {
        $referrer = $this->makeEligibleReferrer();

        $this->expectException(ValidationException::class);
        $this->service->create($referrer, [
            'name' => $referrer->first_name,
            'phone' => $referrer->phone,
        ]);
    }

    public function test_full_flow_submit_register_match_qualify(): void
    {
        $friendPhone = $this->uniqueMobile();
        $friendEmail = 'friend-'.$this->uniq().'@example.test';

        $referrer = $this->makeEligibleReferrer();
        $referral = $this->service->create($referrer, [
            'name' => 'Alex Friend',
            'phone' => $friendPhone,
            'email' => $friendEmail,
        ]);

        $this->assertSame(RentingReferral::STATUS_SUBMITTED, $referral->status);
        $this->assertSame($referrer->id, $referral->referrer_customer_id);
        $this->assertNull($referral->referred_customer_id);
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{8}$/', $referral->referral_code);

        $friend = $this->makeCustomer('Alex', 'Friend', $friendPhone, $friendEmail);
        $this->service->syncCustomer($friend);

        $referral->refresh();
        $this->assertSame($friend->id, $referral->referred_customer_id);
        $this->assertSame(RentingReferral::STATUS_MATCHED, $referral->status);

        $this->makePaidWeeklyInvoice($friend, now());
        $this->service->syncCustomer($friend);

        $referral->refresh();
        $this->assertSame(RentingReferral::STATUS_REVIEW, $referral->status);
        $this->assertNotNull($referral->referred_qualifying_invoice_id);

        $credit = $referral->ledger()->where('direction', 'credit')->first();
        $this->assertNotNull($credit);
        $this->assertSame(RentingReferralPointLedger::STATUS_PENDING, $credit->status);
        $this->assertSame(100, (int) $credit->points);
        $this->assertSame($referrer->id, $credit->customer_id);
        $this->assertSame(100, $this->service->pendingPoints((int) $referrer->id));
        $this->assertSame(0, $this->service->availablePoints((int) $referrer->id));
    }

    public function test_prior_renter_is_cancelled_as_already_rented(): void
    {
        $friendPhone = $this->uniqueMobile();
        $friend = $this->makeCustomer('Sam', 'Existing', $friendPhone);
        $this->makePaidWeeklyInvoice($friend, now()->subMonths(2));

        $referrer = $this->makeEligibleReferrer();
        $referral = $this->service->create($referrer, [
            'name' => 'Sam Existing',
            'phone' => $friendPhone,
        ]);

        $this->assertSame(RentingReferral::STATUS_CANCELLED, $referral->fresh()->status);
        $this->assertSame('already_rented', $referral->fresh()->review_reason);
    }

    public function test_second_referrer_loses_to_first_match(): void
    {
        $friendPhone = $this->uniqueMobile();
        $first = $this->makeEligibleReferrer();
        $second = $this->makeEligibleReferrer();

        $winner = $this->service->create($first, [
            'name' => 'Alex Friend',
            'phone' => $friendPhone,
        ]);
        $loser = $this->service->create($second, [
            'name' => 'Alex Friend',
            'phone' => $friendPhone,
        ]);

        $friend = $this->makeCustomer('Alex', 'Friend', $friendPhone);
        $this->service->syncCustomer($friend);

        $this->assertSame($friend->id, $winner->fresh()->referred_customer_id);
        $this->assertSame(RentingReferral::STATUS_MATCHED, $winner->fresh()->status);
        $this->assertSame(RentingReferral::STATUS_CANCELLED, $loser->fresh()->status);
        $this->assertSame('already_attributed', $loser->fresh()->review_reason);
    }

    public function test_redeemable_invoices_skip_paid_already_rewarded_and_future_weeks(): void
    {
        $referrer = $this->makeEligibleReferrer();
        $bookingId = (int) \App\Models\RentingBooking::query()->where('customer_id', $referrer->id)->value('id');
        $userId = (int) (User::query()->orderBy('id')->value('id') ?: 1);

        $dueUnpaid = BookingInvoice::query()->create([
            'booking_id' => $bookingId,
            'user_id' => $userId,
            'invoice_date' => now()->subDay()->toDateString(),
            'amount' => 70,
            'deposit' => 0,
            'is_posted' => true,
            'is_paid' => false,
            'state' => 'Open',
        ]);
        $futureUnpaid = BookingInvoice::query()->create([
            'booking_id' => $bookingId,
            'user_id' => $userId,
            'invoice_date' => now()->addWeek()->toDateString(),
            'amount' => 70,
            'deposit' => 0,
            'is_posted' => true,
            'is_paid' => false,
            'state' => 'Open',
        ]);

        $ids = $this->service->redeemableInvoices($referrer)->pluck('id');

        $this->assertTrue($ids->contains($dueUnpaid->id));
        $this->assertFalse($ids->contains($futureUnpaid->id));

        $this->expectException(\RuntimeException::class);
        $this->service->assertInvoiceCanTakeFreeWeek($futureUnpaid);
    }

    public function test_last_paid_invoice_history_lists_payment_fields_or_eligibility_message(): void
    {
        $withPaid = $this->makeEligibleReferrer();
        $found = $this->service->lastPaidInvoiceHistoryForCustomer((int) $withPaid->id);

        $this->assertFalse($found['missing']);
        $this->assertNotNull($found['booking_id']);
        $this->assertNotEmpty($found['invoices']);
        $this->assertArrayHasKey('invoice_id', $found['invoices'][0]);
        $this->assertArrayHasKey('transaction_no', $found['invoices'][0]);
        $this->assertArrayHasKey('invoice_amount', $found['invoices'][0]);
        $this->assertArrayHasKey('paid_amount', $found['invoices'][0]);
        $this->assertArrayHasKey('paid_date', $found['invoices'][0]);
        $this->assertArrayHasKey('invoice_state', $found['invoices'][0]);
        $this->assertSame(150.0, (float) $found['invoices'][0]['invoice_amount']);

        $withoutPaid = $this->makeCustomer('No', 'Invoice', $this->uniqueMobile());
        $missing = $this->service->lastPaidInvoiceHistoryForCustomer((int) $withoutPaid->id);

        $this->assertTrue($missing['missing']);
        $this->assertSame([], $missing['invoices']);
        $this->assertSame(
            \App\Models\RentingFreeWeekAward::ELIGIBILITY_FALLBACK,
            $missing['message']
        );
    }

    public function test_direct_free_week_redeems_unused_programme_points(): void
    {
        if (! Schema::hasTable('renting_free_week_awards') || ! Schema::hasTable('renting_transactions')) {
            $this->markTestSkipped('Free week award tables are not migrated.');
        }

        $referrer = $this->makeEligibleReferrer();
        $friendPhone = $this->uniqueMobile();
        $referral = $this->service->create($referrer, [
            'name' => 'Alex Friend',
            'phone' => $friendPhone,
        ]);
        $friend = $this->makeCustomer('Alex', 'Friend', $friendPhone);
        $referral->update([
            'referred_customer_id' => $friend->id,
            'status' => RentingReferral::STATUS_APPROVED,
        ]);

        RentingReferralPointLedger::query()->create([
            'customer_id' => $referrer->id,
            'referral_id' => $referral->id,
            'direction' => RentingReferralPointLedger::DIRECTION_CREDIT,
            'status' => RentingReferralPointLedger::STATUS_AVAILABLE,
            'points' => 100,
            'available_from' => now()->subDay(),
        ]);

        $unpaid = $this->makeUnpaidWeeklyInvoice($friend);
        $staffId = (int) (User::query()->orderBy('id')->value('id') ?: 1);

        $this->service->applyDirectFreeWeek($unpaid, $referrer, $staffId, 'testtesttest');

        $credit = $referral->ledger()->where('direction', 'credit')->first();
        $this->assertSame(RentingReferralPointLedger::STATUS_REDEEMED, $credit?->status);
        $this->assertSame(0, $this->service->availablePoints((int) $referrer->id));
        $this->assertSame(100, $this->service->portalRedeemedPoints((int) $referrer->id));
        $this->assertSame(1, $this->service->appliedFreeWeekCountForCustomer((int) $referrer->id));

        $this->expectException(\RuntimeException::class);
        $this->service->applyDirectFreeWeek($this->makeUnpaidWeeklyInvoice($friend), $referrer, $staffId, 'secondweekok');
    }

    private function makeEligibleReferrer(): Customer
    {
        $customer = $this->makeCustomer('Riley', 'Renter', $this->uniqueMobile());
        $this->makePaidWeeklyInvoice($customer, now()->subWeeks(3));

        return $customer->fresh();
    }

    private function makePaidWeeklyInvoice(Customer $customer, $startDate): BookingInvoice
    {
        $userId = (int) (User::query()->orderBy('id')->value('id') ?: 1);

        $booking = RentingBooking::query()->create([
            'customer_id' => $customer->id,
            'user_id' => $userId,
            'start_date' => $startDate,
            'state' => 'Active',
            'is_posted' => true,
            'deposit' => 0,
        ]);

        $invoice = BookingInvoice::query()->create([
            'booking_id' => $booking->id,
            'user_id' => $userId,
            'invoice_date' => now()->toDateString(),
            'amount' => 150,
            'deposit' => 0,
            'is_posted' => true,
            'is_paid' => true,
            'paid_date' => now()->toDateString(),
            'state' => 'Completed',
        ]);

        if (Schema::hasTable('renting_transactions')) {
            $typeId = TransactionType::query()->orderBy('id')->value('id');
            $methodId = PaymentMethod::query()->orderBy('id')->value('id');

            RentingTransaction::query()->create(array_filter([
                'transaction_date' => now(),
                'booking_id' => $booking->id,
                'invoice_id' => $invoice->id,
                'transaction_type_id' => $typeId,
                'payment_method_id' => $methodId,
                'user_id' => $userId,
                'amount' => 150,
                'notes' => 'RentingReferralFlowTest',
            ], fn ($value) => $value !== null));
        }

        return $invoice;
    }

    private function makeUnpaidWeeklyInvoice(Customer $customer): BookingInvoice
    {
        $userId = (int) (User::query()->orderBy('id')->value('id') ?: 1);

        $booking = RentingBooking::query()->create([
            'customer_id' => $customer->id,
            'user_id' => $userId,
            'start_date' => now()->subWeek(),
            'state' => 'Active',
            'is_posted' => true,
            'deposit' => 0,
        ]);

        return BookingInvoice::query()->create([
            'booking_id' => $booking->id,
            'user_id' => $userId,
            'invoice_date' => now()->toDateString(),
            'amount' => 150,
            'deposit' => 0,
            'is_posted' => true,
            'is_paid' => false,
            'state' => 'Awaiting Payment',
        ]);
    }

    private function makeCustomer(string $first, string $last, string $phone, ?string $email = null): Customer
    {
        return Customer::query()->create([
            'first_name' => $first,
            'last_name' => $last,
            'email' => $email ?: strtolower($first).'-'.$this->uniq().'@example.test',
            'phone' => $phone,
            'whatsapp' => $phone,
            'dob' => '1990-01-01',
            'address' => 'Not Provided',
            'postcode' => 'SE1 1AA',
            'emergency_contact' => 'Not Provided',
            'city' => 'London',
            'country' => 'United Kingdom',
            'nationality' => 'British',
            'license_number' => 'TEST'.$this->uniq(),
            'license_expiry_date' => now()->addYears(2),
            'license_issuance_authority' => 'DVLA',
            'license_issuance_date' => now()->subYears(2),
            'is_register' => true,
            'is_club' => false,
        ]);
    }

    private function uniqueMobile(): string
    {
        return '07123'.str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function uniq(): string
    {
        return substr(str_replace('.', '', uniqid('', true)), -10);
    }
}
