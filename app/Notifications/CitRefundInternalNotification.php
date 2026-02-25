<?php

namespace App\Notifications;

use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayPaymentSessionOutcome;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CitRefundInternalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $citSession;
    protected $refundOutcome;
    protected $originalOutcome;
    protected $refundedByUser;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        JudopayCitPaymentSession $citSession,
        JudopayPaymentSessionOutcome $refundOutcome,
        JudopayPaymentSessionOutcome $originalOutcome,
        ?User $refundedByUser = null
    ) {
        $this->citSession = $citSession;
        $this->refundOutcome = $refundOutcome;
        $this->originalOutcome = $originalOutcome;
        $this->refundedByUser = $refundedByUser;
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

        // Customer details
        $customerName = $customer ? trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) : 'Unknown Customer';
        $customerEmail = $customer->email ?? 'N/A';
        $customerPhone = $customer->phone ?? 'N/A';

        // Refund details
        $refundAmount = $this->refundOutcome->amount ?? $this->originalOutcome->amount;
        $originalAmount = $this->originalOutcome->amount;
        $refundReceiptId = $this->refundOutcome->judopay_receipt_id ?? 'N/A';
        $originalReceiptId = $this->originalOutcome->judopay_receipt_id ?? 'N/A';
        
        // Get refunded by user info
        $refundedByName = 'System (Automatic)';
        $refundedByEmail = 'N/A';
        $refundedById = null;
        
        if ($this->refundedByUser) {
            $refundedByName = trim(($this->refundedByUser->first_name ?? '') . ' ' . ($this->refundedByUser->last_name ?? ''));
            $refundedByEmail = $this->refundedByUser->email ?? 'N/A';
            $refundedById = $this->refundedByUser->id;
        } else {
            // Try to get from refund outcome payload
            $payload = $this->refundOutcome->payload ?? [];
            $refundedById = data_get($payload, 'refunded_by_user_id');
            if ($refundedById) {
                $refundedByUser = User::find($refundedById);
                if ($refundedByUser) {
                    $refundedByName = trim(($refundedByUser->first_name ?? '') . ' ' . ($refundedByUser->last_name ?? ''));
                    $refundedByEmail = $refundedByUser->email ?? 'N/A';
                }
            }
        }

        // CRITICAL DETAILS - Only for admin (Thiago), not shared with staff/customer
        $originalPayload = $this->originalOutcome->payload ?? [];
        $refundPayload = $this->refundOutcome->payload ?? [];
        
        // Extract sensitive payment details from original outcome
        $cardLastFour = $this->originalOutcome->card_last_four ?? data_get($originalPayload, 'cardDetails.cardLastfour', 'N/A');
        $cardFunding = $this->originalOutcome->card_funding ?? data_get($originalPayload, 'cardDetails.cardFunding', 'N/A');
        $cardCategory = $this->originalOutcome->card_category ?? data_get($originalPayload, 'cardDetails.cardCategory', 'N/A');
        $cardCountry = $this->originalOutcome->card_country ?? data_get($originalPayload, 'cardDetails.cardCountry', 'N/A');
        $issuingBank = $this->originalOutcome->issuing_bank ?? data_get($originalPayload, 'cardDetails.bank', 'N/A');
        $acquirerTransactionId = $this->originalOutcome->acquirer_transaction_id ?? data_get($originalPayload, 'acquirerTransactionId', 'N/A');
        $paymentNetworkTransactionId = $this->originalOutcome->payment_network_transaction_id ?? data_get($originalPayload, 'paymentNetworkTransactionId', 'N/A');
        $riskScore = $this->originalOutcome->risk_score ?? data_get($originalPayload, 'riskScore', 'N/A');
        $externalBankResponseCode = $this->originalOutcome->external_bank_response_code ?? data_get($originalPayload, 'externalBankResponseCode', 'N/A');
        $bankResponseCategory = $this->originalOutcome->bank_response_category ?? 'N/A';
        $netAmount = $this->originalOutcome->net_amount ?? data_get($originalPayload, 'netAmount', 'N/A');
        $amountCollected = $this->originalOutcome->amount_collected ?? data_get($originalPayload, 'amountCollected', 'N/A');
        $merchantName = $this->originalOutcome->merchant_name ?? data_get($originalPayload, 'merchantName', 'N/A');
        $judoId = $this->originalOutcome->judo_id ?? data_get($originalPayload, 'judoId', 'N/A');
        $paymentReference = $this->originalOutcome->your_payment_reference ?? data_get($originalPayload, 'yourPaymentReference', 'N/A');
        $consumerReference = $this->originalOutcome->your_consumer_reference ?? data_get($originalPayload, 'consumer.yourConsumerReference', 'N/A');

        // Get recipient email to determine if critical details should be shown
        $recipientEmail = $notifiable->email ?? (is_string($notifiable) ? $notifiable : '');
        $isAdmin = $recipientEmail === 'thiago@neguinhomotors.co.uk';
        
        // Determine subject based on recipient
        $subject = $isAdmin 
            ? '⚠️ CRITICAL: CIT Payment Refunded - ' . $customerName
            : 'Payment Refund Processed - Customer Service Notification';

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.judopay.cit-refund-internal', [
                'recipient_email' => $recipientEmail,
                'is_admin' => $isAdmin,
                'refund_amount' => $refundAmount,
                'original_amount' => $originalAmount,
                'refund_receipt_id' => $refundReceiptId,
                'original_receipt_id' => $originalReceiptId,
                'refunded_at' => $isAdmin 
                    ? $this->refundOutcome->occurred_at->format('d/m/Y H:i:s')
                    : $this->refundOutcome->occurred_at->format('d F Y H:i'),
                'refunded_by_name' => $refundedByName,
                'refunded_by_email' => $refundedByEmail,
                'refunded_by_user_id' => $refundedById,
                'customer_id' => $customer->id ?? 'N/A',
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'contract_id' => $contractId,
                'vrm' => $vrm,
                'subscription_id' => $subscription->id,
                'cit_session_id' => $this->citSession->id,
                'service_type' => $subscription->subscribable_type === 'App\Models\RentingBooking' ? 'Rental' : 'Finance',
                'original_payment_date' => $isAdmin
                    ? $this->originalOutcome->occurred_at->format('d/m/Y H:i:s')
                    : $this->originalOutcome->occurred_at->format('d F Y H:i'),
                'subscription_url' => route('page.judopay.subscribe', $subscription->id),
                // CRITICAL DETAILS - Only for admin
                'card_last_four' => $cardLastFour,
                'card_funding' => $cardFunding,
                'card_category' => $cardCategory,
                'card_country' => $cardCountry,
                'issuing_bank' => $issuingBank,
                'acquirer_transaction_id' => $acquirerTransactionId,
                'payment_network_transaction_id' => $paymentNetworkTransactionId,
                'risk_score' => $riskScore,
                'external_bank_response_code' => $externalBankResponseCode,
                'bank_response_category' => $bankResponseCategory,
                'net_amount' => $netAmount,
                'amount_collected' => $amountCollected,
                'merchant_name' => $merchantName,
                'judo_id' => $judoId,
                'payment_reference' => $paymentReference,
                'consumer_reference' => $consumerReference,
            ]);
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
            'original_outcome_id' => $this->originalOutcome->id,
            'refund_amount' => $this->refundOutcome->amount,
            'refund_receipt_id' => $this->refundOutcome->judopay_receipt_id,
            'refunded_by_user_id' => $this->refundedByUser?->id,
            'status' => 'refunded',
            'notified_at' => now(),
        ];
    }
}
