<?php

namespace App\Http\Controllers;

use App\Models\NgnSurvey;
use App\Models\NgnSurveyAnswer;
use App\Models\NgnSurveyResponse;
use App\Models\SurveyEmailCampaign;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    /**
     * Display the survey form.
     *
     * @param  int  $surveyId
     * @return \Illuminate\View\View
     */
    public function show($surveyId)
    {
        $survey = NgnSurvey::with('questions.options')->findOrFail($surveyId);

        return view('frontend.ngnsurvey.survey', compact('survey'));
    }

    /**
     * Display the survey form by slug.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function showBySlug($slug)
    {
        $survey = NgnSurvey::with('questions.options')->where('slug', $slug)->firstOrFail();

        return view('frontend.ngnsurvey.survey', compact('survey'));
    }

    /**
     * Handle the survey form submission.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit(Request $request)
    {
        \Log::info('Starting survey submission process.');

        // Log the incoming request data for debugging
        \Log::info('Form data received:', $request->all());

        $validatedData = $request->validate([
            'survey_id' => 'required|exists:ngn_surveys,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'opt_in' => 'boolean',
            'answers' => 'required|array',
        ]);

        \Log::info('Validation completed.', ['survey_id' => $validatedData['survey_id']]);

        $response = NgnSurveyResponse::create([
            'survey_id' => $validatedData['survey_id'],
            'contact_name' => $validatedData['name'],
            'contact_email' => $validatedData['email'],
            'contact_phone' => $validatedData['phone'],
            'is_contact_opt_in' => $validatedData['opt_in'] ?? false,
        ]);

        \Log::info('Survey response registered.', ['response_id' => $response->id]);

        foreach ($validatedData['answers'] as $questionId => $answer) {
            NgnSurveyAnswer::create([
                'response_id' => $response->id,
                'question_id' => $questionId,
                'option_id' => $answer['option_id'] ?? null,
                'answer_text' => $answer['answer_text'] ?? null,
            ]);
            \Log::info('Survey answer logged.', [
                'response_id' => $response->id,
                'question_id' => $questionId,
                'option_id' => $answer['option_id'],
            ]);
        }

        \Log::info('Survey submission process finalised.');

        return redirect()->back()->with('message', 'Thank you for your submission!');
    }

    public function thankyou()
    {
        return view('frontend.ngnsurvey.thankyou');
    }

    // IT WILL BE DELETED ---- no use of this function
    public function generateSurveyLink($surveyId, $userType)
    {
        // Generate a unique token for the link
        $token = bin2hex(random_bytes(16));

        // Create the survey link
        $link = route('survey.show', ['surveyId' => $surveyId, 'token' => $token, 'userType' => $userType]);

        // Optionally, store the link in the database for tracking
        // SurveyLink::create([
        //     'survey_id' => $surveyId,
        //     'token' => $token,
        //     'user_type' => $userType,
        //     'link' => $link,
        // ]);

        return $link;
    }

    public function index()
    {
        $surveyEmailCampaigns = SurveyEmailCampaign::count();

        return view('admin.survey_index', compact('surveyEmailCampaigns'));
    }

    public function getResponses($surveyId)
    {
        $responses = NgnSurveyResponse::with(['answers.question', 'answers.option'])
            ->where('survey_id', $surveyId) // Adjust the survey ID as needed
            ->get();

        return view('admin.survey_responses', compact('responses'));
    }

    /**
     * Fetch unique email records from club_members and customers.
     *
     * @return \Illuminate\Support\Collection
     */
    public function fetchUniqueEmails()
    {
        $clubMembers = \DB::table('club_members')
            ->select('full_name as name', 'email', 'phone')
            ->get();

        $customers = \DB::table('customers')
            ->select(\DB::raw('CONCAT(first_name, " ", last_name) as name'), 'email', 'phone')
            ->get();

        $combined = $clubMembers->merge($customers);

        $uniqueEmails = $combined->unique('email');

        return $uniqueEmails;
    }
}
