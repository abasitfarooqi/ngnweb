<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Models\NgnSurvey;
use App\Models\SurveyEmailCampaign;
use App\Services\SmsNotificationService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class NgnSurveyCampaignController extends Controller
{
    public function index($surveyId)
    {
        $surveyEmailCampaigns = SurveyEmailCampaign::where('ngn_survey_id', $surveyId)->paginate(20);

        $campaignData = [];
        foreach ($surveyEmailCampaigns as $campaign) {
            // $customer = Customer::where('phone', $campaign->phone)->first();

            // if (!$customer) {
            //     continue;
            // }

            // customer phone if WhatsApp number is not available or less than 4 characters
            // $rawPhone = (strlen($customer->whatsapp) > 4) ? $customer->whatsapp : $customer->phone;

            // Format number to international +44 format
            $phoneNumber = preg_replace('/\s+/', '', $campaign->phone);
            $phoneNumber = preg_replace('/^0/', '', $phoneNumber);
            $phoneNumber = preg_replace('/^(\+44|44)/', '', $phoneNumber);
            $phoneNumber = '+44'.$phoneNumber;

            // Get survey and generate link
            $survey = NgnSurvey::find($campaign->ngn_survey_id);
            $surveyLink = route('survey.showBySlug', ['slug' => $survey->slug]);

            // Message for WhatsApp
            // $message = "NGN MOTORS – Hello {$campaign->fullname}, we value your opinion. Please take a moment to complete our Motorbike Preference Survey at {$surveyLink} , Thank you for helping us improve!";
            $message = "NGN MOTORS – Hello {$campaign->fullname}, we value your opinion. Please take a moment to complete our Motorbike Preference Survey:\n{$surveyLink}\nThank you for helping us improve!";

            $sms_message = "NGN MOTORS – Hello {$campaign->fullname}, we value your opinion. Be a part of Motorbike Preference Survey at {$surveyLink} , Thanks for helping us improve!";
            $url_whatsapp = "https://wa.me/{$phoneNumber}?text=".urlencode($message);

            $campaignData[] = [
                'id' => $campaign->id,
                'fullname' => $campaign->fullname,
                'email' => $campaign->email,
                'phone' => $campaign->phone,
                'url_whatsapp' => $url_whatsapp,
                'sms_message' => $sms_message,
                'is_email_sent' => $campaign->is_email_sent,
                'is_sms_sent' => $campaign->is_sms_sent,
                'is_whatsapp_sent' => $campaign->is_whatsapp_sent,
                'last_email_sent_datetime' => $campaign->last_email_sent_datetime,
                'last_sms_sent_datetime' => $campaign->last_sms_sent_datetime,
                'last_whatsapp_sent_datetime' => $campaign->last_whatsapp_sent_datetime,
            ];
        }

        return view('admin.ngn_survey_campaign', [
            'title' => 'Ngn Survey Campaign',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'NgnSurveyCampaign' => false,
            ],
            'page' => 'resources/views/admin/ngn_survey_campaign.blade.php',
            'controller' => 'app/Http/Controllers/Admin/NgnSurveyCampaignController.php',
            'surveyEmailCampaigns' => $surveyEmailCampaigns,
            'surveyId' => $surveyId,
            'campaignData' => $campaignData,
        ]);
    }

    public function sendReminder($id)
    {
        $surveyEmailCampaign = SurveyEmailCampaign::findOrFail($id);
        $surveyEmailCampaign->update([
            'is_whatsapp_sent' => true,
            'last_whatsapp_sent_datetime' => now(),
        ]);

        return redirect()->back()->with('success', 'WhatsApp reminder sent successfully.');
    }

    public function sendSmsReminder($id)
    {
        $surveyEmailCampaign = SurveyEmailCampaign::findOrFail($id);

        // Get survey and generate link
        $survey = NgnSurvey::find($surveyEmailCampaign->ngn_survey_id);
        $surveyLink = route('survey.showBySlug', ['slug' => $survey->slug]);

        // Check if SMS has already been sent
        if ($surveyEmailCampaign->is_sms_sent) {
            return redirect()->back()->with('error', 'SMS has already been sent to this user.');
        }
        $sms_message = "NGN MOTORS – Hello {$surveyEmailCampaign->fullname}, we value your opinion. Be a part of Motorbike Preference Survey at {$surveyLink}. Thanks for helping us improve!";
        $phoneNumber = $surveyEmailCampaign->phone;

        $smsService = new SmsNotificationService;
        try {
            $smsService->sendSms($phoneNumber, $sms_message);
            Log::info("SMS reminder has been dispatched to: {$phoneNumber}, with the following message: {$sms_message}");

            // Update the campaign to mark SMS as sent
            $surveyEmailCampaign->update([
                'is_sms_sent' => true,
                'last_sms_sent_datetime' => now(),
            ]);

            return redirect()->back()->with('success', 'SMS reminder sent successfully.');
        } catch (\Exception $e) {
            Log::error("Failed to send SMS to: {$phoneNumber}. Error: ".$e->getMessage());

            return redirect()->back()->with('error', 'Failed to send SMS reminder.');
        }
    }
}
