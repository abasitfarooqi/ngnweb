<?php

namespace App\Http\Controllers\Api\Mobile\V2;

use App\Http\Controllers\Api\Mobile\V2\Concerns\RewritesApiPayload;
use Illuminate\Http\JsonResponse;

class MobileExperienceController extends \App\Http\Controllers\Api\Mobile\MobileExperienceController
{
    use RewritesApiPayload;

    public function pageManifest(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::pageManifest());
    }

    public function experienceBlueprint(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::experienceBlueprint());
    }

    public function fullAppMap(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::fullAppMap());
    }

    public function siteFullState(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::siteFullState());
    }

    public function dbLinkMap(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::dbLinkMap());
    }

    public function authBlueprint(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::authBlueprint());
    }

    public function frontendParityMap(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::frontendParityMap());
    }

    public function presentationViews(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::presentationViews());
    }

    public function presentationViewPayload(string $segment, string $path): JsonResponse
    {
        return $this->rewriteApiPayload(parent::presentationViewPayload($segment, $path));
    }
}
