<?php

namespace Tests\Browser;

use App\Models\Contact;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ContactFormTest extends DuskTestCase
{
    /**
     * Clean up the testing environment before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Clear rate limits and cache before each test
        RateLimiter::clear('store.message');
        Cache::flush();

        // Clean up any test data from previous runs
        Contact::where('email', 'like', '%@example.com')->delete();

        // Clear any existing data
        Cache::flush();
        RateLimiter::clear('store.message');
        Mail::fake();
    }

    /**
     * Clean up after each test.
     */
    protected function tearDown(): void
    {
        // Clear rate limits and cache after each test
        RateLimiter::clear('store.message');
        Cache::flush();

        // Clean up test data
        Contact::where('email', 'like', '%@example.com')->delete();

        parent::tearDown();
    }

    /**
     * Test contact page loads correctly
     */
    public function test_contact_page_loads_correctly(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                ->assertSee('Email Us')
                ->assertPresent('form')
                ->assertPresent('input[name="name"]')
                ->assertPresent('input[name="email"]')
                ->assertPresent('input[name="phone"]')
                ->assertPresent('textarea[name="message"]');
        });
    }

    /**
     * Test successful contact form submission
     */
    public function test_successful_contact_form_submission(): void
    {
        $testEmail = 'john@example.com';

        $this->browse(function (Browser $browser) use ($testEmail) {
            try {
                $browser->visit('/contact')

                    ->type('name', 'John Doe')
                    ->type('email', $testEmail)
                    ->type('phone', '1234567890')
                    ->type('subject', 'Test Subject')
                    ->type('message', 'This is a test message')
                    ->press('SEND')
                    ->waitForLocation('/thank-you')
                    ->assertPathIs('/thank-you')

                    ->assertSee('Thank you for contacting Neguinho Motors. We have sent you an email.');

            } catch (\Exception $e) {
                // Log current URL and page content for debugging
                \Log::info('Current URL: '.$browser->driver->getCurrentURL());
                \Log::info('Page Source: '.$browser->driver->getPageSource());

                $this->fail('Test failed with exception: '.$e->getMessage());
            }
        });
    }

    /**
     * Test form validation errors
     */
    public function test_contact_form_validation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')

                ->press('SEND')
                ->waitFor('.text-danger')
                ->assertSee('The name field is required')
                ->assertSee('The email field is required')
                ->assertSee('The phone field is required')
                ->assertSee('The message field is required')

                // Test invalid email format
                ->type('email', 'invalid-email')

                ->press('SEND')
                ->waitFor('.text-danger')
                ->assertSee('The email field must be a valid email address');
        });
    }

    /**
     * Test form XSS protection
     */
    public function test_contact_form_xss_protection(): void
    {
        $this->browse(function (Browser $browser) {
            $maliciousScript = '<script>alert("XSS")</script>';

            $browser->visit('/contact')

                ->type('name', 'John Doe')
                ->type('email', 'john@example.com')
                ->type('phone', '1234567890')
                ->type('subject', $maliciousScript)
                ->type('message', $maliciousScript)
                ->press('SEND')
                ->waitForLocation('/thank-you')
                ->assertPathIs('/thank-you')
                ->assertDontSee($maliciousScript)

                ->assertSee('Thank you for contacting Neguinho Motors. We have sent you an email.');

        });
    }

    /**
     * Test form throttling/rate limiting
     */
    public function test_contact_form_rate_limiting()
    {
        $this->browse(function (Browser $browser) {
            // Clear any existing rate limits
            RateLimiter::clear('store.message');
            Cache::flush();

            // Try 4 submissions (3 should succeed, 4th should fail)
            for ($i = 1; $i <= 4; $i++) {
                $browser->visit('/contact')
                    ->type('name', 'Test User')
                    ->type('email', 'test@example.com')
                    ->type('phone', '1234567890')
                    ->type('reg_no', 'ABC123')
                    ->type('subject', 'Test Subject')
                    ->type('message', 'Test message')
                    ->press('SEND');

                if ($i <= 3) {
                    // First 3 attempts should succeed
                    $browser->assertPathIs('/thank-you')
                        ->assertSee('Thank you for contacting NGN')
                        ->pause(1000);
                } else {

                    // 4th attempt should fail with rate limit error
                    $browser->pause(1000)
                        ->assertSourceHas('429')
                        ->assertSourceHas('Too Many Requests');
                }
            }
        });
    }

    /**
     * Test form with maximum length inputs
     */
    public function test_contact_form_max_length_validation(): void
    {
        $this->browse(function (Browser $browser) {
            $longString = str_repeat('a', 256); // Create a string longer than 255 characters

            $browser->visit('/contact')

                ->type('name', $longString)
                ->type('email', 'john@example.com')
                ->type('phone', '1234567890')
                ->type('subject', $longString)
                ->type('message', $longString)
                ->press('SEND')

                ->waitFor('.text-danger')
                ->assertSee('The name field must not be greater than 255 characters')
                ->assertSee('The subject field must not be greater than 255 characters');

        });
    }

    /**
     * Test form accessibility
     */
    public function test_contact_form_accessibility(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                ->waitUntilMissing('#loading-overlay')
                ->assertPresent('input[name="name"]')
                ->assertPresent('input[name="email"]')
                ->assertPresent('input[name="phone"]')
                ->assertPresent('textarea[name="message"]')
                    // Verify ARIA labels and form structure
                ->assertPresent('input[aria-required="true"]')
                ->assertPresent('label[for="name"]')
                ->assertPresent('label[for="email"]')
                ->assertPresent('label[for="phone"]')
                ->assertPresent('label[for="message"]');
        });
    }

    /**
     * Helper method to wait for loading overlay to disappear
     */
    protected function waitForLoadingOverlay(Browser $browser)
    {
        return $browser->pause(5000)
            ->waitUntilMissing('#loading-overlay', 50)
            ->pause(5000);
    }

    /**
     * Helper method to fill and submit form
     */
    protected function fillAndSubmitForm(Browser $browser, array $data = [])
    {
        $this->waitForLoadingOverlay($browser);

        $browser->type('name', $data['name'] ?? 'Test User')
            ->type('email', $data['email'] ?? 'test@example.com')
            ->type('phone', $data['phone'] ?? '1234567890')
            ->type('message', $data['message'] ?? 'Test message');

        $this->waitForLoadingOverlay($browser);

        return $browser->press('SEND');
    }

    /**
     * Test phone number format validation
     */
    public function test_phone_number_format_validation()
    {
        $this->browse(function (Browser $browser) {
            $invalidPhones = ['abc123', '123'];

            foreach ($invalidPhones as $phone) {
                $browser->visit('/contact')
                    ->waitFor('#contactform', 30);

                $this->waitForLoadingOverlay($browser);

                $browser->type('name', 'Test User')
                    ->type('email', 'test@example.com')
                    ->type('phone', $phone)
                    ->type('message', 'Test message')
                    ->press('SEND')
                    ->pause(4000);  // Wait for validation

                // Take screenshot for debugging
                $browser->screenshot('validation-check-'.str_replace(['@', '.'], '-', $phone));

                // Check for validation error
                $browser->assertSeeIn('.error-message, .text-danger', 'The phone field format is invalid')
                    ->assertDontSee('Thank you for contacting NGN');
            }
        });
    }

    /**
     * Test email functionality
     */
    public function test_contact_form_sends_email()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                ->waitFor('#contactform', 30);

            $this->waitForLoadingOverlay($browser);

            $browser->type('name', 'Test User')
                ->type('email', 'test@example.com')
                ->type('phone', '1234567890')
                ->type('message', 'Test message')
                ->press('SEND')
                ->pause(3000)  // Wait for form submission
                ->assertSee('Thank you for contacting NGN');
        });
    }

    /**
     * Test CSRF protection
     */
    public function test_csrf_protection()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact');

            $this->waitForLoadingOverlay($browser);

            $browser->script("document.querySelector('input[name=\"_token\"]')?.remove()");

            $this->fillAndSubmitForm($browser);

            $browser->assertSee('419')
                ->assertSee('Page Expired');
        });
    }

    /**
     * Test special characters
     */
    public function test_special_characters_submission()
    {
        $this->browse(function (Browser $browser) {
            $specialChars = 'Test @ # $ % & * ( ) + = ; : " \' < > , . ? / ';

            $browser->visit('/contact');

            $this->fillAndSubmitForm($browser, [
                'name' => $specialChars,
                'message' => $specialChars,
            ]);

            $browser->waitForLocation('/thank-you')
                ->assertPathIs('/thank-you');

            $this->assertDatabaseHas('contacts', [
                'name' => $specialChars,
                'message' => $specialChars,
            ]);
        });
    }

    /**
     * Helper method to wait for page load
     */
    protected function waitForPageLoad(Browser $browser)
    {
        return $browser->waitUntilMissing('.loading-indicator')  // If you have a loading indicator
            ->pause(500);  // Small additional wait to ensure JS is loaded
    }

    /**
     * Helper method to fill contact form
     */
    protected function fillContactForm(Browser $browser, array $data = [])
    {
        return $browser->waitFor('#contactform')
            ->type('name', $data['name'] ?? 'Test User')
            ->type('email', $data['email'] ?? 'test@example.com')
            ->type('phone', $data['phone'] ?? '1234567890')
            ->type('message', $data['message'] ?? 'Test message');
    }

    /**
     * Take screenshot on failure
     */
    protected function onFailure($e)
    {
        if ($this->browser) {
            $this->browser->screenshot('failure-'.time());

        }
        parent::onFailure($e);
    }
}
