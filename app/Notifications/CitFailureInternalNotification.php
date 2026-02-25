<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayPaymentSessionOutcome;

class CitFailureInternalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $citSession;
    protected $outcome;
    protected $failureReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(JudopayCitPaymentSession $citSession, ?JudopayPaymentSessionOutcome $outcome = null, string $failureReason = 'Unknown')
    {
        $this->citSession = $citSession;
        $this->outcome = $outcome;
        $this->failureReason = $failureReason;
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
        $customer = null;
        
        // Get customer through morph relationship
        if ($subscription->subscribable_type === 'App\Models\RentingBooking') {
            $customer = $subscription->subscribable?->customer;
        } elseif ($subscription->subscribable_type === 'App\Models\FinanceApplication') {
            $customer = $subscription->subscribable?->customer;
        }
        
        $serviceData = $subscription?->subscribable;

        // Get contract details with null safety
        $contractId = $serviceData?->id ?? 'N/A';
        $vrm = 'N/A';
        
        if ($subscription?->subscribable_type === 'App\Models\RentingBooking' && $serviceData) {
            $vrm = $serviceData->rentingBookingItems?->first()?->motorbike?->reg_no ?? 'N/A';
        } elseif ($subscription?->subscribable_type === 'App\Models\FinanceApplication' && $serviceData) {
            $vrm = $serviceData->application_items?->first()?->motorbike?->reg_no ?? 'N/A';
        }

        // Safe customer name handling
        $customerName = 'Unknown Customer';
        if ($customer) {
            $customerName = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));
            if (empty($customerName)) {
                $customerName = 'Unknown Customer';
            }
        }

        // Extract detailed failure information from outcome
        $receiptId = $this->outcome?->judopay_receipt_id ?? 'N/A';
        $cardLastFour = $this->outcome?->card_last_four ?? 'N/A';
        $cardCountry = $this->outcome?->card_country ?? 'N/A';
        $issuingBank = $this->outcome?->issuing_bank ?? 'N/A';
        $acquirerTransactionId = $this->outcome?->acquirer_transaction_id ?? 'N/A';
        $paymentReference = $this->outcome?->your_payment_reference ?? $this->citSession->payment_reference ?? 'N/A';
        $consumerReference = $this->outcome?->your_consumer_reference ?? $this->citSession->consumer_reference ?? 'N/A';
        
        // Extract additional data from payload JSON
        $payload = $this->outcome?->payload ?? [];
        $cardScheme = data_get($payload, 'cardDetails.cardScheme') ?? 'N/A';
        $externalBankResponseCode = data_get($payload, 'externalBankResponseCode') ?? 'N/A';
        $riskScore = data_get($payload, 'risks.riskScore') ?? 'N/A';
        $cardFunding = data_get($payload, 'cardDetails.cardFunding') ?? 'N/A';
        $cardCategory = data_get($payload, 'cardDetails.cardCategory') ?? 'N/A';
        $cardHolderName = data_get($payload, 'cardDetails.cardHolderName') ?? 'N/A';

        $mailMessage = (new MailMessage)
            ->subject('❌ CIT Payment Authorisation FAILED - ' . $customerName)
            ->greeting('CIT Payment Authorisation Failed!')
            ->line('A customer\'s recurring payment authorisation has failed.')
            ->line('**Failure Details:**')
            ->line('• **Reason:** ' . $this->failureReason)
            ->line('• **Amount:** £' . $this->citSession->amount)
            ->line('• **Contract ID:** ' . $contractId)
            ->line('• **VRM:** ' . $vrm)
            ->line('• **Subscription ID:** ' . ($subscription?->id ?? 'N/A'))
            ->line('**Customer:** ' . $customerName . ' | ' . ($customer?->email ?? 'N/A'))
            ->line('**Failed At:** ' . $this->citSession->updated_at->format('d/m/Y H:i:s'))
            ->line('')
            ->line('**Payment Evidence (for customer disputes):**')
            ->line('• **Receipt ID:** ' . $receiptId)
            ->line('• **Payment Reference:** ' . $paymentReference)
            ->line('• **Consumer Reference:** ' . $consumerReference)
            ->line('• **Card Last 4 Digits:** ' . $cardLastFour)
            ->line('• **Card Scheme:** ' . $cardScheme)
            ->line('• **Card Type:** ' . $cardFunding . ' (' . $cardCategory . ')')
            ->line('• **Card Country:** ' . $cardCountry)
            ->line('• **Cardholder Name:** ' . $cardHolderName)
            ->line('• **Issuing Bank:** ' . $issuingBank)
            ->line('• **Acquirer Transaction ID:** ' . $acquirerTransactionId)
            ->line('• **Bank Response Code:** ' . $externalBankResponseCode)
            ->line('• **Risk Score:** ' . $riskScore)
            ->action('View Subscription', route('page.judopay.subscribe', $subscription?->id ?? 0))
            ->salutation('NGN Payment System');

        return $mailMessage;
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
            'customer_id' => $this->citSession->subscription->customer_id,
            'amount' => $this->citSession->amount,
            'status' => 'failed',
            'failure_reason' => $this->failureReason,
            'notified_at' => now(),
        ];
    }
}