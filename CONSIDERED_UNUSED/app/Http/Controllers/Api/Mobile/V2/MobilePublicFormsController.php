<?php

namespace App\Http\Controllers\Api\Mobile\V2;

use App\Http\Controllers\Api\Mobile\V2\Concerns\RewritesApiPayload;
use App\Services\DvlaVehicleEnquiryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobilePublicFormsController extends \App\Http\Controllers\Api\Mobile\MobilePublicFormsController
{
    use RewritesApiPayload;

    public function serviceContent(string $slug): JsonResponse
    {
        return $this->rewriteApiPayload(parent::serviceContent($slug));
    }

    public function motCheck(Request $request, DvlaVehicleEnquiryService $dvla): JsonResponse
    {
        return $this->rewriteApiPayload(parent::motCheck($request, $dvla));
    }

    public function motAlerts(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::motAlerts($request));
    }

    public function financeContent(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::financeContent());
    }

    public function financeCalculate(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::financeCalculate($request));
    }

    public function financeApply(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::financeApply($request));
    }

    public function contactCallback(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::contactCallback($request));
    }

    public function contactTradeAccount(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::contactTradeAccount($request));
    }

    public function contactServiceBooking(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::contactServiceBooking($request));
    }
}
