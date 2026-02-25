<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayPaymentSessionOutcome;

class CitSuccessInternalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $citSession;
    protected $outcome;
    protected $webhookData;

    /**
     * Create a new notification instance.
     */
    public function __construct(JudopayCitPaymentSession $citSession, JudopayPaymentSessionOutcome $outcome, array $webhookData = [])
    {
        $this->citSession = $citSession;
        $this->outcome = $outcome;
        $this->webhookData = $webhookData;
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

        // Extract detailed success information from outcome
        $receiptId = $this->outcome?->judopay_receipt_id ?? 'N/A';
        $cardLastFour = $this->outcome?->card_last_four ?? 'N/A';
        $cardCountry = $this->outcome?->card_country ?? 'N/A';
        $issuingBank = $this->outcome?->issuing_bank ?? 'N/A';
        $acquirerTransactionId = $this->outcome?->acquirer_transaction_id ?? 'N/A';
        $authCode = $this->outcome?->auth_code ?? 'N/A';
        $paymentReference = $this->outcome?->your_payment_reference ?? $this->citSession->payment_reference ?? 'N/A';
        $consumerReference = $this->outcome?->your_consumer_reference ?? $this->citSession->consumer_reference ?? 'N/A';
        
        // Extract additional data from payload JSON
        $payload = $this->outcome?->payload ?? [];
        $cardScheme = data_get($payload, 'cardDetails.cardScheme') ?? 'N/A';
        $cardFunding = data_get($payload, 'cardDetails.cardFunding') ?? 'N/A';
        $cardCategory = data_get($payload, 'cardDetails.cardCategory') ?? 'N/A';
        $cardHolderName = data_get($payload, 'cardDetails.cardHolderName') ?? 'N/A';
        $cardToken = data_get($payload, 'cardDetails.cardToken') ?? 'N/A';

        return (new MailMessage)
            ->subject('✅ CIT Payment Authorisation SUCCESS - ' . $customerName)
            ->greeting('CIT Payment Authorisation Completed Successfully!')
            ->line('A customer has successfully completed their recurring payment authorisation.')
            ->line('**Payment Details:**')
            ->line('• **Amount:** £' . $this->outcome->amount)
            ->line('• **Contract ID:** ' . $contractId)
            ->line('• **VRM:** ' . $vrm)
            ->line('• **Subscription ID:** ' . ($subscription?->id ?? 'N/A'))
            ->line('**Customer:** ' . $customerName . ' | ' . ($customer?->email ?? 'N/A'))
            ->line('**Completed:** ' . $this->outcome->occurred_at->format('d/m/Y H:i:s'))
            ->line('')
            ->line('**Payment Evidence (for audit & verification):**')
            ->line('• **Receipt ID:** ' . $receiptId)
            ->line('• **Payment Reference:** ' . $paymentReference)
            ->line('• **Consumer Reference:** ' . $consumerReference)
            ->line('• **Auth Code:** ' . $authCode)
            ->line('• **Card Token:** ' . ($cardToken !== 'N/A' ? substr($cardToken, 0, 16) . '...' : 'N/A'))
            ->line('• **Card Last 4 Digits:** ' . $cardLastFour)
            ->line('• **Card Scheme:** ' . $cardScheme)
            ->line('• **Card Type:** ' . $cardFunding . ' (' . $cardCategory . ')')
            ->line('• **Card Country:** ' . $cardCountry)
            ->line('• **Cardholder Name:** ' . $cardHolderName)
            ->line('• **Issuing Bank:** ' . $issuingBank)
            ->line('• **Acquirer Transaction ID:** ' . $acquirerTransactionId)
            ->action('View Subscription', route('page.judopay.subscribe', $subscription?->id ?? 0))
            ->salutation('NGN Payment System');
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
            'customer_id' => $this->citSession->subscription?->customer_id,
            'amount' => $this->outcome->amount,
            'receipt_id' => $this->outcome->judopay_receipt_id,
            'status' => 'success',
            'notified_at' => now(),
        ];
    }
}