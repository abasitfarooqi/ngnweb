<?php

namespace App\Http\Controllers\Api\Mobile\V2;

use App\Http\Controllers\Api\Mobile\V2\Concerns\RewritesApiPayload;
use Illuminate\Http\JsonResponse;

class MobileClubController extends \App\Http\Controllers\Api\Mobile\MobileClubController
{
    use RewritesApiPayload;

    public function content(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::content());
    }
}
