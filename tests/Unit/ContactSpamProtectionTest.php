<?php

namespace Tests\Unit;

use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use App\Services\ContactSpamProtection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ContactSpamProtectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config()->set('contact.captcha_enabled', false);
        config()->set('contact.rate_limit', ['ip_attempts' => 5, 'email_attempts' => 5, 'decay_seconds' => 600]);
        request()->server->set('REMOTE_ADDR', '198.51.100.'.random_int(1, 200));
    }

    public function test_captcha_off_still_accepts_a_valid_first_submission(): void
    {
        app(ContactSpamProtection::class)->check('', now()->timestamp - 3, [
            'email' => 'customer@example.com',
            'phone' => '07123456789',
            'topic' => 'Service',
            'message' => 'Please contact me about servicing.',
        ]);

        $this->assertTrue(true);
    }

    public function test_honeypot_and_duplicate_are_blocked(): void
    {
        $service = app(ContactSpamProtection::class);
        $data = ['email' => 'customer@example.com', 'message' => 'Please contact me about servicing.'];

        $this->expectException(ValidationException::class);
        $service->check('filled-by-bot', now()->timestamp - 3, $data);
    }

    public function test_duplicate_is_blocked_atomically_after_first_submission(): void
    {
        $service = app(ContactSpamProtection::class);
        $data = ['email' => 'customer@example.com', 'message' => 'Please contact me about servicing.'];

        $service->check('', now()->timestamp - 3, $data);

        $this->expectException(ValidationException::class);
        $service->check('', now()->timestamp - 3, $data);
    }

    public function test_ip_rate_limit_blocks_the_sixth_submission(): void
    {
        $service = app(ContactSpamProtection::class);
        for ($i = 0; $i < 5; $i++) {
            $service->check('', now()->timestamp - 3, [
                'email' => "customer{$i}@example.com",
                'message' => "Please contact me about servicing {$i}.",
            ]);
        }

        $this->expectException(ValidationException::class);
        $service->check('', now()->timestamp - 3, [
            'email' => 'customer-six@example.com',
            'message' => 'Please contact me about servicing six.',
        ]);
    }

    public function test_captcha_on_requires_server_verified_token(): void
    {
        config()->set('contact.captcha_enabled', true);
        NoCaptcha::shouldReceive('verifyResponse')->once()->with('valid-token', request()->ip())->andReturn(true);

        app(ContactSpamProtection::class)->check('', now()->timestamp - 3, [
            'email' => 'customer@example.com',
            'message' => 'Please contact me about servicing.',
        ], 'valid-token');

        $this->assertTrue(true);
    }

    public function test_captcha_on_rejects_missing_token(): void
    {
        config()->set('contact.captcha_enabled', true);

        $this->expectException(ValidationException::class);
        app(ContactSpamProtection::class)->check('', now()->timestamp - 3, [
            'email' => 'customer@example.com',
            'message' => 'Please contact me about servicing.',
        ]);
    }
}
