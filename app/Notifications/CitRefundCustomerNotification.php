<?php

namespace App\Notifications;

use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayPaymentSessionOutcome;
use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CitRefundCustomerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $citSession;

    protected $refundOutcome;

    protected $originalOutcome;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        JudopayCitPaymentSession $citSession,
        JudopayPaymentSessionOutcome $refundOutcome,
        JudopayPaymentSessionOutcome $originalOutcome
    ) {
        $this->citSession = $citSession;
        $this->refundOutcome = $refundOutcome;
        $this->originalOutcome = $originalOutcome;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subscription = $this->citSession->subscription;

        // Standardised customer resolution
        $customer = $subscription->subscribable?->customer ?? $subscription->customer;
        $serviceData = $subscription->subscribable;

        // Get contract details
        $contractId = $serviceData->id ?? 'N/A';
        $vrm = 'N/A';

        if ($subscription->subscribable_type === 'App\Models\RentingBooking') {
            $vrm = $serviceData->rentingBookingItems->first()->motorbike->reg_no ?? 'N/A';
        } elseif ($subscription->subscribable_type === 'App\Models\FinanceApplication') {
            $vrm = $serviceData->application_items->first()->motorbike->reg_no ?? 'N/A';
        }

        $refundAmount = $this->refundOutcome->amount ?? $this->originalOutcome->amount;
        $refundReceiptId = $this->refundOutcome->judopay_receipt_id ?? 'N/A';

        return (new MailMessage)
            ->subject('Payment Refund Processed - NGN Motors')
            ->view(
                'emails.templates.agreement-controller-universal',
                UniversalMailPayload::wrap('livewire.agreements.migrated.emails.judopay.cit-refund-customer', [
                    'customer_name' => $customer->first_name ?? 'Customer',
                    'refund_amount' => $refundAmount,
                    'refund_receipt_id' => $refundReceiptId,
                    'refunded_at' => $this->refundOutcome->occurred_at->format('d F Y H:i'),
                    'contract_id' => $contractId,
                    'vrm' => $vrm,
                    'subscription_id' => $subscription->id,
                    'original_payment_date' => $this->originalOutcome->occurred_at->format('d F Y H:i'),
                ], 'Payment Refund Processed - NGN Motors')
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'cit_session_id' => $this->citSession->id,
            'subscription_id' => $this->citSession->subscription_id,
            'refund_outcome_id' => $this->refundOutcome->id,
            'refund_amount' => $this->refundOutcome->amount,
            'refund_receipt_id' => $this->refundOutcome->judopay_receipt_id,
            'status' => 'refunded',
            'notified_at' => now(),
        ];
    }
}
