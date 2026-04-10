<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class MobileContentController extends Controller
{
    public function websiteNavigation(): JsonResponse
    {
        return response()->json([
            'top_navigation' => [
                [
                    'key' => 'sales',
                    'label' => 'Sales',
                    'route' => '/motorcycle-sales',
                    'children' => [
                        ['key' => 'new_motorcycles', 'label' => 'New motorcycles', 'route' => '/motorcycles/new'],
                        ['key' => 'used_motorcycles', 'label' => 'Used motorcycles', 'route' => '/motorcycles/used'],
                        ['key' => 'finance', 'label' => 'Finance', 'route' => '/finance'],
                        ['key' => 'accident_management', 'label' => 'Accident management', 'route' => '/accident-management'],
                    ],
                ],
                [
                    'key' => 'services',
                    'label' => 'Services',
                    'route' => '/all-services',
                    'children' => [
                        ['key' => 'all_services', 'label' => 'All services', 'route' => '/all-services'],
                        ['key' => 'mot_testing', 'label' => 'MOT testing', 'route' => '/mot'],
                        ['key' => 'repairs_servicing', 'label' => 'Repairs and servicing', 'route' => '/repairs'],
                        ['key' => 'recovery_delivery', 'label' => 'Recovery and delivery', 'route' => '/motorcycle-delivery'],
                        ['key' => 'accident_management', 'label' => 'Accident management', 'route' => '/accident-management'],
                    ],
                ],
                [
                    'key' => 'shop',
                    'label' => 'Shop',
                    'route' => '/shop',
                    'children' => [
                        ['key' => 'all_shop', 'label' => 'All shop', 'route' => '/shop'],
                        ['key' => 'accessories', 'label' => 'Accessories', 'route' => '/shop/accessories'],
                        ['key' => 'gps_trackers', 'label' => 'GPS trackers', 'route' => '/shop/gps-tracker'],
                        ['key' => 'helmets', 'label' => 'Helmets', 'route' => '/shop/helmets'],
                        ['key' => 'ebikes', 'label' => 'E-bikes', 'route' => '/ebikes'],
                    ],
                ],
                ['key' => 'spareparts', 'label' => 'Spareparts', 'route' => '/spareparts'],
                ['key' => 'about', 'label' => 'About', 'route' => '/about'],
                ['key' => 'contact', 'label' => 'Contact', 'route' => '/contact'],
            ],
            'mobile_bottom_navigation_recommended' => [
                ['key' => 'home', 'label' => 'Home'],
                ['key' => 'explore', 'label' => 'Explore'],
                ['key' => 'chat', 'label' => 'Chat'],
                ['key' => 'cart', 'label' => 'Cart'],
                ['key' => 'account', 'label' => 'Account'],
            ],
        ]);
    }

    public function portalNavigation(): JsonResponse
    {
        return response()->json([
            'portal_navigation' => [
                ['key' => 'dashboard', 'label' => 'Dashboard', 'route' => '/account'],
                ['key' => 'profile', 'label' => 'Profile', 'route' => '/account/profile'],
                ['key' => 'documents', 'label' => 'Documents', 'route' => '/account/documents'],
                [
                    'key' => 'repairs_mot',
                    'label' => 'Repairs and MOT',
                    'children' => [
                        ['key' => 'my_bookings', 'label' => 'My bookings', 'route' => '/account/bookings'],
                        ['key' => 'book_mot', 'label' => 'Book MOT', 'route' => '/account/mot/book'],
                        ['key' => 'repair_enquiry', 'label' => 'Repair enquiry', 'route' => '/account/repairs/request'],
                        ['key' => 'repairs_appointment', 'label' => 'Repairs appointment', 'route' => '/account/repairs/appointment'],
                    ],
                ],
                [
                    'key' => 'rentals',
                    'label' => 'Rentals',
                    'children' => [
                        ['key' => 'rentals_booking', 'label' => 'Rentals booking', 'route' => '/account/rentals'],
                        ['key' => 'rental_enquiries', 'label' => 'Rental enquiries', 'route' => '/account/rentals/my-enquiries'],
                        ['key' => 'my_rentals', 'label' => 'My rentals', 'route' => '/account/rentals/my-rentals'],
                    ],
                ],
                [
                    'key' => 'finance',
                    'label' => 'Finance',
                    'children' => [
                        ['key' => 'browse_finance', 'label' => 'Browse new and used bikes', 'route' => '/account/finance/browse'],
                        ['key' => 'my_finance_applications', 'label' => 'My finance applications', 'route' => '/account/finance/my-applications'],
                    ],
                ],
                [
                    'key' => 'recovery',
                    'label' => 'Recovery',
                    'children' => [
                        ['key' => 'request_recovery', 'label' => 'Request recovery', 'route' => '/account/recovery/request'],
                        ['key' => 'my_recovery_requests', 'label' => 'My requests', 'route' => '/account/recovery/my-requests'],
                    ],
                ],
                ['key' => 'orders', 'label' => 'My orders', 'route' => '/account/orders'],
                ['key' => 'enquiries', 'label' => 'My enquiries', 'route' => '/account/enquiries'],
                ['key' => 'conversations', 'label' => 'Conversations', 'route' => '/account/support'],
                ['key' => 'addresses', 'label' => 'Addresses', 'route' => '/account/addresses'],
                ['key' => 'payment_methods', 'label' => 'Payment methods', 'route' => '/account/payment-methods'],
                ['key' => 'recurring_payments', 'label' => 'Recurring payments', 'route' => '/account/payments/recurring'],
                ['key' => 'ngn_club', 'label' => 'NGN club', 'route' => '/account/club'],
                ['key' => 'security', 'label' => 'Security', 'route' => '/account/security'],
            ],
        ]);
    }

    public function homeBlocks(): JsonResponse
    {
        return response()->json([
            'hero' => [
                'title' => 'Motorcycles. Rentals, MOT, Repairs and sales.',
                'subtitle' => 'Serving Catford, Tooting and Sutton.',
                'actions' => [
                    ['label' => 'Book now', 'target' => 'modal:quick-book'],
                    ['label' => 'Find your branch', 'target' => '#locations'],
                ],
            ],
            'quick_links' => [
                ['key' => 'motorcycles_for_sale', 'label' => 'Motorcycles for sale', 'route' => '/bikes'],
                ['key' => 'spare_parts', 'label' => 'Spare parts', 'route' => '/shop'],
                ['key' => 'all_services', 'label' => 'All services', 'route' => '/repairs'],
                ['key' => 'finance', 'label' => 'Finance', 'route' => '/finance'],
            ],
            'sections' => [
                ['key' => 'ebikes', 'route' => '/ebikes'],
                ['key' => 'rentals', 'route' => '/rentals'],
                ['key' => 'used_motorcycles', 'route' => '/used-motorcycles'],
                ['key' => 'finance_strip', 'route' => '/finance'],
                ['key' => 'blog', 'route' => '/shop/blog'],
                ['key' => 'contact', 'route' => '/contact'],
                ['key' => 'about', 'route' => '/about'],
                ['key' => 'locations', 'route' => '/locations'],
                ['key' => 'newsletter_social', 'route' => '/shop/blog'],
            ],
        ]);
    }

    public function serviceModules(): JsonResponse
    {
        return response()->json([
            'modules' => [
                [
                    'key' => 'mot',
                    'label' => 'MOT',
                    'pages' => ['/mot', '/mot/book'],
                    'enquiry_route' => '/contact/service-booking',
                    'portal_routes' => ['/account/mot/book', '/account/mot/my-bookings'],
                ],
                [
                    'key' => 'repairs',
                    'label' => 'Repairs',
                    'pages' => [
                        '/repairs',
                        '/motorbike-basic-service-london',
                        '/motorbike-full-service-london',
                        '/motorbike-repair-services',
                        '/motorbike-service-comparison',
                    ],
                    'enquiry_route' => '/contact/service-booking',
                    'portal_routes' => ['/account/repairs/request', '/account/repairs/appointment'],
                ],
                [
                    'key' => 'rentals',
                    'label' => 'Rentals',
                    'pages' => ['/rentals'],
                    'enquiry_route' => '/contact/service-booking',
                    'portal_routes' => ['/account/rentals', '/account/rentals/my-enquiries', '/account/rentals/my-rentals'],
                ],
                [
                    'key' => 'finance',
                    'label' => 'Finance',
                    'pages' => ['/finance'],
                    'enquiry_route' => '/account/finance/browse#finance-enquiry',
                    'portal_routes' => ['/account/finance/browse', '/account/finance/my-applications'],
                ],
                [
                    'key' => 'recovery_delivery',
                    'label' => 'Recovery and delivery',
                    'pages' => ['/motorcycle-delivery', '/recovery'],
                    'enquiry_route' => '/contact/service-booking',
                    'portal_routes' => ['/account/recovery/request', '/account/recovery/my-requests'],
                ],
            ],
        ]);
    }

    public function viewFolderApiDbReport(): JsonResponse
    {
        $reportPath = base_path('MOBILE_V2_VIEW_FOLDER_API_DB_REPORT.txt');
        $reportText = File::exists($reportPath) ? File::get($reportPath) : '';

        return response()->json([
            'generated_file' => 'MOBILE_V2_VIEW_FOLDER_API_DB_REPORT.txt',
            'available' => $reportText !== '',
            'report_text' => $reportText,
        ]);
    }
}
