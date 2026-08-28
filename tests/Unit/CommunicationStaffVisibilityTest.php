<?php

namespace Tests\Unit;

use App\Models\Communication;
use App\Support\Communications\CommunicationStaffRedactor;
use App\Support\FluxAdminAccess;
use App\Support\FluxAdminPageAccess;
use Tests\TestCase;

class CommunicationStaffVisibilityTest extends TestCase
{
    public function test_redacts_plain_password_and_passkey_lines(): void
    {
        $html = '<p>Phone Number: 07858323476</p><p>Password: 123499</p><p>Passkey: abcd12</p>';

        $redacted = CommunicationStaffRedactor::html($html);

        $this->assertStringContainsString('Phone Number: 07858323476', $redacted);
        $this->assertStringContainsString('Password: [hidden]', $redacted);
        $this->assertStringContainsString('Passkey: [hidden]', $redacted);
        $this->assertStringNotContainsString('123499', $redacted);
        $this->assertStringNotContainsString('abcd12', $redacted);
    }

    public function test_redacts_table_credential_cells(): void
    {
        $html = '<tr><td>Password</td><td>secret99</td></tr>';

        $redacted = CommunicationStaffRedactor::html($html);

        $this->assertStringContainsString('[hidden]', $redacted);
        $this->assertStringNotContainsString('secret99', $redacted);
    }

    public function test_inbox_off_without_staff_copy_hides_body_from_staff(): void
    {
        $communication = new Communication([
            'policy_snapshot' => [
                'internal_inbox_enabled' => false,
                'staff_copy_enabled' => false,
            ],
        ]);

        $this->assertFalse($communication->staffMaySeeBody());
        $this->assertFalse($communication->inboxEnabledForCustomer());
    }

    public function test_staff_copy_lets_staff_see_body_when_inbox_is_off(): void
    {
        $communication = new Communication([
            'policy_snapshot' => [
                'internal_inbox_enabled' => false,
                'staff_copy_enabled' => true,
            ],
        ]);

        $this->assertTrue($communication->staffMaySeeBody());
        $this->assertFalse($communication->inboxEnabledForCustomer());
    }

    public function test_inbox_on_lets_staff_see_body(): void
    {
        $communication = new Communication([
            'policy_snapshot' => [
                'internal_inbox_enabled' => true,
                'staff_copy_enabled' => false,
            ],
        ]);

        $this->assertTrue($communication->staffMaySeeBody());
        $this->assertTrue($communication->inboxEnabledForCustomer());
    }

    public function test_notifications_log_uses_view_notifications_permission(): void
    {
        $req = FluxAdminPageAccess::requirementForRoute('flux-admin.communications.sent.index');

        $this->assertSame(FluxAdminAccess::NOTIFICATIONS_PERMISSION, $req['permission'] ?? null);
    }

    public function test_notification_detail_uses_view_notifications_permission(): void
    {
        $req = FluxAdminPageAccess::requirementForRoute('flux-admin.communications.sent.show');

        $this->assertSame(FluxAdminAccess::NOTIFICATIONS_PERMISSION, $req['permission'] ?? null);
    }

    public function test_communications_panel_uses_manage_communications_permission(): void
    {
        $index = FluxAdminPageAccess::requirementForRoute('flux-admin.communications.index');
        $show = FluxAdminPageAccess::requirementForRoute('flux-admin.communications.show');

        $this->assertSame(FluxAdminAccess::COMMUNICATIONS_PERMISSION, $index['permission'] ?? null);
        $this->assertSame(FluxAdminAccess::COMMUNICATIONS_PERMISSION, $show['permission'] ?? null);
    }
}
