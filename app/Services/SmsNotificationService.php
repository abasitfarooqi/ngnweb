<?php

namespace App\Services;

use App\Models\PcnCase;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class SmsNotificationService
{
    // / it need some changes it is send msgs daily it should check which person is just received the msg check date and with differece of 14 days it should send the msg again,
    // chcek whatsapp_reminder_sent_at  and is_whatsapp_sent is true then check the date and if the date is less than 14 days then send the msg again
    protected $client;

    protected $from;

    public function __construct()
    {
        $this->from = config('services.twilio.from');
        $this->client = new Client(config('services.twilio.sid'), config('services.twilio.token'));
    }

    public function sendPcnReminders()
    {
        $pcnCases = PcnCase::with('customer', 'motorbike')
            ->where('isClosed', false)
            ->where('date_of_contravention', '<=', now()->subDays(14))
            ->get();

        Log::info('PCN Cases: '.$pcnCases->count());
        foreach ($pcnCases as $pcnCase) {
            // Check if the WhatsApp reminder within 14 days has already been sent
            if ($pcnCase->is_whatsapp_sent && $pcnCase->whatsapp_last_reminder_sent_at > now()->subDays(14)) {
                continue; // Skip to the next case if already sent
            }

            // Log customer details for debugging
            try {
                Log::info('PCN Case customer details: '.json_encode([
                    'customer_id' => $pcnCase->customer->id,
                    'name' => $pcnCase->customer->first_name.' '.$pcnCase->customer->last_name,
                    'phone' => $pcnCase->customer->phone,
                    'pcn_number' => $pcnCase->pcn_number,
                    'vehicle_reg' => $pcnCase->motorbike->reg_no ?? 'Unknown',
                    'amount_due' => $pcnCase->reduced_amount,
                    'full_amount' => $pcnCase->full_amount,
                    'reduced_amount' => $pcnCase->reduced_amount,
                    'date_of_contravention' => $pcnCase->date_of_contravention,
                    'is_sms_sent' => $pcnCase->is_sms_sent ? 'Yes' : 'No',
                    'sms_last_sent_at' => $pcnCase->sms_last_sent_at,
                ]));
            } catch (\Exception $e) {
                Log::warning('PCN Case ID: '.$pcnCase->id.' has no associated customer or missing data: '.$e->getMessage());

                continue; // Skip to the next case if customer data is incomplete
            }

            // Check if the SMS reminder within 14 days has already been sent
            if ($pcnCase->is_sms_sent && $pcnCase->sms_last_sent_at > now()->subDays(14)) {
                continue; // Skip to the next case if already sent
            }

            $customer = $pcnCase->customer;
            if ($customer) {
                $customerName = $customer->first_name.' '.$customer->last_name;
            } else {
                Log::warning("Customer not found for PCN Case ID: {$pcnCase->id}");

                continue; // Skip to the next case if customer is not found
            }

            $phoneNumber = $customer->phone;

            if (str_starts_with($phoneNumber, '7')) {
                $phoneNumber = '0'.$phoneNumber;
            }

            $phoneNumber = str_replace(' ', '', $phoneNumber);

            $pcnNumber = $pcnCase->pcn_number;
            $vehicleReg = $pcnCase->motorbike->reg_no;
            // changes
            $fullAmount = $pcnCase->reduced_amount;
            $reducedAmount = $pcnCase->reduced_amount;

            // Determine the amount due based on whether the reduced amount applies
            $amountDue = $pcnCase->date_of_contravention >= now()->subDays(14) ? $reducedAmount : $fullAmount;

            if ($this->isValidUKMobileNumber($phoneNumber)) {
                // Check if the customer has been notified within the last 10 days
                // if ($pcnCase->is_whatsapp_sent && $pcnCase->whatsapp_last_reminder_sent_at > now()->subDays(14)) {
                //     continue; // Skip to the next case if already sent within the last 10 days
                // }

                $message = "Dear $customerName, this is a reminder for Penalty Charge Notice $pcnNumber regarding vehicle $vehicleReg. The outstanding amount of £$amountDue is unpaid. Please make payment promptly to avoid increases. If you've already paid, contact us at 0208 314 1498 or WhatsApp 07951790568, NGN Motors.";
                Log::info("Message: $message");
                $updatedCount = 0;
                $failedCount = 0;
                $failedRecords = [];

                try {

                    $this->sendSms($phoneNumber, $message);
                    Log::info("SMS sent to customer ID: {$customer->id}, Phone: $phoneNumber");

                    // Update the PCN case for SMS
                    $pcnCase->is_sms_sent = true;
                    $pcnCase->sms_last_sent_at = now();

                    // Update the PCN case for WhatsApp
                    $pcnCase->is_whatsapp_sent = true;
                    $pcnCase->whatsapp_last_reminder_sent_at = now();

                    $pcnCase->save();
                    $updatedCount++;

                } catch (\Exception $e) {
                    Log::error("Failed to send SMS to customer ID: {$customer->id}, Phone: $phoneNumber. Error: ".$e->getMessage());
                    $failedCount++;
                    $failedRecords[] = [
                        'contact_number' => $phoneNumber,
                        'name' => $customerName,
                        'pcn_number' => $pcnNumber,
                        'amount' => $amountDue,
                    ];
                }

                // Log the summary of updates and failures

                if ($failedCount > 0) {
                    Log::info('Failed records: ', $failedRecords);
                }

                // Log the intended action instead of sending
                Log::info("Intended to send reminder SMS to customer ID: {$customer->id}, Phone: $phoneNumber, Message: $message");
            } else {
                Log::warning("Invalid phone number for customer ID: {$customer->id}, Phone: $phoneNumber");
            }
        }

        Log::info('Completed sending PCN reminders.');
    }

    public function sendSms($phoneNumber, $message)
    {
        // Skip actual API call in non-production environments
        if (app()->environment('local', 'testing', 'staging')) {
            Log::info('LOCAL/TESTING ENVIRONMENT: Skipping actual SMS API call to avoid charges');
            Log::info('SMS would be sent to: '.$phoneNumber);
            Log::info('SMS message: '.$message);
            return;
        }

        // Production environment - make actual API call
        $this->client->messages->create(
            $phoneNumber,
            [
                'from' => $this->from,
                'body' => $message,
            ]
        );
    }

    private function isValidUKMobileNumber($phoneNumber)
    {
        return preg_match('/^(\+44|07)\d{9,10}$/', $phoneNumber);
    }
}
