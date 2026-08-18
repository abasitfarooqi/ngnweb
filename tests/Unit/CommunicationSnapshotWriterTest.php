<?php

namespace Tests\Unit;

use App\Models\Communication;
use App\Models\CommunicationDefinition;
use App\Models\CommunicationPolicy;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Schema;
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
    }

    public function test_inbox_only_skips_email_and_records_inbox_delivery(): void
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
        $this->assertSame('skipped', $row->deliveries()->where('channel', 'email')->value('status'));
        $this->assertSame('failed', $row->deliveries()->where('channel', 'internal_inbox')->value('status'));
        $this->assertSame(0, $row->recipients()->count());
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
