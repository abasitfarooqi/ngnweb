<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\JudopayMitQueue;

class MitSuccessCustomerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $mitQueue;

    /**
     * Create a new notification instance.
     */
    public function __construct(JudopayMitQueue $mitQueue)
    {
        $this->mitQueue = $mitQueue;
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
        $clearedAt = $this->mitQueue->cleared_at ?? now();

        return (new MailMessage)
            ->subject('✅ Recurring Payment Successful - NGN Motors')
            ->greeting('Payment Processed Successfully')
            ->line('Dear ' . $customer->first_name . ',')
            ->line('Your recurring payment has been processed successfully.')
            ->line('**Payment Details**')
            ->line('• **Customer:** ' . $customer->first_name . ' ' . $customer->last_name)
            ->line('• **Contract ID:** ' . $contractId . ' — VRM: ' . $vrm)
            ->line('• **Subscription ID:** ' . $subscription->id)
            ->line('• **Amount:** £' . number_format($amount, 2))
            ->line('• **Payment Reference:** ' . $paymentReference)
            ->line('• **Date:** ' . $clearedAt->format('d F Y H:i'))
            ->line('• **Attempt:** ' . ucfirst($this->mitQueue->ngnMitQueue->mit_attempt))
            ->line('**Your payment is now complete.**')
            ->line('This payment will appear on your bank statement as: **NGNNegui**')
            ->line('**Questions?**')
            ->line('If you have any concerns about this payment, please contact us immediately.')
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
            'payment_reference' => $this->mitQueue->judopay_payment_reference,
            'status' => 'success',
            'notified_at' => now(),
        ];
    }
}
