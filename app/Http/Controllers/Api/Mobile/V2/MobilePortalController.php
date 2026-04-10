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
}
