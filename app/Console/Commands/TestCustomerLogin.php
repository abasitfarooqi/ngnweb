<?php

namespace App\Console\Commands;

use App\Http\Controllers\Auth\CustomerAuthController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class TestCustomerLogin extends Command
{
    protected $signature = 'test:customer-login {email} {password}';

    protected $description = 'Test customer login with provided credentials';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $this->info('Testing Customer Login...');
        $this->info("Email: {$email}");

        $authController = new CustomerAuthController;

        try {
            $loginCredentials = [
                'email' => $email,
                'password' => $password,
            ];

            $loggedInUser = $authController->login($loginCredentials);

            if ($loggedInUser) {
                $this->info('Login successful!');
                $this->info("Logged in user ID: {$loggedInUser->id}");

                // Test getting authenticated user
                $authUser = Auth::guard('customer')->user();
                $this->info("Authenticated user email: {$authUser->email}");

                // Get associated customer details
                $customer = $authUser->customer;
                if ($customer) {
                    $this->info("Customer Name: {$customer->first_name} {$customer->last_name}");
                    $this->info("Customer Phone: {$customer->phone}");
                }

                // Test logout
                $this->info('\nTesting Logout...');
                $authController->logout();
                $this->info('Logout successful!');

                return 0;
            } else {
                $this->error('Login failed: Invalid credentials');

                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Login test failed: '.$e->getMessage());

            return 1;
        }
    }
}
