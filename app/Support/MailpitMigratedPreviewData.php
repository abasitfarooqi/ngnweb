<?php

namespace App\Support;

use App\Models\Ecommerce\EcOrder;
use Carbon\Carbon;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Stub view data for php artisan mail:test-mailpit --mailable=migrated --view=...
 */
final class MailpitMigratedPreviewData
{
    public const VIEW_PREFIX = 'livewire.agreements.migrated.emails.';

    /**
     * @return list<string>
     */
    public static function discoverRelativeViews(): array
    {
        $base = resource_path('views/livewire/agreements/migrated/emails');
        if (! is_dir($base)) {
            return [];
        }

        $out = [];
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $fileInfo) {
            if (! $fileInfo->isFile() || ! str_ends_with($fileInfo->getFilename(), '.blade.php')) {
                continue;
            }
            $rel = substr($fileInfo->getPathname(), strlen($base) + 1);
            $rel = str_replace(['\\', '/'], '.', $rel);
            $rel = (string) preg_replace('/\.blade\.php$/', '', $rel);
            $out[] = $rel;
        }
        sort($out);

        return $out;
    }

    public static function fullViewName(string $relativeDot): string
    {
        return self::VIEW_PREFIX.ltrim($relativeDot, '.');
    }

    /**
     * @return array<string, mixed>
     */
    public static function viewDataFor(string $relativeDot): array
    {
        $relativeDot = str_replace('/', '.', $relativeDot);

        return array_merge(self::coreStubs(), self::shapeForRelativeView($relativeDot));
    }

    /**
     * @return array<string, mixed>
     */
    private static function coreStubs(): array
    {
        return [
            'user' => (object) [
                'first_name' => 'preview',
                'last_name' => 'user',
                'full_name' => 'Preview User',
                'phone' => '07900 000000',
                'passkey' => 'preview-pass-0000',
            ],
            'customer_name' => 'Preview Customer',
            'customer' => (object) [
                'first_name' => 'Preview',
                'last_name' => 'Customer',
                'fullname' => 'Preview Customer',
                'email' => 'preview@example.com',
                'reg_no' => 'AB12 CDE',
            ],
            'customer_email' => 'preview@example.com',
            'customer_phone' => '020 0000 0000',
            'customer_id' => 1,
            'name' => 'Preview Name',
            'email' => 'preview@example.com',
            'fullname' => 'Preview Full Name',
            'branchName' => 'Catford',
            'contact_number' => '0208 314 1498',
            'staff_name' => 'Preview Staff',
            'staff_user_id' => 1,
            'passcode' => 'preview-club-pass',
            'contract_id' => 'CNT-1',
            'booking_id' => 1001,
            'booking_reason' => 'Preview reason',
            'cancellation_reason' => 'Preview cancellation',
            'appointment_date' => Carbon::now()->format('Y-m-d'),
            'date_of_appointment' => Carbon::now()->format('Y-m-d'),
            'start' => '09:00',
            'end' => '10:00',
            'service_type' => 'MOT',
            'registration_number' => 'AB12 CDE',
            'regno' => 'AB12 CDE',
            'vrm' => 'AB12 CDE',
            'vehicle_registration' => 'AB12 CDE',
            'vehicle_chassis' => 'CHASSIS123',
            'vehicle_color' => 'Black',
            'mot_due_date' => Carbon::now()->addMonth(),
            'tax_due_date' => Carbon::now()->addMonths(2),
            'motorbike_reg' => 'AB12 CDE',
            'motorbike_make' => 'Honda',
            'motorbike_model' => 'CBR',
            'motorbike_year' => '2020',
            'motorbike_id' => 501,
            'motDetails' => [
                'customer_name' => 'Preview Rider',
                'mot_due_date' => Carbon::now()->addMonth(),
            ],
            'motorcycle' => (object) ['registration_number' => 'AB12 CDE'],
            'surveyLink' => 'https://example.com/survey',
            'subscription_url' => 'https://example.com/club',
            'subscription_id' => 'sub_preview',
            'partnerData' => [
                'business_name' => 'Preview Partner',
                'companyname' => 'Preview Partner Ltd',
                'company_address' => '1 Partner Street, London',
                'company_number' => '12345678',
                'first_name' => 'Pat',
                'last_name' => 'Partner',
                'contact_name' => 'Partner Contact',
                'email' => 'partner@example.com',
                'phone' => '020 0000 0001',
                'mobile' => '07900 000000',
                'website' => 'https://example.com',
                'fleet_size' => '10',
                'operating_since' => '2020',
            ],
            'clubMember' => (object) [
                'name' => 'Club Member',
                'full_name' => 'Preview Club Member',
                'phone' => '020 0000 0002',
            ],
            'member' => (object) ['email' => 'member@example.com'],
            'subscriber' => (object) [
                'email' => 'subscriber@example.com',
                'full_name' => 'Preview Subscriber',
                'first_name' => 'Preview',
                'last_name' => 'Subscriber',
                'reg_no' => 'AB12 CDE',
            ],
            'stats' => [
                'count' => 3,
                'active_rentals' => 2,
                'weekly_revenue' => 450.5,
                'due_payments' => 1,
                'unpaid_invoices' => 120.0,
            ],
            'summary' => 'Preview summary line.',
            'month' => 'April',
            'day' => 'Monday',
            'weekStart' => '2026-03-31',
            'weekEnd' => '2026-04-06',
            'fmt' => 'Y-m-d',
            'mailData' => [
                'title' => 'Preview document',
                'url' => 'https://example.com/sign-preview',
                'customer_name' => 'Preview Customer',
                'totalProcessed' => 5,
                'total' => 10,
                'successCount' => 4,
                'failureCount' => 1,
            ],
            'booking' => (object) [
                'service_type' => 'Service',
                'fullname' => 'Preview User',
                'phone' => '020 0000 0000',
                'email' => 'preview@example.com',
                'reg_no' => 'AB12 CDE',
                'booking_date' => '2026-04-10',
                'booking_time' => '10:00',
                'description' => 'Preview notes',
            ],
            'items' => collect([
                (object) [
                    'product_name' => 'Preview product',
                    'quantity' => 1,
                    'unit_price' => 19.99,
                    'line_total' => 19.99,
                ],
            ]),
            'payment' => (object) [
                'order_id' => 'EC-9001',
                'transaction_id' => 'txn_preview',
                'status' => 'completed',
                'amount' => '99.99',
                'payer_email' => null,
            ],
            'resource' => ['id' => 'preview'],
            'additionalData' => [],
            'webhookEvent' => (object) [
                'transmission_id' => 'tr_preview',
                'transmission_time' => Carbon::now()->toIso8601String(),
                'auth_algo' => 'SHA256withRSA',
            ],
            'refund_amount' => 49.99,
            'old_billing_frequency' => 'weekly',
            'new_billing_frequency' => 'weekly',
            'old_billing_day' => 1,
            'new_billing_day' => 1,
            'total_products' => 120,
            'total_stock' => 4500.5,
            'subscription_amount' => 89.99,
            'original_amount' => 120.0,
            'address' => '9-13 Unit 1179 Catford Hill, London, SE6 4NU',
            'request' => (object) [
                'name' => 'Preview Claimant',
                'email' => 'claim@example.com',
                'phone' => '020 0000 0000',
                'reg_no' => 'AB12 CDE',
                'vehicle_type' => 'Motorcycle',
                'referal' => 'Web',
            ],
            'payment_method' => 'card',
            'payment_notes' => 'Preview payment notes',
            'payment_reference' => 'REF-PREVIEW',
            'invoice_id' => 'INV-1000',
            'invoice_date' => Carbon::now()->format('Y-m-d'),
            'amount' => '25.00',
            'charges_id' => 'CHG-1',
            'charges_description' => 'Preview charge',
            'fromAddress' => 'from@example.com',
            'toAddress' => 'to@example.com',
            'success' => true,
            'decline' => false,
            'notes' => 'Preview notes',
            'distance' => '10 miles',
            'isPickup' => false,
            'is_paid' => true,
            'isNewMake' => false,
            'is_resolved' => false,
            'makeDisplay' => 'Preview Make',
            'userDetails' => [
                'name' => 'Preview Rider',
                'bike_reg' => 'AB12 CDE',
                'phone' => '020 0000 0000',
                'message' => 'Preview recovery message',
            ],
            'acquirer_transaction_id' => 'acq_1',
            'bank_response_category' => 'approved',
            'card_category' => 'credit',
            'card_country' => 'GB',
            'card_funding' => 'credit',
            'card_last_four' => '4242',
            'change_date_time' => Carbon::now()->toIso8601String(),
            'cit_session_id' => 'cit_sess',
            'consumer_reference' => 'cons_ref',
            'errorMessage' => 'Preview error',
            'eventType' => 'preview.event',
            'external_bank_response_code' => '0',
            'issuing_bank' => 'Preview Bank',
            'judo_id' => 'judo_1',
            'merchant_name' => 'NGN Motors',
            'negative_stock' => 0,
            'positive_stock' => 5,
            'original_payment_date' => Carbon::now()->subDay()->format('Y-m-d'),
            'original_receipt_id' => 'RCP-1',
            'refund_receipt_id' => 'RR-1',
            'refunded_at' => Carbon::now()->format('Y-m-d H:i'),
            'refunded_by_email' => 'refund@example.com',
            'refunded_by_name' => 'Refunder',
            'refunded_by_user_id' => 1,
            'risk_score' => 'low',
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'transaction_id' => 'txn_preview',
            'payment_network_transaction_id' => 'pnt_1',
            'anomalyType' => 'test',
            'zero_stock' => collect([]),
            'emailDataList' => collect([
                [
                    'booking_no' => 'BK-1',
                    'customer_name' => 'Preview',
                    'vin_number' => 'VIN123',
                    'registration_number' => 'AB12 CDE',
                    'weekly_rent' => '120.00',
                    'invoice_date' => Carbon::now()->format('Y-m-d'),
                ],
            ]),
            'emailData' => [
                'customer_name' => 'Preview Customer',
                'weekly_rent' => '120.00',
                'vin_number' => 'VIN123456',
                'registration_number' => 'AB12 CDE',
                'invoice_date' => Carbon::now()->format('Y-m-d'),
            ],
            'active_bookings' => collect([]),
            'allBranchIds' => [1, 2, 3],
            'used_motorbike' => collect([]),
            'rows' => collect([]),
            'rowCount' => 0,
            'row' => (object) ['col' => 'x'],
            'value' => 'preview',
            'item' => (object) ['label' => 'Item'],
            'detailedDeclines' => collect([]),
            'expectedItems' => collect([]),
            'successItems' => collect([]),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function shapeForRelativeView(string $relativeDot): array
    {
        return match (true) {
            in_array($relativeDot, ['pcn.t1', 'pcn.t2'], true) => [
                'data' => (object) [
                    'full_name' => 'Preview Rider',
                    'reg_no' => 'AB12 CDE',
                    'pcn_number' => 'PCN-99999',
                    'date_of_letter_issued' => Carbon::now()->subDays(3)->format('Y-m-d'),
                ],
            ],
            in_array($relativeDot, ['pcn.t3', 'pcn.t4', 'pcn.t5', 'pcn.t6'], true) => [
                'data' => collect([
                    (object) [
                        'pcn_number' => 'PCN-100',
                        'reg_no' => 'AB12 CDE',
                        'full_name' => 'Preview Owner',
                        'pcn_customer_email' => 'owner@example.com',
                    ],
                ]),
            ],
            in_array($relativeDot, ['pcn.failed-t1', 'pcn.failed-t2'], true) => [
                'data' => [
                    'Notice 1' => [
                        'PCN-77204 · Reg AB12 CDE',
                        'Recipient: owner.invalid@example.com',
                        'Reason: mailbox does not exist',
                        'Last attempt: '.Carbon::now()->subHours(2)->format('Y-m-d H:i'),
                    ],
                    'Notice 2' => [
                        'PCN-77205 · Reg XY99 ZZZ',
                        'Recipient: bounced@example.com',
                        'Reason: message rejected by provider',
                        'Last attempt: '.Carbon::now()->subHour()->format('Y-m-d H:i'),
                    ],
                ],
            ],
            in_array($relativeDot, ['payment_reminder'], true) => [
                'data' => [
                    'customer_name' => 'Preview Customer',
                    'vehicle_registration' => 'AB12 CDE',
                    'vehicle_chassis' => 'CH1',
                    'vehicle_color' => 'Red',
                    'date_of_appointment' => Carbon::now()->addDay()->format('Y-m-d'),
                    'start' => '09:00',
                    'end' => '10:00',
                    'payment_link' => 'https://example.com/pay',
                    'payment_status' => 'N/A',
                    'address' => '1 Preview Street, London',
                ],
            ],
            in_array($relativeDot, ['pcn-notify', 'pcn-notify-police'], true) => [
                'data' => [
                    'reg_no' => 'AB12 CDE',
                    'pcn_number' => 'PCN-500',
                    'council_link' => '',
                ],
            ],
            in_array($relativeDot, ['invoice-generation'], true) => [
                'data' => [
                    'totalProcessed' => 10,
                    'newInvoices' => 8,
                ],
            ],
            in_array($relativeDot, ['monthly_sales_report'], true) => [
                'data' => [
                    [
                        'date' => '2026-04-01',
                        'reg_no' => 'AB12 CDE',
                        'motorbike_id' => 99,
                        'status' => 'Sold',
                        'user' => 'preview.user',
                    ],
                ],
            ],
            in_array($relativeDot, ['cron-jobs.cron-job-daily-ngn-club-report', 'cron-jobs.cron-job-weekly-ngn-club-report'], true) => [
                'data' => [
                    'active_bookings' => [
                        [
                            'pos_invoice' => 'INV-9001',
                            'branch_id' => 'C1',
                            'total' => 120.5,
                            'percent' => 10,
                            'discount' => 12.05,
                        ],
                    ],
                ],
            ],
            str_starts_with($relativeDot, 'ecommerce.') => self::ecommerceStubs(),
            in_array($relativeDot, ['weekly_busiest_days_report'], true) => [
                'emailData' => [
                    'topVisits' => [
                        (object) [
                            'week_number' => 1,
                            'visit_date' => '2026-04-01',
                            'day_name' => 'Tuesday',
                            'total_visits' => 12,
                            'rank_in_week' => 1,
                        ],
                    ],
                    'allVisits' => [
                        (object) [
                            'week_number' => 1,
                            'visit_date' => '2026-04-01',
                            'day_name' => 'Tuesday',
                            'total_visits' => 12,
                            'rank_in_week' => 1,
                        ],
                    ],
                ],
            ],
            in_array($relativeDot, ['annual_busiest_days_report'], true) => [
                'emailData' => [
                    'yearStart' => '2025-01-01',
                    'yearEnd' => '2025-12-31',
                    'allDaysReport' => collect([]),
                    'allMonthsReport' => collect([]),
                    'dayReportByBranch' => [],
                    'monthReportByBranch' => [],
                ],
            ],
            in_array($relativeDot, ['quarterly_vehicle_visits_report'], true) => [
                'emailData' => [
                    'periodStartFormatted' => '1 Jan 2026',
                    'periodEndFormatted' => '31 Mar 2026',
                    'mostVisited' => [],
                    'leastVisited' => [],
                    'mostRepeatedModelYear' => [],
                    'leastRepeatedModelYear' => [],
                ],
            ],
            in_array($relativeDot, ['mit-weekly-closing-report', 'mit-weekly-opening-report'], true) => [
                'summary' => [
                    'expected' => 1000,
                    'received' => 800,
                    'decline' => 200,
                    'expectedItems' => collect([]),
                    'receivedItems' => collect([]),
                ],
                'detailedDeclines' => [],
                'successItems' => [],
            ],
            in_array($relativeDot, [
                'motorbike_transport_delivery_order_enquiry',
                'motorbike_transport_delivery_order_confirmed',
                'motorbike_transport_delivery_order_cancelled',
                'motorbike_delivery_order_enquiry_internal',
            ], true) => [
                'order' => self::mockMotorbikeTransportOrder(),
            ],
            in_array($relativeDot, ['vehicle_delivery_order_confirmation'], true) => [
                'order' => self::mockVehicleDeliveryOrder(),
            ],
            in_array($relativeDot, ['rental-agreement', 'rental-agreement-sign', 'purchase-invoice-sign', 'upload-documents'], true) => [
                'mailData' => self::previewActionMailData(match ($relativeDot) {
                    'purchase-invoice-sign' => 'Complete your purchase invoice',
                    'upload-documents' => 'Upload your supporting documents',
                    default => 'Sign your rental agreement',
                }),
            ],
            in_array($relativeDot, ['TAX.15days', 'TAX.30days'], true) => [
                'subscriber' => (object) [
                    'email' => 'subscriber@example.com',
                    'full_name' => 'Alex Morgan',
                    'first_name' => 'Alex',
                    'last_name' => 'Morgan',
                    'reg_no' => 'AB12 CDE',
                    'tax_due_date' => Carbon::now()->addDays($relativeDot === 'TAX.15days' ? 15 : 30)->format('j F Y'),
                ],
            ],
            in_array($relativeDot, self::migratedViewsUsingTitleBody(), true) => self::documentPreviewFields($relativeDot),
            default => [],
        };
    }

    private static function mockMotorbikeTransportOrder(): object
    {
        return (object) [
            'id' => 9001,
            'full_name' => 'Preview Rider',
            'order_id' => 'ORD-9001',
            'email' => 'rider@example.com',
            'phone' => '020 0000 0000',
            'customer_address' => '1 Preview Street',
            'customer_postcode' => 'SE6 4NU',
            'vrm' => 'AB12 CDE',
            'vehicle_type' => 'Motorcycle',
            'moveable' => true,
            'documents' => true,
            'keys' => true,
            'note' => 'Preview note',
            'pick_up_datetime' => Carbon::now()->addDay()->toDateTimeString(),
            'pickup_address' => 'Pickup address',
            'dropoff_address' => 'Dropoff address',
            'distance' => '15',
            'total_cost' => 199.5,
        ];
    }

    private static function mockVehicleDeliveryOrder(): object
    {
        return (object) [
            'full_name' => 'Preview Customer',
            'vehicle_type' => 'Motorcycle',
            'vrm' => 'AB12 CDE',
            'from_address' => '1 From Road, London',
            'to_address' => '2 To Road, London',
            'pickup_date' => Carbon::now()->addDay()->format('Y-m-d H:i:s'),
            'total_distance' => 28,
            'phone_number' => '020 8314 1498',
            'delivery_charge' => 85.0,
            'surcharge' => 0,
            'bike_require_lift_fee' => 0,
            'additional_fee' => 0,
            'express_fee' => 0,
            'notes' => '',
        ];
    }

    /**
     * Migrated blades that read $title / $body (not only mailData). Do not put defaults in coreStubs() — they leak into every preview fragment.
     *
     * @return list<string>
     */
    private static function migratedViewsUsingTitleBody(): array
    {
        return [
            'sale-agreement',
            'purchase-invoice',
            'finance-contract-sign',
            'rental-terminate-v1',
            'quote-request',
            'rental-payment-receipt',
            'othercharges-receipt',
            'loyalty-scheme-policy',
            'logbook-transfer',
        ];
    }

    /**
     * @return array{title: string, url: string, customer_name: string, totalProcessed: int, total: int, successCount: int, failureCount: int}
     */
    private static function previewActionMailData(string $title): array
    {
        return [
            'title' => $title,
            'url' => 'https://example.com/customer/preview-action',
            'customer_name' => 'Taylor Morgan',
            'totalProcessed' => 5,
            'total' => 10,
            'successCount' => 4,
            'failureCount' => 1,
        ];
    }

    /**
     * Realistic copy for Mailpit previews (local only). Production mailables pass real title/body.
     *
     * @return array{title: string, body: string}
     */
    private static function documentPreviewFields(string $relativeDot): array
    {
        return match ($relativeDot) {
            'sale-agreement' => [
                'title' => 'Hire / sale agreement',
                'body' => 'Thank you for choosing NGN Motors. Your hire or sale agreement is attached to this email. If anything does not match what you agreed in branch, please contact us before signing.',
            ],
            'purchase-invoice' => [
                'title' => 'Purchase invoice',
                'body' => 'Please find your purchase invoice attached. The amount and vehicle details should match your order. Contact us if you need a correction or payment plan.',
            ],
            'finance-contract-sign' => [
                'title' => 'Finance contract',
                'body' => 'Your finance contract is ready to review and sign. Open the link we sent separately or refer to the attached PDF. Our sales team can explain any clause you are unsure about.',
            ],
            'rental-terminate-v1' => [
                'title' => 'Rental termination',
                'body' => 'Please find attached your termination letter. It confirms the end date and any amounts still due. If you have questions, reply to this email or call customer services.',
            ],
            'quote-request' => [
                'title' => 'Dear Spare Parts Team,',
                'body' => 'Find attached our quote request. Please reply to the email below.',
            ],
            'rental-payment-receipt' => [
                'title' => 'Rental payment receipt',
                'body' => 'Thank you. We have recorded your rental payment against your agreement. Keep this email for your records.',
            ],
            'othercharges-receipt' => [
                'title' => 'Other charges receipt',
                'body' => 'Please find attached your receipt for additional charges (e.g. repairs, accessories, or fees). Contact us if the description or amount does not look right.',
            ],
            'loyalty-scheme-policy' => [
                'title' => 'Loyalty upgrade scheme',
                'body' => 'Thank you for enrolling in our loyalty upgrade scheme. A short summary of benefits and terms is set out below; your signed policy document is attached for your records.',
            ],
            'logbook-transfer' => [
                'title' => 'Logbook (V5) transfer',
                'body' => 'We are processing the logbook transfer for your vehicle with DVLA. You will receive a further update when the registration document is issued or if we need more information.',
            ],
            default => [
                'title' => 'Document',
                'body' => 'Please see the attached document.',
            ],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private static function ecommerceStubs(): array
    {
        try {
            $order = EcOrder::query()
                ->with(['shippingMethod', 'branch', 'customerAddress', 'customer'])
                ->first();
            if ($order !== null) {
                return [
                    'order' => $order,
                    'customer' => $order->customer,
                    'items' => $order->items()->get(),
                    'shipping' => $order->shipping,
                    'address' => $order->customerAddress,
                    'shippingMethod' => $order->shippingMethod,
                    'branch' => $order->branch,
                    'status' => $order->order_status ?? 'confirmed',
                ];
            }
        } catch (\Throwable) {
            // fall through to mock
        }

        $branch = (object) [
            'name' => 'CATFORD',
            'address' => '9-13 Unit 1179 Catford Hill',
            'city' => 'London',
            'postal_code' => 'SE6 4NU',
        ];

        $shippingMethod = (object) [
            'name' => 'Collect in store',
            'in_store_pickup' => true,
        ];

        $order = (object) [
            'id' => 999001,
            'order_date' => Carbon::now(),
            'payment_status' => 'paid',
            'total_amount' => 100.00,
            'discount' => 0,
            'tax' => 20.00,
            'grand_total' => 120.00,
            'shipping_cost' => 0,
            'shippingMethod' => $shippingMethod,
            'branch' => $branch,
        ];

        return [
            'order' => $order,
            'customer' => (object) ['first_name' => 'Preview', 'last_name' => 'Buyer', 'email' => 'buyer@example.com'],
            'items' => collect([
                (object) [
                    'product_name' => 'Helmet (preview)',
                    'quantity' => 1,
                    'unit_price' => 100.00,
                    'line_total' => 100.00,
                ],
            ]),
            'shipping' => null,
            'address' => (object) [
                'street_address' => '1 Preview Road',
                'street_address_plus' => '',
                'city' => 'London',
                'postcode' => 'SW1A 1AA',
            ],
            'shippingMethod' => $shippingMethod,
            'branch' => $branch,
            'status' => 'confirmed',
        ];
    }
}
