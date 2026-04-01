<?php

namespace App\Livewire\Site\Club;

use App\Services\Club\ClubPasskeyResetService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ForgotPasskey extends Component
{
    public string $identifier = '';

    public string $verification_code = '';

    public bool $codeSent = false;

    /** UUID from ?t= — allows reset without the same browser session (e.g. email on another device). */
    public string $continueToken = '';

    public function mount(): void
    {
        $service = app(ClubPasskeyResetService::class);

        $token = request()->query('t') ?? request()->query('token');
        if (is_string($token) && trim($token) !== '' && $service->peekResetToken(trim($token)) !== null) {
            $this->continueToken = trim($token);
            $this->codeSent = true;
        }

        $v = request()->query('verification_code') ?? request()->query('code');
        if (is_string($v) && $v !== '') {
            $digits = preg_replace('/\D/', '', $v);
            if (strlen($digits) === 6) {
                $this->verification_code = $digits;
            }
        }

        if (! $this->codeSent && $this->hasValidForgotSession()) {
            $this->codeSent = true;
        }

        if ($this->verification_code !== '' && strlen($this->verification_code) === 6 && ! $this->codeSent) {
            session()->flash(
                'error',
                'That address is incomplete. Open the full link from your SMS or email (it includes a secure token), or request a new code below.'
            );
        }
    }

    protected function hasValidForgotSession(): bool
    {
        if (! session()->has('verification_code') || ! session()->has('verification_phone') || ! session()->has('verification_code_expires_at')) {
            return false;
        }

        try {
            return ! Carbon::now()->greaterThan(session('verification_code_expires_at'));
        } catch (\Throwable) {
            return false;
        }
    }

    public function sendCode(ClubPasskeyResetService $service): void
    {
        $this->validate([
            'identifier' => ['required', 'string', 'min:3', 'max:191'],
        ]);

        $result = $service->sendVerificationCodeForIdentifier($this->identifier);

        if (! $result['success']) {
            $this->addError('identifier', $result['message']);

            return;
        }

        $this->continueToken = '';
        $this->codeSent = true;
        $this->resetErrorBag();
        session()->flash('success', $result['message']);
    }

    public function resetPasskey(ClubPasskeyResetService $service): void
    {
        $this->verification_code = preg_replace('/\D/', '', (string) $this->verification_code);

        $this->validate([
            'verification_code' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
        ]);

        $token = $this->continueToken !== '' ? $this->continueToken : null;
        $result = $service->resetPasskeyWithCode($this->verification_code, null, $token);

        if (! $result['success']) {
            $this->addError('verification_code', $result['message']);

            return;
        }

        $this->continueToken = '';
        session()->flash('success', $result['message']);
        $phone = $result['phone'] ?? null;
        if (is_string($phone) && $phone !== '') {
            $this->redirect(route('ngnclub.login', ['phone' => $phone]), navigate: false);

            return;
        }

        $this->redirectRoute('ngnclub.login', navigate: false);
    }

    public function startOver(ClubPasskeyResetService $service): void
    {
        if ($this->continueToken !== '') {
            Cache::forget($service->cacheKeyForToken($this->continueToken));
        }
        session()->forget([
            'verification_code',
            'verification_code_expires_at',
            'verification_phone',
            'forgot_delivery_channel',
        ]);
        $this->continueToken = '';
        $this->codeSent = false;
        $this->verification_code = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.site.club.forgot-passkey')
            ->layout('components.layouts.public', [
                'title' => 'Forgot NGN Club passkey | NGN Motors',
            ]);
    }
}
