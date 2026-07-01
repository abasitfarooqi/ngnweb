<?php

namespace App\Support;

use App\Mail\CustomerDocumentRequest;
use App\Models\Customer;
use App\Models\RentingBooking;
use App\Models\UploadDocumentAccess;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class DocumentUploadAccessGenerator
{
    public const LINK_VALID_DAYS = 7;

    /**
     * @return array{uploadLink: string, access: UploadDocumentAccess, reused: bool}
     */
    public function create(int $customerId, int $bookingId, bool $sendEmail = false, bool $forceNew = false): array
    {
        $rentingBooking = RentingBooking::with('customer')->findOrFail($bookingId);

        if ((int) $rentingBooking->customer_id !== $customerId) {
            throw new InvalidArgumentException('Customer does not match this booking.');
        }

        $customer = $rentingBooking->customer;
        if (! $customer) {
            throw new RuntimeException('Customer not found for this booking.');
        }

        if (! $forceNew) {
            $existing = UploadDocumentAccess::query()
                ->where('customer_id', $customerId)
                ->where('booking_id', $bookingId)
                ->where('expires_at', '>', now())
                ->latest('id')
                ->first();

            if ($existing) {
                $existing->update(['expires_at' => now()->addDays(self::LINK_VALID_DAYS)]);
                $uploadLink = $existing->getLink();

                if ($sendEmail) {
                    $this->sendCustomerEmail($customer, $uploadLink);
                }

                return [
                    'uploadLink' => $uploadLink,
                    'access'     => $existing->fresh(),
                    'reused'     => true,
                ];
            }
        }

        $passcode = Str::random(12);

        $access = UploadDocumentAccess::create([
            'customer_id' => $customerId,
            'booking_id'  => $bookingId,
            'passcode'    => $passcode,
            'expires_at'  => now()->addDays(self::LINK_VALID_DAYS),
        ]);

        $uploadLink = route('uploaddoc.showUploadDocPage.show', [
            'customer_id' => $customerId,
            'passcode'    => $passcode,
        ]);

        if ($sendEmail) {
            $this->sendCustomerEmail($customer, $uploadLink);
        }

        return [
            'uploadLink' => $uploadLink,
            'access'     => $access,
            'reused'     => false,
        ];
    }

    public function findActiveLink(int $customerId, int $bookingId): ?string
    {
        $access = UploadDocumentAccess::query()
            ->where('customer_id', $customerId)
            ->where('booking_id', $bookingId)
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        return $access?->getLink();
    }

    private function sendCustomerEmail(Customer $customer, string $uploadLink): void
    {
        $body = 'Dear valued customer,

            We kindly request your attention to finalize your booking with Neguinho Motors. To proceed, please click the following link to upload all pending documents: '.$uploadLink.'

            Completing this step is essential to move forward. Thank you for choosing Neguinho Motors for your motorcycle rental needs.';

        try {
            Mail::to([
                $customer->email,
                'customerservice@neguinhomotors.co.uk',
            ])->send(new CustomerDocumentRequest([
                'title' => 'Documents Upload',
                'body'  => $body,
                'url'   => $uploadLink,
            ]));
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.': Failed to send document upload email: '.$e->getMessage());

            throw new RuntimeException('Failed to send email. Check the customer email address and try again.');
        }
    }
}
