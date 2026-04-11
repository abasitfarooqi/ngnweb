<?php

namespace App\Http\Controllers\Api\Mobile\V2;

use App\Http\Controllers\Api\Mobile\V2\Concerns\RewritesApiPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobilePortalController extends \App\Http\Controllers\Api\Mobile\MobilePortalController
{
    use RewritesApiPayload;

    public function overview(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::overview($request));
    }

    public function fullState(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::fullState($request));
    }

    public function myOrders(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::myOrders($request));
    }

    public function myRentals(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::myRentals($request));
    }

    public function myMotBookings(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::myMotBookings($request));
    }

    public function myRecoveryRequests(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::myRecoveryRequests($request));
    }

    public function createMotBooking(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::createMotBooking($request));
    }

    public function rentalDetail(Request $request, int $id): JsonResponse
    {
        return $this->rewriteApiPayload(parent::rentalDetail($request, $id));
    }

    public function bookingsUnified(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::bookingsUnified($request));
    }
}
