<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\Communications\CustomerNotificationMenu;
use Illuminate\Http\JsonResponse;

class NotificationLiveController extends Controller
{
    public function __invoke(CustomerNotificationMenu $menu): JsonResponse
    {
        return response()->json($menu->livePayload());
    }
}
