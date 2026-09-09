<?php

namespace App\Livewire\Concerns;

use App\Services\ContactSpamProtection;
use Illuminate\Validation\ValidationException;

trait HasContactSpamProtection
{
    public string $companyWebsite = '';
    public string $captchaToken = '';
    public int $formStartedAt = 0;

    protected function startContactSpamProtection(): void
    {
        $this->formStartedAt = now()->timestamp;
    }

    protected function protectContactSubmission(array $validated): void
    {
        try {
            app(ContactSpamProtection::class)->check(
                $this->companyWebsite,
                $this->formStartedAt,
                $validated,
                $this->captchaToken,
            );
        } catch (ValidationException $exception) {
            $this->captchaToken = '';
            $this->dispatch('contact-captcha-reset');
            throw $exception;
        }
    }

    protected function resetContactSpamProtection(): void
    {
        $this->companyWebsite = '';
        $this->captchaToken = '';
        $this->formStartedAt = now()->timestamp;
    }
}
