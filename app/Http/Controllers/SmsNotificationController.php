<?php

namespace App\Http\Controllers;

use App\Services\SmsNotificationService;

class SmsNotificationController extends Controller
{
    protected $smsService;

    public function __construct(SmsNotificationService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function notifyPcnCustomers()
    {
        $this->smsService->sendPcnNotifications();

        return response()->json(['message' => 'SMS notifications sent successfully.']);
    }
}
