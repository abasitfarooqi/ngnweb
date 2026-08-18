<?php

namespace App\Services\Communications;

use App\Mail\ContactSubmission;
use App\Mail\RentalEndedWithPendingsMail;
use App\Models\CommunicationDefinition;
use App\Support\MailpitMigratedPreviewData;
use App\Support\UniversalMailPayload;
use Illuminate\Support\Facades\View;
use Throwable;

final class CommunicationEmailPreviewRenderer
{
    /**
     * @return array{
     *     available: bool,
     *     subject: string,
     *     html: string,
     *     source: string,
     *     error: string|null,
     *     uses_sample_data: bool
     * }
     */
    public function forDefinition(CommunicationDefinition $definition): array
    {
        $subject = $definition->name;

        try {
            if ($definition->key === 'rental.ended_with_pendings') {
                $sample = MailpitMigratedPreviewData::withLatestRentalBooking([]);
                $bookingId = $sample['booking_id'] ?? 1001;
                $mailable = new RentalEndedWithPendingsMail([
                    'booking_id' => $bookingId,
                    'customer_name' => $sample['customer_name'] ?? 'Preview Customer',
                    'staff_name' => 'Preview Staff',
                    'staff_id' => 1,
                    'collect_date' => now()->format('Y-m-d'),
                    'collect_time' => '14:30',
                    'rental' => 120.0,
                    'additional' => 35.5,
                    'pcn' => 60.0,
                    'total' => 215.5,
                    'show_url' => 'https://example.com/flux-admin/rentals/'.$bookingId,
                ]);

                return $this->available(
                    subject: 'Rental ended with outstanding balances — booking #'.$bookingId,
                    html: $mailable->render(),
                    source: RentalEndedWithPendingsMail::class,
                );
            }

            if ($definition->key === 'mot.status.result_email') {
                $mailable = new ContactSubmission(
                    senderName: 'NGN Motors MOT Checker',
                    senderEmail: config('mail.from.address', 'customerservice@neguinhomotors.co.uk'),
                    phone: '',
                    topic: 'MOT status for AB12 CDE',
                    messageBody: "MOT status for: AB12 CDE\n\nMake: Honda\nMOT status: Valid\nMOT expires: ".now()->addMonths(6)->format('Y-m-d')."\nRoad tax status: Taxed\n",
                    branchName: '',
                );

                return $this->available(
                    subject: 'MOT status for AB12 CDE',
                    html: $mailable->render(),
                    source: ContactSubmission::class,
                );
            }

            if ($definition->key === 'order.shipped') {
                return $this->available(
                    subject: 'Order Shipped',
                    html: View::make('emails.templates.agreement-controller-universal', [
                        'mailData' => [
                            'title' => 'Order Shipped',
                            'body' => 'Your order has been shipped. If you have questions, contact customerservice@neguinhomotors.co.uk.',
                            'customer_name' => 'Preview Customer',
                        ],
                    ])->render(),
                    source: 'emails.templates.agreement-controller-universal',
                );
            }

            if ($relative = $this->migratedViewForKey($definition->key)) {
                return $this->renderFromMigratedRelative($relative, $subject, $definition->key);
            }

            if ($definition->template_view && View::exists($definition->template_view)) {
                $mailData = $this->sampleMailDataForKey($definition->key, $definition->name);

                return $this->available(
                    subject: (string) ($mailData['title'] ?? $subject),
                    html: View::make($definition->template_view, ['mailData' => $mailData])->render(),
                    source: $definition->template_view,
                );
            }

            return $this->unavailable('No preview mapping exists for this communication yet.');
        } catch (Throwable $e) {
            report($e);

            return $this->unavailable($e->getMessage());
        }
    }

