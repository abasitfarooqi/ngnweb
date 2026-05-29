<?php

namespace Tests\Unit;

use App\Models\BookingInvoice;
use App\Services\RentingInvoiceSyncService;
use Carbon\Carbon;
use Tests\TestCase;

class RentingInvoiceSyncServiceTest extends TestCase
{
    private RentingInvoiceSyncService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RentingInvoiceSyncService;
    }

    public function test_compute_target_dates_includes_today_when_payment_day_is_today(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-22')); // Friday

        $dates = $this->service->computeTargetDates(Carbon::FRIDAY);

        $this->assertSame('2026-05-22', $dates[0]->toDateString());
        $this->assertSame('2026-05-29', $dates[1]->toDateString());
        $this->assertSame('2026-06-05', $dates[2]->toDateString());

        Carbon::setTestNow();
    }

    public function test_compute_target_dates_starts_on_next_matching_weekday(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21')); // Thursday

        $dates = $this->service->computeTargetDates(Carbon::FRIDAY);

        $this->assertSame('2026-05-22', $dates[0]->toDateString());
        $this->assertSame('2026-05-29', $dates[1]->toDateString());
        $this->assertSame('2026-06-05', $dates[2]->toDateString());

        Carbon::setTestNow();
    }

    public function test_should_delete_future_invoice_on_wrong_weekday(): void
    {
        $invoice = new BookingInvoice([
            'invoice_date' => '2026-05-25', // Monday
        ]);

        $targetDateStrings = ['2026-05-23', '2026-05-30', '2026-06-06']; // Saturdays

        $this->assertTrue(
            $this->service->shouldDeleteFutureInvoice($invoice, Carbon::SATURDAY, $targetDateStrings)
        );
    }

    public function test_should_delete_future_invoice_when_correct_weekday_but_not_in_target_set(): void
    {
        $invoice = new BookingInvoice([
            'invoice_date' => '2026-06-13', // Saturday, fourth week ahead
        ]);

        $targetDateStrings = ['2026-05-23', '2026-05-30', '2026-06-06'];

        $this->assertTrue(
            $this->service->shouldDeleteFutureInvoice($invoice, Carbon::SATURDAY, $targetDateStrings)
        );
    }

    public function test_should_keep_future_invoice_on_target_date(): void
    {
        $invoice = new BookingInvoice([
            'invoice_date' => '2026-05-30',
        ]);

        $targetDateStrings = ['2026-05-23', '2026-05-30', '2026-06-06'];

        $this->assertFalse(
            $this->service->shouldDeleteFutureInvoice($invoice, Carbon::SATURDAY, $targetDateStrings)
        );
    }

    public function test_past_unpaid_invoice_is_not_future_deletable(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-22'));

        $invoice = new BookingInvoice([
            'invoice_date' => '2026-05-15',
            'is_paid' => false,
        ]);

        $this->assertFalse($this->callIsFutureDeletable($invoice, [], '2026-05-22'));

        Carbon::setTestNow();
    }

    public function test_paid_invoice_is_not_future_deletable(): void
    {
        $invoice = new BookingInvoice([
            'invoice_date' => '2026-05-29',
            'is_paid' => true,
        ]);

        $this->assertFalse($this->callIsFutureDeletable($invoice, [], Carbon::today()->toDateString()));
    }

    public function test_invoice_with_paid_date_is_not_future_deletable(): void
    {
        $invoice = new BookingInvoice([
            'invoice_date' => '2026-05-29',
            'is_paid' => false,
            'paid_date' => '2026-05-20',
        ]);

        $this->assertFalse($this->callIsFutureDeletable($invoice, [], Carbon::today()->toDateString()));
    }

    public function test_invoice_with_transaction_is_not_future_deletable(): void
    {
        $invoice = new BookingInvoice([
            'invoice_date' => '2026-05-29',
            'is_paid' => false,
        ]);
        $invoice->id = 42;

        $this->assertFalse($this->callIsFutureDeletable($invoice, [42], Carbon::today()->toDateString()));
    }

    public function test_future_unpaid_invoice_without_transaction_is_deletable(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-22'));

        $invoice = new BookingInvoice([
            'invoice_date' => '2026-05-29',
            'is_paid' => false,
        ]);
        $invoice->id = 99;

        $this->assertTrue($this->callIsFutureDeletable($invoice, [], '2026-05-22'));

        Carbon::setTestNow();
    }

    /**
     * @param  array<int, int|string>  $invoiceIdsWithTransactions
     */
    private function callIsFutureDeletable(BookingInvoice $invoice, array $invoiceIdsWithTransactions, string $today): bool
    {
        $method = new \ReflectionMethod(RentingInvoiceSyncService::class, 'isFutureDeletable');
        $method->setAccessible(true);

        return $method->invoke($this->service, $invoice, $today, $invoiceIdsWithTransactions);
    }
}
