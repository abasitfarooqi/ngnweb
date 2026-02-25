<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\JudopayMitQueue;

class MitFailureInternalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $mitQueue;
    protected $failureReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(JudopayMitQueue $mitQueue, string $failureReason = 'Unknown')
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
        $customer = $subscription->subscribable?->customer;
        
        $serviceData = $subscription->subscribable;

        // Get contract details with null safety
        $contractId = $serviceData?->id ?? 'N/A';
        $vrm = 'N/A';
        
        if ($subscription->subscribable_type === 'App\Models\RentingBooking' && $serviceData) {
            $vrm = $serviceData->rentingBookingItems?->first()?->motorbike?->reg_no ?? 'N/A';
        } elseif ($subscription->subscribable_type === 'App\Models\FinanceApplication' && $serviceData) {
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

        $amount = $subscription->amount;
        $paymentReference = $this->mitQueue->judopay_payment_reference;
        $attempt = ucfirst($this->mitQueue->ngnMitQueue->mit_attempt);
        $hasRetry = $this->mitQueue->ngnMitQueue->mit_attempt !== 'manual';

        $mailMessage = (new MailMessage)
            ->subject('❌ MIT Payment FAILED - ' . $customerName)
            ->greeting('MIT Payment Failed!')
            ->line('A recurring payment has been declined.')
            ->line('**Failure Details:**')
            ->line('• **Reason:** ' . $this->failureReason)
            ->line('• **Amount:** £' . number_format($amount, 2))
            ->line('• **Contract ID:** ' . $contractId)
            ->line('• **VRM:** ' . $vrm)
            ->line('• **Subscription ID:** ' . ($subscription?->id ?? 'N/A'))
            ->line('**Customer:** ' . $customerName . ' | ' . ($customer?->email ?? 'N/A'))
            ->line('**Failed At:** ' . now()->format('d/m/Y H:i:s'))
            ->line('• **Payment Reference:** ' . $paymentReference)
            ->line('**MIT Queue Info:**')
            ->line('• **MIT Queue ID:** ' . $this->mitQueue->id)
            ->line('• **NGN MIT Queue ID:** ' . $this->mitQueue->ngn_mit_queue_id)
            ->line('• **Fired:** ' . ($this->mitQueue->fired ? 'Yes' : 'No'))
            ->line('• **Retry Count:** ' . $this->mitQueue->retry)
            ->line('• **Authorized By:** ' . optional($this->mitQueue->authorizedBy)->name ?? 'System')
            ->line('**Action Required:**');
        
        if ($hasRetry) {
            $mailMessage->line('✅ RETRY SCHEDULED - System will retry this payment automatically tomorrow at 16:45.');
        }
        
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
