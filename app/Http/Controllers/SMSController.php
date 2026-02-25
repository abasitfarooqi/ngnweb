<?php

namespace App\Http\Controllers;

use App\Models\SmsMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class SMSController extends Controller
{
    protected $sid;

    protected $token;

    protected $from;

    protected $client;

    public function __construct()
    {
        $this->sid = config('services.twilio.sid');
        $this->token = config('services.twilio.token');
        $this->from = config('services.twilio.from');

        $this->client = new Client($this->sid, $this->token);
    }

    public function handle(Request $request)
    {
        $sid = $request->input('MessageSid');
        $status = $request->input('MessageStatus');

        if ($sid) {
            SmsMessage::where('sid', $sid)->update([
                'status' => $status,
                'date_sent' => now(),
            ]);
        }

        return response()->noContent();
    }

    /**
     * Normalize UK phone number to +44 format
     * Handles formats like: 0723234526, +44723234526, +44 72 3234 526, 0044723234526, etc.
     */
    private function normalizeUKPhoneNumber($phoneNumber)
    {
        if (empty($phoneNumber)) {
            return $phoneNumber;
        }

        // Remove all spaces, dashes, parentheses, and other formatting characters
        $normalized = preg_replace('/[\s\-()\.]/', '', trim($phoneNumber));

        // Handle different UK number formats
        // +44 format (already correct) - e.g., +44723234526 or +44 72 3234 526
        if (preg_match('/^\+44\d{10}$/', $normalized)) {
            return $normalized;
        }

        // 07 format (UK mobile without country code) - e.g., 0723234526
        if (preg_match('/^07\d{9}$/', $normalized)) {
            return '+44'.substr($normalized, 1);
        }

        // 0044 format (international format with double zero) - e.g., 0044723234526
        if (preg_match('/^0044\d{10}$/', $normalized)) {
            return '+44'.substr($normalized, 4);
        }

        // 44 format (without + or 00) - e.g., 44723234526
        if (preg_match('/^44\d{10}$/', $normalized)) {
            return '+'.$normalized;
        }

        // Handle +44 with spaces that weren't fully removed (edge case)
        // e.g., +44 7 400 117242 -> +447400117242
        $cleaned = preg_replace('/[^\d+]/', '', $normalized);
        if (preg_match('/^\+44\d{10}$/', $cleaned)) {
            return $cleaned;
        }

        // Return normalized version (will be validated later)
        return $cleaned;
    }

    /**
     * Validate if the normalized phone number is a valid UK mobile number
     */
    private function isValidUKMobileNumber($phoneNumber)
    {
        // Normalize first
        $normalized = $this->normalizeUKPhoneNumber($phoneNumber);

        // UK mobile numbers should be +44 followed by 10 digits, starting with 7
        // Format: +447XXXXXXXXX
        if (preg_match('/^\+447\d{9}$/', $normalized)) {
            return true;
        }

        return false;
    }

    public function sendSms($phoneNumber, $message)
    {
        try {
            if (empty($this->from)) {
                throw new \Exception('Twilio "from" number is not set.');
            }

            // Normalize the phone number
            $normalizedPhone = $this->normalizeUKPhoneNumber($phoneNumber);

            if (! $this->isValidUKMobileNumber($normalizedPhone)) {
                Log::warning('Invalid UK mobile number format', [
                    'original' => $phoneNumber,
                    'normalized' => $normalizedPhone,
                ]);

                return [
                    'success' => false,
                    'message' => 'Invalid UK mobile number format.',
                ];
            }

            \Log::info('Sending SMS', [
                'original_number' => $phoneNumber,
                'normalized_number' => $normalizedPhone,
                'message_length' => strlen($message),
            ]);

            // Skip actual API call in non-production environments
            if (app()->environment('local', 'testing', 'staging')) {
                Log::info('LOCAL/TESTING ENVIRONMENT: Skipping actual SMS API call to avoid charges');
                Log::info('SMS would be sent to', [
                    'original' => $phoneNumber,
                    'normalized' => $normalizedPhone,
                ]);
                Log::info('SMS message: '.$message);

                return [
                    'success' => true,
                    'message' => 'SMS logged successfully (local environment - no actual SMS sent).',
                    'sid' => 'MOCK_'.uniqid(),
                ];
            }

            // Production environment - make actual API call
            // Use normalized phone number for Twilio API
            $response = $this->client->messages->create(
                $normalizedPhone,
                [
                    'from' => $this->from,
                    'body' => $message,
                    'statusCallback' => route('twilio.status.callback'),
                ]
            );

            SmsMessage::create([
                'sid' => $response->sid,
                'account_sid' => $response->accountSid,
                'api_version' => $response->apiVersion,
                'body' => $response->body,
                'date_created' => Carbon::parse($response->dateCreated),
                'date_sent' => $response->dateSent ? Carbon::parse($response->dateSent) : null,
                'date_updated' => Carbon::parse($response->dateUpdated),
                'direction' => $response->direction,
                'error_code' => $response->errorCode,
                'error_message' => $response->errorMessage,
                'from' => $response->from,
                'to' => $response->to,
                'num_media' => $response->numMedia,
                'num_segments' => $response->numSegments,
                'price' => $response->price,
                'price_unit' => $response->priceUnit,
                'status' => $response->status,
                'uri' => $response->uri,
                'subresource_uris' => json_encode($response->subresourceUris),
            ]);

            return [
                'success' => true,
                'message' => 'SMS sent successfully.',
                'sid' => $response->sid,
            ];
        } catch (\Exception $e) {
            Log::error('Twilio SMS Error: '.$e->getMessage());

            SmsMessage::create([
                'body' => $message,
                'from' => $this->from,
                'to' => $normalizedPhone ?? $phoneNumber,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'date_created' => Carbon::now(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send SMS. Please try again later.',
                'error' => $e->getMessage(),
            ];
        }
    }
}
