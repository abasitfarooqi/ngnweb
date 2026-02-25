<?php

namespace App\Notifications\Channels;

use App\Http\Controllers\SMSController;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    protected UrlGenerator $url;

    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    public function send($notifiable, Notification $notification)
    {
        if (! method_exists($notification, 'toSms')) {
            Log::error('Notification does not have toSms method', [
                'notification' => get_class($notification),
            ]);

            return;
        }

        $message = $notification->toSms($notifiable);

        if (empty($message)) {
            Log::warning('Empty SMS message from notification', [
                'notification' => get_class($notification),
                'notifiable' => get_class($notifiable),
            ]);

            return;
        }

        // Get phone number from notifiable object
        $phoneNumber = $notifiable->phone ?? $notifiable->mobile ?? null;

        if (empty($phoneNumber)) {
            Log::error('No phone number found for SMS notification', [
                'notification' => get_class($notification),
                'notifiable' => get_class($notifiable),
                'notifiable_data' => $notifiable->toArray(),
            ]);

            return;
        }

        try {
            // Send SMS using existing SMSController
            $smsController = new SMSController;
            $result = $smsController->sendSms($phoneNumber, $message);

            if ($result['success']) {
                Log::info('SMS notification sent successfully', [
                    'notification' => get_class($notification),
                    'phone' => $phoneNumber,
                    'sid' => $result['sid'] ?? 'unknown',
                ]);
            } else {
                Log::error('SMS notification sending failed', [
                    'notification' => get_class($notification),
                    'phone' => $phoneNumber,
                    'error' => $result['message'] ?? 'Unknown error',
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('SMS notification exception', [
                'notification' => get_class($notification),
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
