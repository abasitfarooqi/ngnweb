<?php

namespace App\Http\Controllers\FluxAdmin;

use App\Http\Controllers\Controller;
use App\Support\FluxAdminUnreadBadges;
use Illuminate\Http\JsonResponse;

class UnreadBadgeController extends Controller
{
    public function __invoke(): JsonResponse
    {
        if (session()->isStarted()) {
            session()->save();
        }

        return response()->json(FluxAdminUnreadBadges::counts());
    }
}
