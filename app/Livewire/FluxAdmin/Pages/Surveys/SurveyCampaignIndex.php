<?php

namespace App\Livewire\FluxAdmin\Pages\Surveys;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnSurvey;
use App\Models\SurveyEmailCampaign;
use App\Services\SmsNotificationService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Survey campaign — Flux Admin')]
class SurveyCampaignIndex extends Component
{
    use WithAuthorization;
    use WithPagination;

    public NgnSurvey $survey;

    public function mount(NgnSurvey $survey): void
    {
        $this->authorizeModule('see-menu-surveys');
        $this->survey = $survey;
    }

    public function markWhatsAppSent(int $id): void
    {
        $campaign = SurveyEmailCampaign::query()
            ->where('ngn_survey_id', $this->survey->id)
            ->findOrFail($id);

        $campaign->update([
            'is_whatsapp_sent' => true,
            'last_whatsapp_sent_datetime' => now(),
        ]);

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Marked WhatsApp sent.');
    }

    public function sendSmsReminder(int $id): void
    {
        $campaign = SurveyEmailCampaign::query()
            ->where('ngn_survey_id', $this->survey->id)
            ->findOrFail($id);

        if ($campaign->is_sms_sent) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'SMS has already been sent to this contact.');

            return;
        }

        $surveyLink = route('survey.showBySlug', ['slug' => $this->survey->slug]);
        $smsMessage = "NGN MOTORS – Hello {$campaign->fullname}, we value your opinion. Be a part of Motorbike Preference Survey at {$surveyLink}. Thanks for helping us improve!";

        try {
            app(SmsNotificationService::class)->sendSms($campaign->phone, $smsMessage);
            $campaign->update([
                'is_sms_sent' => true,
                'last_sms_sent_datetime' => now(),
            ]);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'SMS reminder sent.');
        } catch (\Throwable $e) {
            Log::error('Flux survey SMS failed: '.$e->getMessage());
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Failed to send SMS reminder.');
        }
    }

    public function render()
    {
        $campaigns = SurveyEmailCampaign::query()
            ->where('ngn_survey_id', $this->survey->id)
            ->orderByDesc('id')
            ->paginate(20);

        $rows = $campaigns->getCollection()->map(function (SurveyEmailCampaign $campaign) {
            $phoneNumber = preg_replace('/\s+/', '', (string) $campaign->phone);
            $phoneNumber = preg_replace('/^0/', '', (string) $phoneNumber);
            $phoneNumber = preg_replace('/^(\+44|44)/', '', (string) $phoneNumber);
            $phoneNumber = '+44'.$phoneNumber;

            $surveyLink = route('survey.showBySlug', ['slug' => $this->survey->slug]);
            $message = "NGN MOTORS – Hello {$campaign->fullname}, we value your opinion. Please take a moment to complete our Motorbike Preference Survey:\n{$surveyLink}\nThank you for helping us improve!";

            return [
                'id' => $campaign->id,
                'fullname' => $campaign->fullname,
                'email' => $campaign->email,
                'phone' => $campaign->phone,
                'url_whatsapp' => 'https://wa.me/'.$phoneNumber.'?text='.urlencode($message),
                'is_email_sent' => (bool) $campaign->is_email_sent,
                'is_sms_sent' => (bool) $campaign->is_sms_sent,
                'is_whatsapp_sent' => (bool) $campaign->is_whatsapp_sent,
                'last_whatsapp_sent_datetime' => $campaign->last_whatsapp_sent_datetime,
                'last_sms_sent_datetime' => $campaign->last_sms_sent_datetime,
            ];
        });

        return view('flux-admin.pages.surveys.campaign', [
            'campaigns' => $campaigns,
            'rows' => $rows,
        ]);
    }
}
