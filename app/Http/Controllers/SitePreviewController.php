<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SitePreviewController extends Controller
{
    public function unlock(Request $request, string $token): RedirectResponse
    {
        $secret = (string) config('launch.preview_secret', '');

        if ($secret === '' || ! hash_equals($secret, $token)) {
            abort(404);
        }

        Cookie::queue(
            (string) config('launch.preview_cookie', 'ngn_launch_preview'),
            hash_hmac('sha256', $secret, (string) config('app.key')),
            max(1, (int) config('launch.preview_cookie_days', 30)) * 24 * 60,
            '/',
            null,
            $request->isSecure(),
            true,
            false,
            'lax'
        );

        return redirect('/')->with('status', 'Preview access enabled on this browser.');
    }
}
