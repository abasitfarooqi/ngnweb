<?php

namespace App\Services;

use App\Models\PcnCase;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    public function sendPcnReminders()
    {
        $pcnCases = PcnCase::with('customer', 'motorbike')
            ->where('isClosed', false)
            ->where('date_of_contravention', '<=', now()->subDays(14))
            ->get();

        foreach ($pcnCases as $pcnCase) {
            $customer = $pcnCase->customer;
            $customerName = $customer->getFullNameAttribute();
            $phoneNumber = $this->formatPhoneNumber($customer->whatsapp ?? $customer->phone);
            $pcnNumber = $pcnCase->pcn_number;
            $vehicleReg = $pcnCase->motorbike->reg_no;
            // changes
            $fullAmount = $pcnCase->reduced_amount;
            $reducedAmount = $pcnCase->reduced_amount;

            // Determine the amount due based on whether the reduced amount applies
            $amountDue = $pcnCase->date_of_contravention >= now()->subDays(14) ? $reducedAmount : $fullAmount;

            if ($this->isValidUKMobileNumber($phoneNumber)) {
                $message = "Dear $customerName, this is a reminder for Penalty Charge Notice $pcnNumber regarding vehicle $vehicleReg. The outstanding amount of £$amountDue is unpaid. Please make payment promptly to avoid increases. If you've already paid, contact us at 0208 314 1498 or WhatsApp 07951790568, NGN Motors.";

                // Generate WhatsApp link
                $url = "https://wa.me/{$phoneNumber}?text=".urlencode($message);

                // Uncomment this to open the link automatically (works only if the server has GUI/browser support)
                // exec("xdg-open '$url'"); // For Linux
                // exec("start $url"); // For Windows
                // exec("open '$url'"); // For macOS

                // Log the intended action
                Log::info("Intended to send WhatsApp reminder to customer ID: {$customer->id}, Phone: $phoneNumber, Message: $message, URL: $url");
                Log::info("url: $url");
            } else {
                Log::warning("Invalid phone number for customer ID: {$customer->id}, Phone: $phoneNumber");
            }
        }

        // // Test WhatsApp message
        // $testPhoneNumber = $this->formatPhoneNumber('07723234526');
        // $senderNumber = '+447723234526'; // Sender's WhatsApp number in international format
        // $testMessage = "This is a test message to verify that WhatsApp is working correctly. Sent from: $senderNumber";
        // $testUrl = "https://wa.me/{$testPhoneNumber}?text=" . urlencode($testMessage);

        // try {
        //     exec("open '$testUrl'"); // Open the link (change for your OS)
        //     Log::info("Test WhatsApp message sent to $testPhoneNumber");
        // } catch (\Exception $e) {
        //     Log::error("Failed to send test WhatsApp message to $testPhoneNumber. Error: " . $e->getMessage());
        // }

        Log::info('Completed sending PCN reminders.');
    }

    /**
     * Validate UK phone number format.
     */
    private function isValidUKMobileNumber($phoneNumber)
    {
        return preg_match('/^(\+44|07)\d{9,10}$/', $phoneNumber);
    }

    /**
     * Format UK phone number to international format.
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Convert 07 to +44 format
        if (strpos($phoneNumber, '07') === 0) {
            $phoneNumber = '+44'.substr($phoneNumber, 1);
        }

        return $phoneNumber;
    }
}
