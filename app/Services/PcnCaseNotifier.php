<?php

namespace App\Services;

use App\Mail\PCNNotify;
use App\Mail\PCNPoliceNotify;
use App\Models\PcnCase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PcnCaseNotifier
{
    public function __construct(protected SmsNotificationService $smsService)
    {
    }

    public function sendEmail(int $pcnCaseId): void
    {
        $pcnCase = PcnCase::with(['customer', 'motorbike'])->find($pcnCaseId);

        if (! $pcnCase || $pcnCase->isClosed) {
            return;
        }

        $customer = $pcnCase->customer;
        $motorbike = $pcnCase->motorbike;

        if (! $customer || ! $motorbike || blank($customer->email)) {
            return;
        }

        $recipients = $this->shouldIgnoreEmail($customer->email)
            ? ['customerservice@neguinhomotors.co.uk']
            : [$customer->email, 'customerservice@neguinhomotors.co.uk'];

        $data = [
            'customer' => trim($customer->first_name.' '.$customer->last_name),
            'reg_no' => $motorbike->reg_no,
            'date_of_contravention' => $pcnCase->date_of_contravention,
            'pcn_number' => $pcnCase->pcn_number,
            'council_link' => $pcnCase->council_link,
        ];

        try {
            if ($pcnCase->is_police) {
                Mail::to($recipients)->send(new PCNPoliceNotify($data));
            } else {
                Mail::to($recipients)->send(new PCNNotify($data));
            }
            Log::info('PCN email sent to: '.implode(', ', $recipients));
        } catch (\Throwable $e) {
            Log::error('PCN email failed: '.$e->getMessage());
        }
    }

    public function sendSms(int $pcnCaseId): void
    {
        $pcnCase = PcnCase::with(['customer', 'motorbike'])->find($pcnCaseId);

        if (! $pcnCase) {
            return;
        }

        $customer = $pcnCase->customer;
        $motorbike = $pcnCase->motorbike;

        if (! $customer || ! $motorbike) {
            return;
        }

        $customerPhoneNumber = preg_replace('/[\s\-()]/', '', (string) $customer->phone);

        if (! $this->isValidUkNumber($customerPhoneNumber)) {
            Log::warning("Invalid phone for PCN SMS customer {$customer->id}: {$customerPhoneNumber}");

            return;
        }

        $smsMessage = "Dear {$customer->first_name} {$customer->last_name}, this is a reminder for Penalty Charge Notice {$pcnCase->pcn_number} regarding vehicle {$motorbike->reg_no}. The outstanding amount of £{$pcnCase->reduced_amount} is unpaid. Please make payment promptly to avoid increases. If you've already paid, contact us at 0208 314 1498 or WhatsApp 07951790568, NGN Motors.";

        try {
            $this->smsService->sendSms($customerPhoneNumber, $smsMessage);
            Log::info("PCN SMS sent to customer {$customer->id}");
        } catch (\Throwable $e) {
            Log::error("PCN SMS failed for customer {$customer->id}: ".$e->getMessage());
        }
    }

    public function notifyOnCreate(int $pcnCaseId): void
    {
        $this->sendEmail($pcnCaseId);
        $this->sendSms($pcnCaseId);
    }

    public function isValidUkNumber(?string $phoneNumber): bool
    {
        return (bool) preg_match('/^(\+44|07)\d{9,10}$/', (string) $phoneNumber);
    }

    public function shouldIgnoreEmail(?string $email): bool
    {
        if (blank($email)) {
            return true;
        }

        $patterns = [
            '/\d+no@/',
            '/email\.ngm$/',
            '/email\.com-$/',
            '/-[a-zA-Z0-9]+@/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $email)) {
                return true;
            }
        }

        return false;
    }
}
