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

    public function test_build_weekly_date_plan_realigns_unpaid_invoices_from_anchor_only(): void
    {
        $invoices = [
            $this->invoiceForPlan(1, '2026-01-02', true),
            $this->invoiceForPlan(2, '2026-01-09', false),
            $this->invoiceForPlan(3, '2026-01-16', false),
            $this->invoiceForPlan(4, '2026-01-23', true),
            $this->invoiceForPlan(5, '2026-01-30', false),
        ];

        $plan = $this->service->buildWeeklyDatePlan($invoices, 2, '2026-01-10');

        $this->assertSame([
            2 => '2026-01-10',
            3 => '2026-01-17',
            5 => '2026-01-24',
        ], $plan);
    }

    public function test_build_weekly_date_plan_keeps_earlier_unpaid_invoices_unchanged(): void
    {
        $invoices = [
            $this->invoiceForPlan(10, '2026-02-06', false),
            $this->invoiceForPlan(11, '2026-02-13', false),
            $this->invoiceForPlan(12, '2026-02-20', false),
        ];

        $plan = $this->service->buildWeeklyDatePlan($invoices, 11, '2026-02-15');

        $this->assertSame([
            11 => '2026-02-15',
            12 => '2026-02-22',
        ], $plan);
    }

    public function test_build_schedule_date_plan_locks_paid_invoices_and_moves_unpaid_to_start_weekday(): void
    {
        $invoices = [
            $this->invoiceForPlan(6270, '2026-04-14', true),
            $this->invoiceForPlan(6276, '2026-04-21', true),
            $this->invoiceForPlan(6277, '2026-04-28', true),
            $this->invoiceForPlan(6278, '2026-05-05', false),
            $this->invoiceForPlan(6299, '2026-05-12', false),
            $this->invoiceForPlan(6321, '2026-05-19', false),
        ];

        $plan = $this->service->buildScheduleDatePlan($invoices, '2026-04-13');

        $this->assertSame([
            6278 => '2026-05-04',
            6299 => '2026-05-11',
            6321 => '2026-05-18',
        ], $plan);
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

    public function test_invoice_amount_for_weekly_rent_preserves_deposit_component(): void
    {
        $invoice = new BookingInvoice([
            'amount' => 300,
            'deposit' => 200,
        ]);

        $this->assertSame(320.0, $this->service->invoiceAmountForWeeklyRent($invoice, 120));
    }

    public function test_invoice_amount_for_weekly_rent_handles_weekly_only_invoice(): void
    {
        $invoice = new BookingInvoice([
            'amount' => 80,
            'deposit' => 0,
        ]);

        $this->assertSame(95.5, $this->service->invoiceAmountForWeeklyRent($invoice, 95.50));
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

    private function invoiceForPlan(int $id, string $invoiceDate, bool $isPaid): BookingInvoice
    {
        $invoice = new BookingInvoice([
            'invoice_date' => $invoiceDate,
            'is_paid' => $isPaid,
        ]);
        $invoice->id = $id;

        return $invoice;
    }
}
