<?php

namespace App\Http\Controllers\Api\Mobile\V2;

use App\Http\Controllers\Api\Mobile\V2\Concerns\RewritesApiPayload;
use App\Services\Club\ClubReferralSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileClubController extends \App\Http\Controllers\Api\Mobile\MobileClubController
{
    use RewritesApiPayload;

    public function content(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::content());
    }

    public function register(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::register($request));
    }

    public function login(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::login($request));
    }

    public function dashboard(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::dashboard($request));
    }

    public function referral(Request $request, ClubReferralSubmissionService $service): JsonResponse
    {
        return $this->rewriteApiPayload(parent::referral($request, $service));
    }
}
