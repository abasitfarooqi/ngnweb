<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);
        Subscriber::query()->firstOrCreate(['email' => strtolower($validated['email'])]);

        return redirect()->back()->with('success', 'Thanks for subscribing');
    }

    public function subscribe(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subscribe-email' => ['required', 'email', 'max:255'],
        ]);

        Subscriber::query()->firstOrCreate(
            ['email' => strtolower($validated['subscribe-email'])]
        );

        return redirect()->back()->with('newsletter_ok', 'Thanks for subscribing');
    }
}
