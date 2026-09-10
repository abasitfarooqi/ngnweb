<?php

use App\Mail\PortalCredentialsMail;
use App\Mail\PortalPasswordResetMail;
use App\Mail\PortalEmailVerificationMail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('communication_definitions') || ! Schema::hasTable('communication_policies')) {
            return;
        }

        foreach ([
            [
                'key' => 'portal.password.reset',
                'name' => 'Portal Password Reset',
                'description' => 'Secure password reset link sent to a customer portal user.',
                'class' => PortalPasswordResetMail::class,
            ],
            [
                'key' => 'portal.credentials.issued',
                'name' => 'Portal Credentials Issued',
                'description' => 'Portal login credentials sent after access is created or reset.',
                'class' => PortalCredentialsMail::class,
            ],
            [
                'key' => 'portal.email.verification',
                'name' => 'Portal Email Verification',
                'description' => 'Email verification link sent to a customer portal user.',
                'class' => PortalEmailVerificationMail::class,
            ],
        ] as $definition) {
            $id = DB::table('communication_definitions')->where('key', $definition['key'])->value('id');

            if (! $id) {
                $id = DB::table('communication_definitions')->insertGetId([
                    'key' => $definition['key'],
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'classification' => 'transactional',
                    'category' => 'portal',
                    'priority' => 'important',
                    'source_class' => $definition['class'],
                    'source_trigger' => 'Flux Admin portal access controls',
                    'email_class' => $definition['class'],
                    'template_view' => match ($definition['key']) {
                        'portal.password.reset' => 'emails.portal.password-reset',
                        'portal.email.verification' => 'emails.portal.email-verification',
                        default => 'emails.portal.credentials',
                    },
                    'recipient_summary' => 'Customer portal email',
                    'supported_channels' => json_encode(['email', 'internal_inbox']),
                    'variables' => json_encode(['customer', 'reset_link', 'credentials']),
                    'metadata' => json_encode(['security_note' => 'Password values are only present in the outbound credential email.']),
                    'existing_email_default' => true,
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('communication_policies')->updateOrInsert(
                ['communication_definition_id' => $id],
                [
                    'email_enabled' => true,
                    'internal_inbox_enabled' => false,
                    'web_push_enabled' => false,
                    'mobile_push_enabled' => false,
                    'reply_allowed' => false,
                    'enquiry_allowed' => false,
                    'mandatory' => false,
                    'priority' => 'important',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('communication_definitions')) {
            return;
        }

        $ids = DB::table('communication_definitions')
            ->whereIn('key', ['portal.password.reset', 'portal.credentials.issued', 'portal.email.verification'])
            ->pluck('id');

        DB::table('communication_policies')->whereIn('communication_definition_id', $ids)->delete();
        DB::table('communication_definitions')->whereIn('id', $ids)->delete();
    }
};
