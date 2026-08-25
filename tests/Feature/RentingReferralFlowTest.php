<?php

namespace Tests\Feature;

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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
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
