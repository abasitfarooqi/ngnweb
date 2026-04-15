<?php

namespace App\Http\Controllers\Api\Mobile\V2;

use App\Http\Controllers\Api\Mobile\V2\Concerns\RewritesApiPayload;
use Illuminate\Http\JsonResponse;

class MobileBootstrapController extends \App\Http\Controllers\Api\Mobile\MobileBootstrapController
{
    use RewritesApiPayload;

    public function systemMap(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::systemMap());
    }

    public function formsBlueprint(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::formsBlueprint());
    }
}
