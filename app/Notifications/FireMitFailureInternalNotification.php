<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\JudopayMitPaymentSession;
use App\Models\JudopaySubscription;

class FireMitFailureInternalNotification extends Notification implements ShouldQueue
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
        $customer = $this->subscription->subscribable?->customer ?? $this->subscription->customer;
        $serviceData = $this->subscription->subscribable;

        // Get contract details with null safety
        $contractId = $serviceData?->id ?? 'N/A';
        $vrm = 'N/A';
        
        if ($this->subscription->subscribable_type === 'App\Models\RentingBooking' && $serviceData) {
            $vrm = $serviceData->rentingBookingItems?->first()?->motorbike?->reg_no ?? 'N/A';
        } elseif ($this->subscription->subscribable_type === 'App\Models\FinanceApplication' && $serviceData) {
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

        $amount = $this->mitSession->amount;
        $paymentReference = $this->mitSession->judopay_payment_reference;
        $authorizedBy = optional($this->mitSession->user)->name ?? 'System';

        return (new MailMessage)
            ->subject('❌ Fire MIT Payment FAILED - ' . $customerName)
            ->greeting('Fire MIT Payment Failed!')
            ->line('A direct MIT payment has been declined.')
            ->line('**Failure Details:**')
            ->line('• **Reason:** ' . $this->failureReason)
            ->line('• **Amount:** £' . number_format($amount, 2))
            ->line('• **Contract ID:** ' . $contractId)
            ->line('• **VRM:** ' . $vrm)
            ->line('• **Subscription ID:** ' . ($this->subscription?->id ?? 'N/A'))
            ->line('**Customer:** ' . $customerName . ' | ' . ($customer?->email ?? 'N/A'))
            ->line('**Failed At:** ' . now()->format('d/m/Y H:i:s'))
            ->line('• **Type:** Fire MIT (Direct Payment)')
            ->line('• **Payment Reference:** ' . $paymentReference)
            ->line('**Session Info:**')
            ->line('• **Session ID:** ' . $this->mitSession->id)
            ->line('• **Authorized By:** ' . $authorizedBy)
            ->line('• **Scheduled For:** ' . $this->mitSession->scheduled_for->format('d/m/Y H:i:s'))
            ->line('**Action Required:**')
            ->line('⚠️ MANUAL INTERVENTION NEEDED - Fire MIT payments do not have automatic retry. Please investigate and take appropriate action.')
            ->action('View Subscription', route('page.judopay.subscribe', $this->subscription?->id ?? 0))
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
            'session_id' => $this->mitSession->id,
            'subscription_id' => $this->subscription->id,
            'amount' => $this->mitSession->amount,
            'payment_reference' => $this->mitSession->judopay_payment_reference,
            'status' => 'declined',
            'type' => 'fire_mit',
            'failure_reason' => $this->failureReason,
            'authorized_by' => optional($this->mitSession->user)->name ?? 'System',
            'notified_at' => now(),
        ];
    }
}
