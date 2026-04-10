<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MobileBootstrapController extends Controller
{
    public function systemMap(): JsonResponse
    {
        return response()->json([
            'project' => [
                'name' => 'NGN Motors mobile API surface',
                'api_prefix' => '/api/v1/mobile',
                'auth' => [
                    'customer_login' => '/api/v1/customer/login',
                    'staff_login' => '/api/v1/staff/login',
                    'customer_guard' => 'auth:customer,sanctum',
                    'staff_guard' => 'auth:sanctum',
                ],
            ],
            'navigation' => [
                'website' => [
                    ['key' => 'sales_hub', 'label' => 'Sales', 'uri' => '/motorcycle-sales'],
                    ['key' => 'used_bikes', 'label' => 'Used motorcycles', 'uri' => '/used-motorcycles'],
                    ['key' => 'new_bikes', 'label' => 'New motorcycles', 'uri' => '/motorcycle-sales'],
                    ['key' => 'finance', 'label' => 'Finance', 'uri' => '/finance'],
                    ['key' => 'services', 'label' => 'All services', 'uri' => '/all-services'],
                    ['key' => 'mot', 'label' => 'MOT', 'uri' => '/mot'],
                    ['key' => 'repairs', 'label' => 'Repairs', 'uri' => '/repairs'],
                    ['key' => 'delivery_recovery', 'label' => 'Recovery and delivery', 'uri' => '/motorcycle-delivery'],
                    ['key' => 'rentals', 'label' => 'Rentals', 'uri' => '/rentals'],
                    ['key' => 'shop', 'label' => 'Shop', 'uri' => '/shop'],
                    ['key' => 'spare_parts', 'label' => 'Spare parts', 'uri' => '/spareparts'],
                    ['key' => 'ebikes', 'label' => 'E-bikes', 'uri' => '/ebikes'],
                    ['key' => 'about', 'label' => 'About', 'uri' => '/about'],
                    ['key' => 'contact', 'label' => 'Contact', 'uri' => '/contact'],
                ],
                'portal' => [
                    ['key' => 'dashboard', 'label' => 'Dashboard', 'uri' => '/account'],
                    ['key' => 'profile', 'label' => 'Profile', 'uri' => '/account/profile'],
                    ['key' => 'documents', 'label' => 'Documents', 'uri' => '/account/documents'],
                    ['key' => 'repairs', 'label' => 'Repairs and MOT', 'uri' => '/account/repairs/request'],
                    ['key' => 'mot', 'label' => 'Book MOT', 'uri' => '/account/mot/book'],
                    ['key' => 'rentals', 'label' => 'Rentals', 'uri' => '/account/rentals'],
                    ['key' => 'finance', 'label' => 'Finance', 'uri' => '/account/finance/browse'],
                    ['key' => 'recovery', 'label' => 'Recovery', 'uri' => '/account/recovery/request'],
                    ['key' => 'orders', 'label' => 'My orders', 'uri' => '/account/orders'],
                    ['key' => 'enquiries', 'label' => 'My enquiries', 'uri' => '/account/enquiries'],
                    ['key' => 'support', 'label' => 'Conversations', 'uri' => '/account/support'],
                ],
            ],
            'database_relations' => [
                'customer_auths.customer_id -> customers.id',
                'service_bookings.customer_auth_id -> customer_auths.id',
                'service_bookings.conversation_id -> support_conversations.id',
                'support_conversations.customer_auth_id -> customer_auths.id',
                'support_conversations.service_booking_id -> service_bookings.id',
                'support_messages.conversation_id -> support_conversations.id',
                'support_attachments.message_id -> support_messages.id',
                'motorbikes_sale.motorbike_id -> motorbikes.id',
                'renting_booking_items.booking_id -> renting_bookings.id',
                'renting_booking_items.motorbike_id -> motorbikes.id',
                'ec_orders.customer_id -> customer_auths.id (model mapping)',
                'ec_order_items.order_id -> ec_orders.id',
                'sp_models.make_id -> sp_makes.id',
                'sp_fitments.model_id -> sp_models.id',
                'sp_assemblies.fitment_id -> sp_fitments.id',
                'sp_assembly_parts.assembly_id -> sp_assemblies.id',
                'sp_assembly_parts.part_id -> sp_parts.id',
            ],
            'existing_api_domains' => [
                'shop' => '/api/v1/shop/*',
                'customer_auth' => '/api/v1/customer/*',
                'customer_support' => '/api/v1/customer/support/*',
                'staff_support' => '/api/v1/staff/support/*',
                'manager_ops' => '/api/* (sanctum staff endpoints)',
                'mobile_content' => '/api/v1/mobile/content/*',
            ],
        ]);
    }

    public function formsBlueprint(): JsonResponse
    {
        return response()->json([
            'unified_enquiry' => [
                'endpoint' => '/api/v1/mobile/enquiries',
                'auth' => 'required',
                'method' => 'POST',
                'fields' => [
                    ['name' => 'module', 'required' => true, 'type' => 'enum', 'values' => ['bike_new', 'bike_used', 'ebike', 'rental', 'mot', 'repairs', 'service', 'recovery', 'finance', 'shop', 'spareparts', 'general']],
                    ['name' => 'subject', 'required' => false, 'type' => 'string', 'max' => 255],
                    ['name' => 'message', 'required' => true, 'type' => 'string', 'max' => 4000],
                    ['name' => 'phone', 'required' => true, 'type' => 'string', 'max' => 40],
                    ['name' => 'reg_no', 'required' => false, 'type' => 'string', 'max' => 40],
                    ['name' => 'reference_type', 'required' => false, 'type' => 'string', 'max' => 60],
                    ['name' => 'reference_id', 'required' => false, 'type' => 'integer'],
                    ['name' => 'booking_date', 'required' => false, 'type' => 'date'],
                    ['name' => 'booking_time', 'required' => false, 'type' => 'HH:MM'],
                    ['name' => 'metadata', 'required' => false, 'type' => 'object'],
                ],
                'writes_to' => [
                    'service_bookings',
                    'support_conversations',
                ],
            ],
            'chat' => [
                'customer_inbox' => '/api/v1/customer/support/conversations',
                'customer_send_message' => '/api/v1/customer/support/conversations/{uuid}/messages',
                'staff_inbox' => '/api/v1/staff/support/conversations',
                'staff_inbox_compact' => '/api/v1/staff/support/inbox',
                'staff_thread_compact' => '/api/v1/staff/support/inbox/{conversationId}',
                'staff_send_message' => '/api/v1/staff/support/conversations/{id}/messages',
                'staff_assignees' => '/api/v1/staff/support/assignees',
                'staff_profile' => '/api/v1/staff/me',
                'staff_logout' => '/api/v1/staff/logout',
                'realtime_channels' => [
                    'support.conversation.{uuid}',
                    'support.customer.{customerAuthId}',
                    'support.staff',
                ],
            ],
        ]);
    }
}
