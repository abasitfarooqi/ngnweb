<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Mail\NgnPartnerRegistrationMailer;
use App\Models\BlogPost;
use App\Models\Motorbike;
use App\Models\Motorcycle;
use App\Models\NgnPartner;
use App\Models\NgnProduct;
use App\Models\NgnSurvey;
use App\Models\NgnSurveyAnswer;
use App\Models\NgnSurveyResponse;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Mobile-native equivalents of small standalone web forms/pages that did not
 * yet have a dedicated /api/v1/mobile endpoint (newsletter, search, surveys,
 * partner subscribe, accident management claim).
 */
class MobileMiscController extends Controller
{
    public function newsletterSubscribe(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        Subscriber::query()->firstOrCreate(['email' => strtolower(trim($payload['email']))]);

        return response()->json(['message' => 'Thanks for subscribing.']);
    }

    public function search(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $term = trim((string) $payload['q']);
        $limit = (int) ($payload['limit'] ?? 8);

        $bikesNew = Motorcycle::query()
            ->where('availability', 'for sale')
            ->where(function ($q) use ($term) {
                $q->where('make', 'like', "%{$term}%")->orWhere('model', 'like', "%{$term}%");
            })
            ->limit($limit)
            ->get(['id', 'make', 'model', 'sale_new_price'])
            ->map(fn (Motorcycle $m) => [
                'type' => 'bike_new',
                'id' => $m->id,
                'title' => trim((string) $m->make.' '.$m->model),
                'price' => (float) ($m->sale_new_price ?? 0),
            ]);

        $bikesUsed = Motorbike::query()
            ->join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
            ->select('motorbikes.id', 'motorbikes.make', 'motorbikes.model', 'motorbikes_sale.price')
            ->where('motorbikes_sale.is_sold', 0)
            ->where(function ($q) use ($term) {
                $q->where('motorbikes.make', 'like', "%{$term}%")->orWhere('motorbikes.model', 'like', "%{$term}%");
            })
            ->limit($limit)
            ->get()
            ->map(fn ($m) => [
                'type' => 'bike_used',
                'id' => $m->id,
                'title' => trim((string) $m->make.' '.$m->model),
                'price' => (float) ($m->price ?? 0),
            ]);

        $products = NgnProduct::query()
            ->where('is_ecommerce', 1)
            ->where(function ($q) {
                $q->whereNull('dead')->orWhere('dead', 0);
            })
            ->where('name', 'like', "%{$term}%")
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'normal_price'])
            ->map(fn (NgnProduct $p) => [
                'type' => 'shop_product',
                'id' => $p->id,
                'title' => $p->name,
                'slug' => $p->slug,
                'price' => (float) ($p->normal_price ?? 0),
            ]);

        $blogPosts = BlogPost::query()
            ->where('title', 'like', "%{$term}%")
            ->limit($limit)
            ->get(['id', 'title', 'slug'])
            ->map(fn (BlogPost $b) => [
                'type' => 'blog_post',
                'id' => $b->id,
                'title' => $b->title,
                'slug' => $b->slug,
            ]);

        return response()->json([
            'query' => $term,
            'data' => [
                'bikes_new' => $bikesNew,
                'bikes_used' => $bikesUsed,
                'shop_products' => $products,
                'blog_posts' => $blogPosts,
            ],
        ]);
    }

    public function surveyShow(int $id): JsonResponse
    {
        $survey = NgnSurvey::with('questions.options')->where('is_active', true)->find($id);
        if (! $survey) {
            return response()->json(['message' => 'Survey not found.'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $survey->id,
                'title' => $survey->title,
                'slug' => $survey->slug,
                'description' => $survey->description,
                'questions' => $survey->questions->map(fn ($q) => [
                    'id' => $q->id,
                    'question_text' => $q->question_text,
                    'question_type' => $q->question_type,
                    'is_required' => (bool) $q->is_required,
                    'options' => $q->options->map(fn ($o) => ['id' => $o->id, 'option_text' => $o->option_text ?? null])->values(),
                ])->values(),
            ],
        ]);
    }

    public function surveySubmit(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'survey_id' => ['required', 'exists:ngn_surveys,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'opt_in' => ['nullable', 'boolean'],
            'answers' => ['required', 'array'],
        ]);

        $response = NgnSurveyResponse::create([
            'survey_id' => $payload['survey_id'],
            'contact_name' => $payload['name'],
            'contact_email' => $payload['email'],
            'contact_phone' => $payload['phone'] ?? null,
            'is_contact_opt_in' => $payload['opt_in'] ?? false,
        ]);

        foreach ($payload['answers'] as $questionId => $answer) {
            NgnSurveyAnswer::create([
                'response_id' => $response->id,
                'question_id' => $questionId,
                'option_id' => $answer['option_id'] ?? null,
                'answer_text' => $answer['answer_text'] ?? null,
            ]);
        }

        return response()->json(['message' => 'Thank you for your submission.'], 201);
    }

    public function partnerSubscribe(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'companyname' => ['required', 'string', 'max:50', 'unique:ngn_partners,companyname'],
            'company_address' => ['nullable', 'string', 'max:255'],
            'company_number' => ['nullable', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:50'],
            'last_name' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'website' => ['nullable', 'string', 'max:255'],
            'fleet_size' => ['nullable', 'integer', 'min:0'],
            'operating_since' => ['nullable', 'string', 'max:8'],
            'tc_agreed' => ['required', 'accepted'],
        ]);

        unset($payload['tc_agreed']);
        $payload['is_approved'] = false;

        try {
            $partner = NgnPartner::create($payload);
            Mail::to('support@neguinhomotors.co.uk')->send(new NgnPartnerRegistrationMailer($payload));
        } catch (\Throwable $e) {
            Log::error('Mobile partner subscribe failed: '.$e->getMessage());

            return response()->json(['message' => 'There was an error processing your registration. Please try again.'], 500);
        }

        return response()->json([
            'message' => 'Thank you for registering. We will review your application and contact you soon.',
            'data' => ['partner_id' => $partner->id],
        ], 201);
    }

    public function accidentClaim(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'string', 'min:7', 'max:30'],
            'email' => ['required', 'email', 'max:200'],
            'reg_no' => ['required', 'string', 'max:20'],
            'language' => ['required', 'string', 'max:50'],
            'privacy_policy' => ['accepted'],
        ]);

        try {
            if (config('mail.mailers.smtp.host') && config('mail.from.address')) {
                Mail::send([], [], function ($message) use ($payload) {
                    $message->to(config('mail.from.address', 'info@neguinhomotors.co.uk'))
                        ->subject('Accident Management Claim – '.$payload['name'])
                        ->html(
                            '<h2>New Accident Management Claim</h2>'
                            .'<p><strong>Name:</strong> '.e($payload['name']).'</p>'
                            .'<p><strong>Phone:</strong> '.e($payload['phone']).'</p>'
                            .'<p><strong>Email:</strong> '.e($payload['email']).'</p>'
                            .'<p><strong>Reg / VRM:</strong> '.e($payload['reg_no']).'</p>'
                            .'<p><strong>Language:</strong> '.e($payload['language']).'</p>'
                        );
                });
            }
        } catch (\Throwable $e) {
            Log::warning('Mobile accident claim mail failed: '.$e->getMessage());
        }

        return response()->json(['message' => 'Claim submitted. Our team will be in touch shortly.'], 201);
    }
}
