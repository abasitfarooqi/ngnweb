<?php

namespace Tests\Unit;

use App\Models\CommunicationDefinition;
use App\Models\CommunicationPolicy;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\Communications\CommunicationAuditRecorder;
use App\Services\Communications\TransactionalEmailPolicy;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\Support\FakePolicyTestMail;
use Tests\TestCase;

class TransactionalCommunicationPolicyTest extends TestCase
{
    private ?CommunicationDefinition $definition = null;

    private ?int $testUserId = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('communication_definitions') || ! Schema::hasTable('system_settings')) {
            $this->markTestSkipped('Communication system tables are not migrated.');
        }

        config(['communications.emergency_bypass' => false]);
        config(['communications.default_enabled' => false]);
        config(['communications.admin_enabled_setting_key' => 'communication_system_enabled']);

        CommunicationDefinition::query()
            ->where('email_class', FakePolicyTestMail::class)
            ->delete();

        $this->definition = CommunicationDefinition::query()->create([
            'key' => 'test.policy.mail.'.uniqid(),
            'name' => 'Policy Test Mail',
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
            CommunicationPolicy::query()
                ->where('communication_definition_id', $this->definition->id)
                ->delete();
            $this->definition->delete();
        }

        SystemSetting::query()->where('key', 'communication_system_enabled')->delete();

        if ($this->testUserId !== null) {
            User::query()->whereKey($this->testUserId)->delete();
        }

        parent::tearDown();
    }

    public function test_legacy_mode_when_global_system_is_off(): void
    {
        $this->setSystemEnabled(false);
        $this->setPolicy(email: false, inbox: false);

        $this->assertTrue($this->policy()->shouldSendMailable(new FakePolicyTestMail));
        $this->assertTrue($this->policy()->shouldSendKey($this->definition->key, 'customer@example.com'));
    }

    public function test_email_blocked_when_system_on_and_email_off(): void
    {
        $this->setSystemEnabled(true);
        $this->setPolicy(email: false, inbox: true);

        $this->assertFalse($this->policy()->shouldSendMailable(new FakePolicyTestMail));
        $this->assertFalse($this->policy()->shouldSendKey($this->definition->key, 'customer@example.com'));
    }

    public function test_inbox_only_does_not_send_email(): void
    {
        $this->setSystemEnabled(true);
        $this->setPolicy(email: false, inbox: true);

        $this->assertFalse($this->policy()->shouldSendMailable(new FakePolicyTestMail));
    }

    public function test_email_only_sends_when_system_on(): void
    {
        $this->setSystemEnabled(true);
        $this->setPolicy(email: true, inbox: false);

        $this->assertTrue($this->policy()->shouldSendMailable(new FakePolicyTestMail));
    }

    public function test_email_and_inbox_both_on_sends_email(): void
    {
        $this->setSystemEnabled(true);
        $this->setPolicy(email: true, inbox: true);

        $this->assertTrue($this->policy()->shouldSendMailable(new FakePolicyTestMail));
    }

    public function test_both_channels_off_blocks_email(): void
    {
        $this->setSystemEnabled(true);
        $this->setPolicy(email: false, inbox: false);

        $this->assertFalse($this->policy()->shouldSendMailable(new FakePolicyTestMail));
    }

    public function test_legacy_definition_ignores_policy_even_when_system_on(): void
    {
        $this->setSystemEnabled(true);
        $this->definition->forceFill(['active' => false])->save();
        $this->setPolicy(email: false, inbox: false);

        $this->assertTrue($this->policy()->shouldSendMailable(new FakePolicyTestMail));
    }

    public function test_internal_recipient_bypasses_customer_email_block(): void
    {
        $this->setSystemEnabled(true);
        $this->setPolicy(email: false, inbox: true);

        $mail = new FakePolicyTestMail;
        $mail->to('customerservice@neguinhomotors.co.uk');

        $this->assertTrue($this->policy()->shouldSendMailable($mail));
    }

    public function test_mailable_trait_blocks_send_when_email_disabled(): void
    {
        $this->setSystemEnabled(true);
        $this->setPolicy(email: false, inbox: true);

        Mail::fake();

        $result = (new FakePolicyTestMail)->send(Mail::mailer());

        $this->assertNull($result);
        Mail::assertNothingSent();
    }

    public function test_mailable_trait_sends_when_email_enabled(): void
    {
        $this->setSystemEnabled(true);
        $this->setPolicy(email: true, inbox: false);

        Mail::fake();

        Mail::to('customer@example.com')->send(new FakePolicyTestMail);

        Mail::assertSent(FakePolicyTestMail::class);
    }

    public function test_audit_log_stores_actor_id_and_name(): void
    {
        if (! Schema::hasTable('users')) {
            $this->markTestSkipped('Users table is not available.');
        }

        $user = User::query()->create([
            'first_name' => 'Audit',
            'last_name' => 'Tester',
            'name' => 'Audit Tester',
            'username' => 'audit_tester_'.uniqid(),
            'email' => 'audit.tester.'.uniqid().'@example.com',
            'password' => 'test-password-hash',
            'avatar_type' => 'gravatar',
            'opt_in' => 0,
            'role_id' => 1,
        ]);
        $this->testUserId = $user->id;

        $this->actingAs($user);

        $audit = app(CommunicationAuditRecorder::class)->record(
            event: 'policy_changed',
            definition: $this->definition,
            field: 'email_enabled',
            oldValue: true,
            newValue: false,
            metadata: ['source' => 'test'],
        );

        $this->assertNotNull($audit);
        $this->assertSame($user->id, $audit->actor_user_id);
        $this->assertSame('Audit Tester', $audit->metadata['actor_name']);
        $this->assertSame($user->id, $audit->metadata['actor_user_id']);
        $this->assertSame($user->email, $audit->metadata['actor_email']);
        $this->assertSame('Audit Tester (#'.$user->id.')', $audit->actorLabel());

        $audit->delete();
    }

    private function policy(): TransactionalEmailPolicy
    {
        return app(TransactionalEmailPolicy::class);
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

    private function setPolicy(bool $email, bool $inbox): CommunicationPolicy
    {
        return CommunicationPolicy::query()->updateOrCreate(
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
