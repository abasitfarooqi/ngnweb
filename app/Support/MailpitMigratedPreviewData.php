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
        $visitRow = (object) [
            'week_number' => 1,
            'visit_date' => '2026-04-01',
            'day_name' => 'Tuesday',
            'total_visits' => 12,
            'rank_in_week' => 1,
        ];

        return [
            'user' => (object) [
                'first_name' => 'preview',
                'last_name' => 'user',
                'full_name' => 'Preview User',
            ],
            'customer_name' => 'Preview Customer',
            'customer' => (object) ['first_name' => 'Preview', 'last_name' => 'Customer', 'email' => 'preview@example.com'],
            'customer_email' => 'preview@example.com',
            'customer_phone' => '020 0000 0000',
            'customer_id' => 1,
            'title' => 'Preview title',
            'body' => "Line one of preview body.\nLine two.",
            'name' => 'Preview Name',
            'email' => 'preview@example.com',
            'fullname' => 'Preview Full Name',
            'branchName' => 'Catford',
            'contact_number' => '0208 314 1498',
            'staff_name' => 'Preview Staff',
            'staff_user_id' => 1,
            'passcode' => 'preview-pass',
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
            'motDetails' => (object) ['registration' => 'AB12 CDE'],
            'motorcycle' => (object) ['registration_number' => 'AB12 CDE'],
            'surveyLink' => 'https://example.com/survey',
            'subscription_url' => 'https://example.com/club',
            'subscription_id' => 'sub_preview',
            'partnerData' => [
                'business_name' => 'Preview Partner',
                'contact_name' => 'Partner Contact',
                'email' => 'partner@example.com',
            ],
            'clubMember' => (object) ['name' => 'Club Member'],
            'member' => (object) ['email' => 'member@example.com'],
            'subscriber' => (object) ['email' => 'subscriber@example.com'],
            'emailData' => [
                'topVisits' => [$visitRow],
                'allVisits' => [$visitRow],
            ],
            'stats' => [
                'count' => 3,
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
            'payment' => (object) ['amount' => 99.99],
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
            'request' => (object) ['id' => 1],
            'success' => true,
            'decline' => false,
            'notes' => 'Preview notes',
            'distance' => '10 miles',
            'isPickup' => false,
            'is_paid' => true,
            'isNewMake' => false,
            'is_resolved' => false,
            'makeDisplay' => 'Preview Make',
            'userDetails' => (object) ['name' => 'Staff User'],
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
            'new_billing_day' => 15,
            'old_billing_day' => 1,
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
            'webhookEvent' => 'preview',
            'anomalyType' => 'test',
            'zero_stock' => collect([]),
            'emailDataList' => [
                [
                    'booking_no' => 'BK-1',
                    'customer_name' => 'Preview',
                    'vin_number' => 'VIN123',
                    'registration_number' => 'AB12 CDE',
                    'weekly_rent' => '120.00',
                    'invoice_date' => Carbon::now()->format('Y-m-d'),
                ],
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
                    'Example row' => ['Line A', 'Line B'],
                    'Single value' => 'Plain text',
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
            default => [],
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
