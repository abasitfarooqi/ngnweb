<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\JudopayCitPaymentSession;

class CitFailureCustomerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $citSession;
    protected $failureReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(JudopayCitPaymentSession $citSession, string $failureReason = 'Payment was declined')
    {
        $this->citSession = $citSession;
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
            ->subject('❌ Payment Authorisation Failed - NGN Motors')
            ->greeting('Payment Authorisation Failed')
            ->line('Dear ' . $customer->first_name . ',')
            ->line('Unfortunately, your payment authorisation could not be completed at this time.')
            ->line('**Transaction Details**')
            ->line('• **Customer:** ' . $customer->first_name . ' ' . $customer->last_name)
            ->line('• **Contract ID:** ' . $contractId . ' — VRM: ' . $vrm)
            ->line('• **Subscription ID:** ' . $subscription->id)
            ->line('• **Amount:** £' . $this->citSession->amount)
            ->line('• **Date:** ' . $this->citSession->updated_at->format('d F Y H:i'))
            ->line('• **Reason:** ' . $this->failureReason)
            ->line('**What you can do:**')
            ->line('• Check that your card details are correct')
            ->line('• Ensure your card has sufficient funds')
            ->line('• Verify your card has not expired')
            ->line('• Contact your bank if the card is blocked')
            ->line('• Try using a different payment method')
            ->line('**Next Steps:**')
            ->line('Please contact our customer service team to resolve this issue and complete your payment authorisation.')
            ->line('Your recurring payment authorisation is not yet active and will need to be completed before your service can continue.')
            ->line('**Contact Information**')
            ->line('• **Phone:** 0203 409 5478 / 0208 314 1498')
            ->line('• **Email:** customerservice@neguinhomotors.co.uk')
            ->line('• **Website:** ngnmotors.co.uk')
            ->action('Contact Customer Service', 'mailto:customerservice@neguinhomotors.co.uk')
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
            'amount' => $this->citSession->amount,
            'status' => 'failed',
            'failure_reason' => $this->failureReason,
            'notified_at' => now(),
        ];
    }
}