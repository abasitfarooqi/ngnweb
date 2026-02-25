<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayPaymentSessionOutcome;

class CitSuccessCustomerNotification extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject('✅ Payment Authorisation Complete - NGN Motors')
            ->greeting('Authorisation Complete!')
            ->line('Thank you, ' . $customer->first_name . '!')
            ->line('Your verification has been completed successfully and your recurring payment authorisation is now active.')
            ->line('**Authorisation Details**')
            ->line('• **Customer:** ' . $customer->first_name . ' ' . $customer->last_name)
            ->line('• **Contract ID:** ' . $contractId . ' — VRM: ' . $vrm)
            ->line('• **Subscription ID:** ' . $subscription->id)
            ->line('• **Date:** ' . $this->outcome->occurred_at->format('d F Y H:i'))
            ->line('• **Amount:** £' . $this->outcome->amount)
            ->line('• **Payment Reference:** ' . $this->outcome->judopay_receipt_id)
            ->line('**What happens next?**')
            ->line('Your recurring payment authorisation is now active. You will receive payment notifications as per your agreement terms.')
            ->line('If you have any questions, please contact our customer service team.')
            ->line('**Contact Information**')
            ->line('• **Phone:** 0203 409 5478 / 0208 314 1498')
            ->line('• **Email:** customerservice@neguinhomotors.co.uk')
            ->line('• **Website:** ngnmotors.co.uk')
            ->salutation('NGN Motors Team');
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
            'amount' => $this->outcome->amount,
            'receipt_id' => $this->outcome->judopay_receipt_id,
            'status' => 'success',
            'notified_at' => now(),
        ];
    }
}