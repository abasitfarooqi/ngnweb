<?php

namespace Tests\Unit;

use App\Events\CustomerCommunicationCreated;
use App\Models\Communication;
use App\Models\CommunicationDefinition;
use App\Models\CommunicationPolicy;
use App\Models\SystemSetting;
use App\Services\Communications\CommunicationInboxClaimer;
use App\Services\Communications\CommunicationMailWebhookProcessor;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\Support\FakePolicyTestMail;
use Tests\TestCase;

class CommunicationSnapshotWriterTest extends TestCase
{
    private ?CommunicationDefinition $definition = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('communication_definitions') || ! Schema::hasTable('communications')) {
            $this->markTestSkipped('Communication system tables are not migrated.');
        }

        config(['communications.emergency_bypass' => false]);
        config(['communications.default_enabled' => false]);
        config(['communications.admin_enabled_setting_key' => 'communication_system_enabled']);

        Event::fake([CustomerCommunicationCreated::class]);
        Http::fake();

        CommunicationDefinition::query()
            ->where('email_class', FakePolicyTestMail::class)
            ->delete();

        $this->definition = CommunicationDefinition::query()->create([
            'key' => 'test.snapshot.mail.'.uniqid(),
            'name' => 'Snapshot Test Mail',
            'classification' => 'transactional',
            'category' => 'tests',
            'priority' => 'normal',
            'email_class' => FakePolicyTestMail::class,
            'template_view' => 'emails.templates.agreement-controller-universal',
            'active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->definition !== null) {
            Communication::query()
                ->where('communication_definition_id', $this->definition->id)
                ->each(function (Communication $communication): void {
                    $communication->attachments()->each(function ($attachment): void {
                        try {
                            Storage::disk($attachment->disk)->delete($attachment->path);
                        } catch (\Throwable) {
                        }
                        $attachment->delete();
                    });
                    $communication->deliveries()->delete();
                    $communication->recipients()->delete();
                    $communication->delete();
                });

            CommunicationPolicy::query()
                ->where('communication_definition_id', $this->definition->id)
                ->delete();
            $this->definition->delete();
        }

        SystemSetting::query()->where('key', 'communication_system_enabled')->delete();

        parent::tearDown();
    }

    public function test_legacy_mode_does_not_store_a_snapshot(): void
    {
        $this->setSystemEnabled(false);
        $this->setPolicy(email: true, inbox: true);

        config(['mail.default' => 'array']);
        (new FakePolicyTestMail)->to('customer@example.com')->send(app('mailer'));

        $this->assertSame(0, Communication::query()->where('communication_definition_id', $this->definition->id)->count());
    }

    public function test_email_send_stores_snapshot_and_email_delivery(): void
    {
        $this->setSystemEnabled(true);
        $this->setPolicy(email: true, inbox: false);
        config(['mail.default' => 'array']);

        (new FakePolicyTestMail)->to('customer@example.com')->send(app('mailer'));

        $row = Communication::query()
            ->where('communication_definition_id', $this->definition->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($row);
        $this->assertSame('customer@example.com', $row->recipient_email);
        $this->assertSame('sent', $row->deliveries()->where('channel', 'email')->value('status'));
        $this->assertSame(0, $row->recipients()->count());
        $this->assertNotEmpty($row->uuid);
    }

    public function test_inbox_only_without_portal_sends_legacy_email_and_defers_inbox(): void
    {
        $this->setSystemEnabled(true);
        $this->setPolicy(email: false, inbox: true);
        config(['mail.default' => 'array']);

        (new FakePolicyTestMail)->to('customer@example.com')->send(app('mailer'));

        $row = Communication::query()
            ->where('communication_definition_id', $this->definition->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($row);
        $this->assertSame('sent', $row->deliveries()->where('channel', 'email')->value('status'));
        $this->assertTrue((bool) data_get($row->payload_snapshot, 'legacy_email_fallback'));
        $this->assertSame('deferred', $row->deliveries()->where('channel', 'internal_inbox')->value('status'));
        $this->assertSame(0, $row->recipients()->count());
    }

    public function test_mailable_attachments_are_copied_onto_the_snapshot(): void
    {
        $this->setSystemEnabled(true);
        $this->setPolicy(email: true, inbox: false);
        config(['mail.default' => 'array']);

        $mail = new FakePolicyTestMail;
        $mail->includeTestAttachment = true;
        $mail->to('customer@example.com')->send(app('mailer'));

        $row = Communication::query()
            ->where('communication_definition_id', $this->definition->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($row);
        $this->assertSame(1, $row->attachments()->count());
        $attachment = $row->attachments()->first();
        $this->assertSame('Policy-Test.pdf', $attachment->filename);
        $this->assertTrue(Storage::disk($attachment->disk)->exists($attachment->path));
    }

    public function test_inbox_claimer_attaches_deferred_messages_to_a_new_portal_account(): void
    {
        if (! Schema::hasTable('customer_auths')) {
            $this->markTestSkipped('customer_auths table is not available.');
        }

        $this->setSystemEnabled(true);
        $this->setPolicy(email: false, inbox: true);
        config(['mail.default' => 'array']);

        $email = 'claim.'.uniqid().'@example.com';
        (new FakePolicyTestMail)->to($email)->send(app('mailer'));

        $row = Communication::query()
            ->where('communication_definition_id', $this->definition->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($row);
        $this->assertSame('deferred', $row->deliveries()->where('channel', 'internal_inbox')->value('status'));

        $claimed = app(CommunicationInboxClaimer::class)->claimFor($this->fakeCustomerAuth($email));

        $row->refresh();
        $this->assertSame(1, $claimed);
        $this->assertSame(1, $row->recipients()->count());
        $this->assertSame('delivered', $row->deliveries()->where('channel', 'internal_inbox')->value('status'));
    }

    public function test_mail_webhook_marks_email_delivered_by_uuid(): void
    {
        $this->setSystemEnabled(true);
        $this->setPolicy(email: true, inbox: false);
        config(['mail.default' => 'array']);

        (new FakePolicyTestMail)->to('customer@example.com')->send(app('mailer'));

        $row = Communication::query()
            ->where('communication_definition_id', $this->definition->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($row);

        $delivery = app(CommunicationMailWebhookProcessor::class)->process([
            'event' => 'delivered',
            'uuid' => $row->uuid,
            'message_id' => 'test-message-id@ngn.test',
        ]);

        $this->assertNotNull($delivery);
        $this->assertSame('delivered', $delivery->status);
        $this->assertNotNull($delivery->delivered_at);
    }

    private function fakeCustomerAuth(string $email): \App\Models\CustomerAuth
    {
        $auth = \App\Models\CustomerAuth::query()->make([
            'email' => $email,
            'password' => 'hash',
        ]);
        $auth->id = 880000 + random_int(1, 9999);
        $auth->customer_id = 1;
        $auth->exists = true;

        return $auth;
    }

    private function setSystemEnabled(bool $enabled): void
    {
        SystemSetting::query()->updateOrCreate(
            ['key' => 'communication_system_enabled'],
            [
                'display_name' => 'Transactional Communication System Enabled',
                'value' => $enabled ? '1' : '0',
                'locked' => true,
            ],
        );
    }

    private function setPolicy(bool $email, bool $inbox): void
    {
        CommunicationPolicy::query()->updateOrCreate(
            ['communication_definition_id' => $this->definition->id],
            [
                'email_enabled' => $email,
                'internal_inbox_enabled' => $inbox,
                'web_push_enabled' => false,
                'mobile_push_enabled' => false,
                'reply_allowed' => false,
                'enquiry_allowed' => false,
                'mandatory' => false,
                'priority' => 'normal',
            ],
        );
    }
}
