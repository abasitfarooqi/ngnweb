<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\JudopayMitPaymentSession;
use App\Models\JudopaySubscription;

class FireMitFailureCustomerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $mitSession;
    protected $subscription;
    protected $failureReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(JudopayMitPaymentSession $mitSession, JudopaySubscription $subscription, string $failureReason)
    {
        $this->mitSession = $mitSession;
        $this->subscription = $subscription;
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
        // Standardised customer resolution
        $customer = $this->subscription->subscribable?->customer ?? $this->subscription->customer;
        $serviceData = $this->subscription->subscribable;

        // Get contract details
        $contractId = $serviceData->id ?? 'N/A';
        $vrm = 'N/A';
        
        if ($this->subscription->subscribable_type === 'App\Models\RentingBooking') {
            $vrm = $serviceData->rentingBookingItems->first()->motorbike->reg_no ?? 'N/A';
        } elseif ($this->subscription->subscribable_type === 'App\Models\FinanceApplication') {
            $vrm = $serviceData->application_items->first()->motorbike->reg_no ?? 'N/A';
        }

        $paymentReference = $this->mitSession->judopay_payment_reference;
        $amount = $this->mitSession->amount;

        return (new MailMessage)
            ->subject('❌ Payment Declined - NGN Motors')
            ->greeting('Payment Could Not Be Processed')
            ->line('Dear ' . $customer->first_name . ',')
            ->line('We were unable to process your payment. Your card was declined.')
            ->line('**Payment Details**')
            ->line('• **Customer:** ' . $customer->first_name . ' ' . $customer->last_name)
            ->line('• **Contract ID:** ' . $contractId . ' — VRM: ' . $vrm)
            ->line('• **Subscription ID:** ' . $this->subscription->id)
            ->line('• **Amount:** £' . number_format($amount, 2))
            ->line('• **Payment Reference:** ' . $paymentReference)
            ->line('• **Decline Reason:** ' . $this->failureReason)
            ->line('• **Date:** ' . now()->format('d F Y H:i'))
            ->line('**What happens next?**')
            ->line('Please contact us immediately to resolve this payment issue and avoid any service interruption.')
            ->line('**Common reasons for card declines:**')
            ->line('• Insufficient funds')
            ->line('• Card expired or blocked')
            ->line('• Incorrect card details')
            ->line('• Bank security restrictions')
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
            'session_id' => $this->mitSession->id,
            'subscription_id' => $this->subscription->id,
            'amount' => $this->mitSession->amount,
            'payment_reference' => $this->mitSession->judopay_payment_reference,
            'status' => 'declined',
            'type' => 'fire_mit',
            'failure_reason' => $this->failureReason,
            'notified_at' => now(),
        ];
    }
}
