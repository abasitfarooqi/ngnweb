<?php

namespace Tests\Feature;

use App\Livewire\FluxAdmin\Partials\Rentals\WeeklyUpdatesPanel;
use App\Mail\RentingInvoiceUpdateReminderMail;
use App\Models\BookingInvoice;
use App\Models\Customer;
use App\Models\RentingBooking;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class RentingInvoiceUpdateNoteTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('renting_weekly_updates') || ! Schema::hasTable('booking_invoices')) {
            $this->markTestSkipped('Rental weekly update tables are not migrated.');
        }
    }

    public function test_adding_an_invoice_note_does_not_email_the_customer(): void
    {
        Mail::fake();

        $staff = User::query()->where('is_admin', 1)->orderBy('id')->first();
        if (! $staff) {
            $this->markTestSkipped('No admin user.');
        }

        $this->actingAs($staff);

        $booking = $this->makeBooking();
        $invoice = $this->makeInvoice($booking);

        Livewire::test(WeeklyUpdatesPanel::class, [
            'bookingId' => $booking->id,
            'invoiceId' => $invoice->id,
        ])
            ->set('newNote', 'Customer said they will pay tomorrow.')
            ->call('addInvoiceUpdate')
            ->assertHasNoErrors()
            ->assertSet('flashMessage', 'Update added.')
            ->assertDontSee('Email this note to the customer');

        $this->assertDatabaseHas('renting_weekly_updates', [
            'booking_id' => $booking->id,
            'invoice_id' => $invoice->id,
            'note' => 'Customer said they will pay tomorrow.',
        ]);

        Mail::assertNothingSent();
        Mail::assertNotQueued(RentingInvoiceUpdateReminderMail::class);
    }

    public function test_invoices_tab_does_not_show_the_booking_wide_weekly_updates_list(): void
    {
        $blade = file_get_contents(resource_path('views/flux-admin/partials/rentals/invoices-tab.blade.php'));

        $this->assertIsString($blade);
        $this->assertStringNotContainsString('booking-weekly-updates-', $blade);
        $this->assertStringNotContainsString('Weekly rental updates', $blade);
        $this->assertStringContainsString('invoice-weekly-updates-', $blade);
    }

    private function makeBooking(): RentingBooking
    {
        $userId = (int) (User::query()->orderBy('id')->value('id') ?: 1);
        $customer = Customer::query()->create([
            'first_name' => 'Note',
            'last_name' => 'Chase',
            'email' => 'note-chase-'.substr(uniqid('', true), -10).'@example.test',
            'phone' => '07123'.str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'whatsapp' => '07123456789',
            'dob' => '1990-01-01',
            'address' => 'Not Provided',
            'postcode' => 'SE1 1AA',
            'emergency_contact' => 'Not Provided',
            'city' => 'London',
            'country' => 'United Kingdom',
            'nationality' => 'British',
            'license_number' => 'NOTE'.substr(uniqid('', true), -10),
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

    private function makeInvoice(RentingBooking $booking): BookingInvoice
    {
        return BookingInvoice::query()->create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'invoice_date' => now()->subDay()->toDateString(),
            'amount' => 75,
            'deposit' => 0,
            'is_posted' => true,
            'is_paid' => false,
            'paid_date' => null,
            'state' => 'Awaiting Payment',
        ]);
    }
}
