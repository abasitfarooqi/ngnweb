<?php

namespace App\Http\Controllers\Api\Mobile\V2;

use App\Http\Controllers\Api\Mobile\V2\Concerns\RewritesApiPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobilePortalExperienceController extends \App\Http\Controllers\Api\Mobile\MobilePortalExperienceController
{
    use RewritesApiPayload;

    public function pageBlueprint(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::pageBlueprint());
    }

    public function addresses(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::addresses($request));
    }

    public function addressCountries(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::addressCountries());
    }

    public function createAddress(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::createAddress($request));
    }

    public function updateAddress(Request $request, int $id): JsonResponse
    {
        return $this->rewriteApiPayload(parent::updateAddress($request, $id));
    }

    public function deleteAddress(Request $request, int $id): JsonResponse
    {
        return $this->rewriteApiPayload(parent::deleteAddress($request, $id));
    }

    public function setAddressDefault(Request $request, int $id): JsonResponse
    {
        return $this->rewriteApiPayload(parent::setAddressDefault($request, $id));
    }

    public function documents(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::documents($request));
    }

    public function uploadDocument(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::uploadDocument($request));
    }

    public function recurringPayments(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::recurringPayments($request));
    }

    public function rentalBrowseOptions(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::rentalBrowseOptions($request));
    }

    public function rentalAvailable(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::rentalAvailable($request));
    }

    public function rentalCreateBlueprint(Request $request, int $motorbikeId): JsonResponse
    {
        return $this->rewriteApiPayload(parent::rentalCreateBlueprint($request, $motorbikeId));
    }

    public function rentalCreateRequest(Request $request, int $motorbikeId): JsonResponse
    {
        return $this->rewriteApiPayload(parent::rentalCreateRequest($request, $motorbikeId));
    }

    public function repairsAppointmentOptions(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::repairsAppointmentOptions());
    }

    public function createRepairsAppointment(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::createRepairsAppointment($request));
    }

    public function recoveryOptions(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::recoveryOptions());
    }

    public function recoveryQuote(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::recoveryQuote($request));
    }

    public function createRecoveryRequest(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::createRecoveryRequest($request));
    }
}
