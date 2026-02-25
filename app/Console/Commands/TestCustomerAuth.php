<?php

namespace App\Console\Commands;

use App\Http\Controllers\Auth\CustomerAuthController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class TestCustomerAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:customer-auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test customer registration and login functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Customer Auth Test...');

        // Test data
        $customerData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@test.com',
            'password' => 'password123',
            'phone' => '+1234567890',
            'dob' => '1990-01-01',
            'address' => '123 Test Street',
            'postcode' => '12345',
            'emergency_contact' => '+1987654321',
            'whatsapp' => '+1234567890',
            'city' => 'Test City',
            'country' => 'Test Country',
            'nationality' => 'Test Nationality',
            'reputation_note' => 'Test customer',
            'rating' => 5,
            'license_number' => 'LIC123456',
            'license_expiry_date' => '2025-01-01',
            'license_issuance_authority' => 'Test Authority',
            'license_issuance_date' => '2020-01-01',
        ];

        // Test Registration
        $this->info('Testing Registration...');
        $authController = new CustomerAuthController;

        try {
            $customerAuth = $authController->register($customerData);
            $this->info('Registration successful!');
            $this->info("CustomerAuth ID: {$customerAuth->id}");
            $this->info("Customer ID: {$customerAuth->customer_id}");
        } catch (\Exception $e) {
            $this->error('Registration failed: '.$e->getMessage());

            return 1;
        }

        // Test Login
        $this->info('\nTesting Login...');
        $loginCredentials = [
            'email' => $customerData['email'],
            'password' => $customerData['password'],
        ];

        try {
            $loggedInUser = $authController->login($loginCredentials);
            if ($loggedInUser) {
                $this->info('Login successful!');
                $this->info("Logged in user ID: {$loggedInUser->id}");

                // Test getting authenticated user
                $authUser = Auth::guard('customer')->user();
                $this->info("Authenticated user email: {$authUser->email}");

                // Test logout
                $this->info('\nTesting Logout...');
                $authController->logout();
                $this->info('Logout successful!');
            } else {
                $this->error('Login failed!');

                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Login test failed: '.$e->getMessage());

            return 1;
        }

        $this->info('\nAll tests completed successfully!');

        return 0;
    }
}
