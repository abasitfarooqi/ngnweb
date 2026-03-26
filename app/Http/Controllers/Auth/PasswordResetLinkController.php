<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $primaryBroker = config('fortify.passwords', 'users');
        $status = Password::broker($primaryBroker)->sendResetLink($request->only('email'));

        if ($status === Password::INVALID_USER && $primaryBroker !== 'users') {
            $status = Password::broker('users')->sendResetLink($request->only('email'));
        }

        if ($status === Password::INVALID_USER) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => "We can't find a user with that email address."]);
        }

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}
