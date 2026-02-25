<?php

namespace App\Helpers;

use App\Models\Customer;
use App\Models\JudopayCitAccess;
use App\Models\JudopaySubscription;
use App\Models\Motorbike;
use App\Models\RentingBookingItem;
use Illuminate\Support\Facades\Log;

class JudopayAuthorizationHelper
{
    public static function generateAuthorizationLink(
        int $customerId,
        int $subscriptionId,
        int $expiresInHours = 24,
        array $adminFormData = null
    ): array {
        try {
            Log::channel('judopay')->info('Generating authorization access', [
                'customer_id' => $customerId,
                'subscription_id' => $subscriptionId,
                'expires_in_hours' => $expiresInHours,
            ]);

            // Validate subscription exists and get related data
            $subscription = JudopaySubscription::with('subscribable')->find($subscriptionId);
            if (! $subscription) {
                return [
                    'success' => false,
                    'url' => null,
                    'message' => 'Subscription not found',
                ];
            }

            // Validate customer exists
            $customer = Customer::find($customerId);
            if (! $customer) {
                return [
                    'success' => false,
                    'url' => null,
                    'message' => 'Customer not found',
                ];
            }

            // Validate subscription has subscribable relationship
            if (! $subscription->subscribable) {
                return [
                    'success' => false,
                    'url' => null,
                    'message' => 'Subscription has no associated service data',
                ];
            }

            // Create authorization access
            $access = JudopayCitAccess::createAccess($customerId, $subscriptionId, $expiresInHours, $adminFormData);

            $url = $access->getLink();

            Log::channel('judopay')->info('Authorization access created', [
                'access_id' => $access->id,
                'customer_id' => $customerId,
                'passcode' => $access->passcode,
                'expires_at' => $access->expires_at,
                'url' => $url,
            ]);

            return [
                'success' => true,
                'url' => $url,
                'message' => 'Authorization link generated successfully',
                'access' => $access,
            ];

        } catch (\Exception $e) {
            Log::channel('judopay')->error('Failed to generate authorization access', [
                'customer_id' => $customerId,
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'url' => null,
                'message' => 'Failed to generate authorization link: '.$e->getMessage(),
            ];
        }
    }

    public static function validateAccess(int $customerId, string $passcode, int $subscriptionId): array
    {
        try {
            $access = JudopayCitAccess::findValid($customerId, $passcode, $subscriptionId);

            if (! $access) {
                Log::channel('judopay')->warning('Invalid authorization access attempt', [
                    'customer_id' => $customerId,
                    'passcode' => $passcode,
                    'subscription_id' => $subscriptionId,
                ]);

                return [
                    'valid' => false,
                    'access' => null,
                    'message' => 'Unauthorized access or link has expired',
                ];
            }

            Log::channel('judopay')->info('Authorization access validated successfully', [
                'access_id' => $access->id,
                'customer_id' => $customerId,
                'subscription_id' => $subscriptionId,
            ]);

            return [
                'valid' => true,
                'access' => $access,
                'message' => 'Access validated successfully',
            ];

        } catch (\Exception $e) {
            Log::channel('judopay')->error('Access validation error', [
                'customer_id' => $customerId,
                'passcode' => $passcode,
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'valid' => false,
                'access' => null,
                'message' => 'Access validation failed: '.$e->getMessage(),
            ];
        }
    }

    public static function getAuthorizationData(JudopayCitAccess $access): array
    {
        try {
            $customer = $access->customer;
            $subscription = $access->subscription;

            if (! $customer || ! $subscription) {
                throw new \Exception('Missing customer or subscription data');
            }

            $serviceData = [];
            $motorbike = null;
            $userName = 'N/A';

            // Get service-specific data
            if ($subscription->subscribable_type === 'App\Models\RentingBooking') {
                $rentingBooking = $subscription->subscribable;
                $bookingItem = RentingBookingItem::where('booking_id', $rentingBooking->id)->first();
                $motorbike = $bookingItem ? Motorbike::find($bookingItem->motorbike_id) : null;
                $userName = $rentingBooking->user ?
                    $rentingBooking->user->first_name.' '.$rentingBooking->user->last_name : 'N/A';

                $serviceData = [
                    'type' => 'rental',
                    'booking' => $rentingBooking,
                    'booking_item' => $bookingItem,
                    'motorbike' => $motorbike,
                ];

            } elseif ($subscription->subscribable_type === 'App\Models\FinanceApplication') {
                $financeApplication = $subscription->subscribable;
                $applicationItem = $financeApplication->application_items->first();
                $motorbike = $applicationItem ? Motorbike::find($applicationItem->motorbike_id) : null;
                $userName = $financeApplication->user ?
                    $financeApplication->user->first_name.' '.$financeApplication->user->last_name : 'N/A';

                $serviceData = [
                    'type' => 'finance',
                    'finance_application' => $financeApplication,
                    'application_item' => $applicationItem,
                    'motorbike' => $motorbike,
                ];
            }

            return [
                'customer' => $customer,
                'service_data' => $serviceData,
                'user_name' => $userName,
                'motorbike' => $motorbike,
                'subscription' => $subscription,
            ];

        } catch (\Exception $e) {
            Log::channel('judopay')->error('Failed to get authorization data', [
                'access_id' => $access->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
