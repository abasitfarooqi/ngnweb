<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayPaymentSessionOutcome;

class CitSuccessCustomerServiceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $citSession;
    protected $outcome;

    /**
     * Create a new notification instance.
     */
    public function __construct(JudopayCitPaymentSession $citSession, JudopayPaymentSessionOutcome $outcome)
    {
        $this->citSession = $citSession;
        $this->outcome = $outcome;
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

        return (new MailMessage)
            ->subject('✅ Payment Authorisation Complete - Customer Service Notification')
            ->greeting('New Payment Authorisation Completed')
            ->line('A customer has successfully completed their recurring payment authorisation.')
            ->line('**Customer Information**')
            ->line('• **Name:** ' . $customerName)
            ->line('• **Email:** ' . $customerEmail)
            ->line('• **Phone:** ' . $customerPhone)
            ->line('• **Customer ID:** ' . ($customer->id ?? 'N/A'))
            ->line('**Authorisation Details**')
            ->line('• **Contract ID:** ' . $contractId)
            ->line('• **VRM:** ' . $vrm)
            ->line('• **Subscription ID:** ' . $subscription->id)
            ->line('• **Date:** ' . $this->outcome->occurred_at->format('d F Y H:i'))
            ->line('• **Amount:** £' . $this->outcome->amount)
            ->line('• **Payment Reference:** ' . $this->outcome->judopay_receipt_id)
            ->line('**Service Type:** ' . ($subscription->subscribable_type === 'App\Models\RentingBooking' ? 'Rental' : 'Finance'))
            ->line('The customer\'s recurring payment authorisation is now active and payments will be processed automatically according to their agreement terms.')
            ->action('View Subscription Details', route('page.judopay.subscribe', $subscription->id))
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
            'customer_id' => $this->citSession->subscription->customer_id ?? null,
            'amount' => $this->outcome->amount,
            'receipt_id' => $this->outcome->judopay_receipt_id,
            'status' => 'success',
            'notified_at' => now(),
        ];
    }
}

