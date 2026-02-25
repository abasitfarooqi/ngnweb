<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\JudopayMitPaymentSession;
use App\Models\JudopaySubscription;

class FireMitSuccessInternalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $mitSession;
    protected $subscription;

    /**
     * Create a new notification instance.
     */
    public function __construct(JudopayMitPaymentSession $mitSession, JudopaySubscription $subscription)
    {
        $this->mitSession = $mitSession;
        $this->subscription = $subscription;
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
        $completedAt = $this->mitSession->payment_completed_at ?? now();
        $authorizedBy = optional($this->mitSession->user)->name ?? 'System';

        return (new MailMessage)
            ->subject('✅ Fire MIT Payment SUCCESS - ' . $customerName)
            ->greeting('Fire MIT Payment Processed Successfully')
            ->line('A direct MIT payment has been processed successfully.')
            ->line('**Payment Details:**')
            ->line('• **Amount:** £' . number_format($amount, 2))
            ->line('• **Contract ID:** ' . $contractId)
            ->line('• **VRM:** ' . $vrm)
            ->line('• **Subscription ID:** ' . ($this->subscription?->id ?? 'N/A'))
            ->line('**Customer:** ' . $customerName . ' | ' . ($customer?->email ?? 'N/A'))
            ->line('**Processed:** ' . $completedAt->format('d/m/Y H:i:s'))
            ->line('• **Type:** Fire MIT (Direct Payment)')
            ->line('• **Payment Reference:** ' . $paymentReference)
            ->line('**Session Info:**')
            ->line('• **Session ID:** ' . $this->mitSession->id)
            ->line('• **Receipt ID:** ' . ($this->mitSession->judopay_receipt_id ?? 'Pending'))
            ->line('• **Authorized By:** ' . $authorizedBy)
            ->line('• **Scheduled For:** ' . $this->mitSession->scheduled_for->format('d/m/Y H:i:s'))
            ->line('• **Attempt:** ' . $this->mitSession->attempt_no)
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
            'status' => 'success',
            'type' => 'fire_mit',
            'authorized_by' => optional($this->mitSession->user)->name ?? 'System',
            'notified_at' => now(),
        ];
    }
}