    /**
     * @return array{
     *     available: bool,
     *     subject: string,
     *     html: string,
     *     source: string,
     *     error: string|null,
     *     uses_sample_data: bool
     * }
     */
    private function renderFromMigratedRelative(string $relative, string $subject, string $key): array
    {
        if (UniversalMailPayload::migratedEmailsPhysicalBladePath($relative) === null
            && ! View::exists(MailpitMigratedPreviewData::fullViewName($relative))) {
            return $this->unavailable("Migrated email view [{$relative}] is not registered.");
        }

        $viewData = MailpitMigratedPreviewData::viewDataFor($relative);
        $viewData = array_merge($viewData, $this->viewDataOverridesForKey($key));
        $overrides = array_merge(
            ['title' => $subject, 'greetingName' => (string) ($viewData['customer_name'] ?? 'Preview Customer')],
            $this->titleOverridesForKey($key),
        );

        $mailData = UniversalMailPayload::fromMigratedEmailRelative($relative, $viewData, $overrides);
        $html = View::make('emails.templates.agreement-controller-universal', ['mailData' => $mailData])->render();

        return $this->available(
            subject: (string) ($mailData['title'] ?? $subject),
            html: $html,
            source: MailpitMigratedPreviewData::fullViewName($relative),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function sampleMailDataForKey(string $key, string $name): array
    {
        $base = MailpitMigratedPreviewData::viewDataFor(match ($key) {
            'customer.document.request' => 'upload-documents',
            'contract.hire.issued' => 'sale-agreement',
            'rental.agreement.issued' => 'rental-agreement',
            'loyalty.policy.issued' => 'loyalty-scheme-policy',
            'purchase.invoice.issued' => 'purchase-invoice',
            'rental.terminated' => 'rental-terminate-v1',
            default => 'rental-agreement',
        });

        if (isset($base['mailData']) && is_array($base['mailData'])) {
            return $base['mailData'];
        }

        if (isset($base['title'], $base['body'])) {
            return [
                'title' => (string) $base['title'],
                'body' => (string) $base['body'],
                'customer_name' => 'Preview Customer',
                'url' => 'https://example.com/customer/preview-action',
            ];
        }

        return [
            'title' => $name,
            'body' => 'Sample preview content for staff review. Real customer emails use live booking, payment, or document data.',
            'customer_name' => 'Preview Customer',
            'url' => 'https://example.com/customer/preview-action',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function titleOverridesForKey(string $key): array
    {
        return match ($key) {
            'finance.contract.review' => ['title' => 'Finance Contract Review'],
            'rental.agreement.review' => ['title' => 'Rental Contract Review'],
            'purchase.invoice.review' => ['title' => 'Purchase Invoice Review'],
            'order.shipped' => ['title' => 'Order Shipped'],
            'rental.deposit.return' => ['title' => 'Rental Deposit Return'],
            default => [],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function viewDataOverridesForKey(string $key): array
    {
        return match ($key) {
            'rental.deposit.return' => [
                'title' => 'Rental Deposit Return',
                'subtitle' => 'Confirmation of rental deposit return.',
                'body' => 'Please find your deposit return details below.',
                'payment_method' => 'Deposit return',
                'amount' => $this->depositPreviewAmount(),
                'invoice_amount' => $this->depositPreviewAmount(true),
                'invoice_amount_label' => 'Deposit Amount',
                'amount_label' => 'Amount Returned',
                'show_remaining_balance' => false,
                'invoice_status_label' => 'Deposit returned',
                'receipt_message' => 'Your rental deposit return has been recorded by NGN Motors.',
                'notes_label' => 'Reason of deduction / notes',
                'notes' => 'Sample preview of a deposit return after rental close.',
                'transaction_id' => 'N/A',
            ],
            default => [],
        };
    }

    private function depositPreviewAmount(bool $originalDeposit = false): float
    {
        $sample = MailpitMigratedPreviewData::withLatestRentalBooking([]);
        $deposit = (float) ($sample['deposit_amount'] ?? $sample['invoice_amount'] ?? 200);

        return $originalDeposit ? $deposit : (float) ($sample['amount'] ?? $deposit);
    }

    private function migratedViewForKey(string $key): ?string
    {
        return match ($key) {
            'service.booking.confirmation' => 'service-booking-confirmation',
            'customer.appointment.notification' => 'servicesandbooking.customer_appointment',
            'customer.document.request' => 'upload-documents',
            'invoice.due.customer_reminder' => 'due_invoice_customer',
            'finance.contract.review' => 'rental-agreement-sign',
            'contract.hire.issued' => 'sale-agreement',
            'rental.instalment.notification' => 'instalment_notification',
            'vehicle.logbook.transfer' => 'logbook-transfer',
            'loyalty.policy.issued' => 'loyalty-scheme-policy',
            'mot.booking.created' => 'mot_booking_confirmation',
            'mot.booking.cancelled' => 'mot_cancelled',
            'mot.booking.completed' => 'mot_completed',
            'mot.reminder' => 'mot_notifier_30_and_10_days',
            'mot.expiry.ten_day_reminder' => 'mot-10days',
            'mot.tax.notification' => 'TAX.30days',
            'delivery.order.confirmed' => 'motorbike_transport_delivery_order_confirmed',
            'delivery.order.cancelled' => 'motorbike_transport_delivery_order_cancelled',
            'delivery.order.enquiry' => 'motorbike_transport_delivery_order_enquiry',
            'motorcycle.recovery.request' => 'motorcycle_recovery',
            'club.member.welcome' => 'ngnclub_new_subscriber_email',
            'club.batch.credentials' => 'club_batch_user_credentials',
            'order.shipped' => null,
            'rental.other_charge.receipt' => 'othercharges-receipt',
            'pcn.payment_reminder' => 'pcn-notify',
            'pcn.police.payment_reminder' => 'pcn-notify-police',
            'pcn.job.email' => 'pcn.t1',
            'mot.payment.reminder' => 'payment_reminder',
            'purchase.invoice.issued' => 'purchase-invoice',
            'purchase.invoice.review' => 'purchase-invoice-sign',
            'quote.request' => 'quote-request',
            'rental.invoice.reminder' => 'rent_notification',
            'rental.agreement.issued' => 'rental-agreement',
            'rental.agreement.review' => 'rental-agreement-sign',
            'rental.due' => 'RentalDueView',
            'rental.other_charge.reminder' => 'rental-other-charge-reminder',
            'rental.payment.receipt' => 'rental-payment-receipt',
            'rental.deposit.return' => 'rental-payment-receipt',
            'rental.payment.reversed' => 'rental-payment-reversed-notice',
            'rental.terminated' => 'rental-terminate-v1',
            'delivery.vehicle.pickup_estimate' => 'vehicle_delivery_order_confirmation',
            'ecommerce.customer.registered' => 'ecommerce.register',
            'ecommerce.order.confirmed' => 'ecommerce.order-confirmed',
            'ecommerce.order.processing' => 'ecommerce.order-process',
            'ecommerce.order.ready_to_collect' => 'ecommerce.order-ready-to-collect',
            'ecommerce.order.refunded' => 'ecommerce.order-refund',
            default => null,
        };
    }

    /**
     * @return array{
     *     available: bool,
     *     subject: string,
     *     html: string,
     *     source: string,
     *     error: string|null,
     *     uses_sample_data: bool
     * }
     */
    private function available(string $subject, string $html, string $source): array
    {
        return [
            'available' => true,
            'subject' => $subject,
            'html' => $html,
            'source' => $source,
            'error' => null,
            'uses_sample_data' => true,
        ];
    }

    /**
     * @return array{
     *     available: bool,
     *     subject: string,
     *     html: string,
     *     source: string,
     *     error: string|null,
     *     uses_sample_data: bool
     * }
     */
    private function unavailable(string $error): array
    {
        return [
            'available' => false,
            'subject' => '',
            'html' => '',
            'source' => '',
            'error' => $error,
            'uses_sample_data' => true,
        ];
    }
}
