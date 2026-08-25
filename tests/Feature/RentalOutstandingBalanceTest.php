<?php

namespace Tests\Feature;

use App\Livewire\FluxAdmin\Pages\Rentals\RentalIndex;
use App\Models\BookingInvoice;
use App\Models\Customer;
use App\Models\Motorbike;
use App\Models\PaymentMethod;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Models\RentingTransaction;
use App\Models\TransactionType;
use App\Models\User;
use App\Support\RentalInvoiceTabData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class RentalOutstandingBalanceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('booking_invoices') || ! Schema::hasTable('renting_transactions')) {
            $this->markTestSkipped('Rental invoice tables are not migrated.');
        }
    }

    public function test_half_paid_due_invoice_shows_the_remaining_balance(): void
    {
        $booking = $this->makeBooking();
        $invoice = $this->makeInvoice($booking, 70, now()->subDay()->toDateString(), false);
        $this->makePayment($booking, $invoice, 30);

        $this->assertSame(40.0, RentalInvoiceTabData::outstandingForBooking((int) $booking->id));
    }

    public function test_fully_paid_via_transactions_shows_zero_even_if_invoice_is_unmarked(): void
    {
        $booking = $this->makeBooking();
        $invoice = $this->makeInvoice($booking, 70, now()->subDay()->toDateString(), false);
        $this->makePayment($booking, $invoice, 70);

        $this->assertSame(0.0, RentalInvoiceTabData::outstandingForBooking((int) $booking->id));
    }

    public function test_marked_paid_without_a_transaction_shows_zero(): void
    {
        $booking = $this->makeBooking();
        $this->makeInvoice($booking, 70, now()->subDay()->toDateString(), true);

        $this->assertSame(0.0, RentalInvoiceTabData::outstandingForBooking((int) $booking->id));
    }

    public function test_future_unpaid_invoice_is_not_outstanding_yet(): void
    {
        $booking = $this->makeBooking();
        $this->makeInvoice($booking, 70, now()->addWeek()->toDateString(), false);

        $this->assertSame(0.0, RentalInvoiceTabData::outstandingForBooking((int) $booking->id));
    }

    public function test_active_bookings_list_shows_the_remaining_balance(): void
    {
        $staff = User::query()->where('is_admin', 1)->orderBy('id')->first();
        $motorbikeId = Motorbike::query()->orderBy('id')->value('id');
        if (! $staff || ! $motorbikeId) {
            $this->markTestSkipped('Need an admin user and a motorbike.');
        }

        $booking = $this->makeBooking();
        RentingBookingItem::query()->create([
            'booking_id' => $booking->id,
            'motorbike_id' => $motorbikeId,
            'user_id' => $booking->user_id,
            'weekly_rent' => 65,
            'start_date' => now()->subWeeks(2),
            'due_date' => now()->addWeek(),
            'is_posted' => true,
        ]);
        $invoice = $this->makeInvoice($booking, 70, now()->subDay()->toDateString(), false);
        $this->makePayment($booking, $invoice, 30);

        $this->actingAs($staff);

        Livewire::test(RentalIndex::class)
            ->set('search', (string) $booking->id)
            ->assertSee('#'.$booking->id)
            ->assertSee('£40.00')
            ->assertDontSee('£70.00');
    }

    private function makeBooking(): RentingBooking
    {
        $userId = (int) (User::query()->orderBy('id')->value('id') ?: 1);
        $customer = Customer::query()->create([
            'first_name' => 'Outstanding',
            'last_name' => 'Test',
            'email' => 'outstanding-'.substr(uniqid('', true), -10).'@example.test',
            'phone' => '07123'.str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'whatsapp' => '07123456789',
            'dob' => '1990-01-01',
            'address' => 'Not Provided',
            'postcode' => 'SE1 1AA',
            'emergency_contact' => 'Not Provided',
            'city' => 'London',
            'country' => 'United Kingdom',
            'nationality' => 'British',
            'license_number' => 'TEST'.substr(uniqid('', true), -10),
            'license_expiry_date' => now()->addYears(2),
            'license_issuance_authority' => 'DVLA',
            'license_issuance_date' => now()->subYears(2),
            'is_register' => true,
            'is_club' => false,
        ]);

        return RentingBooking::query()->create([
            'customer_id' => $customer->id,
            'user_id' => $userId,
            'start_date' => now()->subWeeks(2),
            'state' => 'Active',
            'is_posted' => true,
            'deposit' => 0,
        ]);
    }

    private function makeInvoice(RentingBooking $booking, float $amount, string $invoiceDate, bool $isPaid): BookingInvoice
    {
        return BookingInvoice::query()->create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'invoice_date' => $invoiceDate,
            'amount' => $amount,
            'deposit' => 0,
            'is_posted' => true,
            'is_paid' => $isPaid,
            'paid_date' => $isPaid ? $invoiceDate : null,
            'state' => $isPaid ? 'Completed' : 'Awaiting Payment',
        ]);
    }

    private function makePayment(RentingBooking $booking, BookingInvoice $invoice, float $amount): void
    {
        $typeId = TransactionType::query()->orderBy('id')->value('id');
        $methodId = PaymentMethod::query()->orderBy('id')->value('id');

        if (! $typeId || ! $methodId) {
            $this->markTestSkipped('Payment lookup rows are missing.');
        }

        RentingTransaction::query()->create([
            'transaction_date' => now(),
            'booking_id' => $booking->id,
            'invoice_id' => $invoice->id,
            'transaction_type_id' => $typeId,
            'payment_method_id' => $methodId,
            'user_id' => $booking->user_id,
            'amount' => $amount,
            'notes' => 'RentalOutstandingBalanceTest',
        ]);
    }
}
