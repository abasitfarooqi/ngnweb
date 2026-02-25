<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\JudopayMitQueue;

class MitFailureCustomerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $mitQueue;
    protected $failureReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(JudopayMitQueue $mitQueue, string $failureReason = 'Payment was declined')
    {
        $this->mitQueue = $mitQueue;
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
        $subscription = $this->mitQueue->ngnMitQueue->subscribable;
        
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

        $paymentReference = $this->mitQueue->judopay_payment_reference;
        $amount = $subscription->amount;
        $attempt = ucfirst($this->mitQueue->ngnMitQueue->mit_attempt);
        $hasRetry = $this->mitQueue->ngnMitQueue->mit_attempt !== 'manual';

        $mailMessage = (new MailMessage)
            ->subject('⚠️ Recurring Payment Declined - NGN Motors')
            ->greeting('Payment Declined')
            ->line('Dear ' . $customer->first_name . ',')
            ->line('Unfortunately, your recurring payment could not be processed at this time.')
            ->line('**Payment Details**')
            ->line('• **Customer:** ' . $customer->first_name . ' ' . $customer->last_name)
            ->line('• **Contract ID:** ' . $contractId . ' — VRM: ' . $vrm)
            ->line('• **Subscription ID:** ' . $subscription->id)
            ->line('• **Amount:** £' . number_format($amount, 2))
            ->line('• **Payment Reference:** ' . $paymentReference)
            ->line('• **Date:** ' . now()->format('d F Y H:i'))
            ->line('• **Reason:** ' . $this->failureReason)
            ->line('**What you can do:**')
            ->line('• Ensure your card has sufficient funds')
            ->line('• Contact your bank if the card is blocked')
            ->line('• Contact our customer service team if you need help')
            ->line('**Next Steps:**');
        
        if ($hasRetry) {
            $mailMessage->line('We will automatically attempt to process this payment again tomorrow.')
                ->line('Please ensure your card has sufficient funds by then to avoid service interruption.');
        }
        
        return $mailMessage
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
            'mit_queue_id' => $this->mitQueue->id,
            'ngn_mit_queue_id' => $this->mitQueue->ngn_mit_queue_id,
            'subscription_id' => $this->mitQueue->ngnMitQueue->subscribable_id,
            'amount' => $this->mitQueue->ngnMitQueue->subscribable->amount,
            'status' => 'failed',
            'failure_reason' => $this->failureReason,
            'notified_at' => now(),
        ];
    }
}
