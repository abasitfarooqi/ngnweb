<?php

namespace App\Listeners;

use App\Events\SupportMessageSent;
use App\Models\Communication;
use App\Models\CommunicationRecipient;
use App\Services\Communications\CommunicationSchema;
use App\Services\ExpoPushNotificationService;
use Illuminate\Support\Str;
use Throwable;

class CreateSupportReplyCommunication
{
    public function handle(SupportMessageSent $event): void
    {
        $message = $event->message;
        $conversation = $message->conversation;
        $customer = $conversation?->customerAuth;

        if ($message->sender_type !== 'staff' || ! $conversation || $conversation->service_booking_id || ! $customer || ! app(CommunicationSchema::class)->ready()) {
            return;
        }

        try {
            $communication = Communication::query()->firstOrCreate(
                ['communication_key' => 'support.general.reply', 'correlation_id' => 'support-message-'.$message->id],
                [
                    'uuid' => (string) Str::uuid(),
                    'customer_id' => $customer->customer_id,
                    'customer_auth_id' => $customer->id,
                    'recipient_email' => $customer->email,
                    'title' => 'New support message',
                    'subject' => 'NGN Support replied to your message',
                    'preview' => trim((string) ($message->body ?: 'NGN Support sent an attachment.')),
                    'content_text' => trim((string) ($message->body ?: 'NGN Support sent an attachment.')),
                    'structured_content' => ['type' => 'support_message', 'conversation_uuid' => $conversation->uuid, 'message_id' => $message->id],
                    'payload_snapshot' => ['conversation_uuid' => $conversation->uuid, 'message_id' => $message->id],
                    'policy_snapshot' => ['internal_inbox_enabled' => true, 'mobile_push_enabled' => true, 'email_enabled' => false],
                    'source_type' => 'support_message',
                    'source_id' => $message->id,
                    'priority' => 'normal',
                    'category' => 'support',
                ],
            );

            CommunicationRecipient::query()->firstOrCreate(['communication_id' => $communication->id, 'customer_auth_id' => $customer->id]);
            app(ExpoPushNotificationService::class)->sendToCustomer($customer->id, (string) $communication->title, (string) $communication->preview, ['type' => 'chat', 'conversation_uuid' => $conversation->uuid, 'communication_uuid' => $communication->uuid]);
        } catch (Throwable) {
            // Chat delivery must not fail if optional communication/push storage is unavailable.
        }
    }
}
