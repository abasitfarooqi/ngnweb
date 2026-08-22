<?php

namespace Tests\Unit;

use App\Services\Communications\CommunicationDefinitionRegistry;
use App\Support\Communications\DiscoveredTransactionalCommunicationCatalog;
use Tests\Support\FakeCampaignCommunicationDefinition;
use Tests\Support\FakeTransactionalCommunicationDefinition;
use Tests\TestCase;

class CommunicationDefinitionRegistryTest extends TestCase
{
    public function test_it_returns_only_transactional_definitions(): void
    {
        config()->set('communications.definitions', [
            FakeTransactionalCommunicationDefinition::class,
            FakeCampaignCommunicationDefinition::class,
        ]);

        $definitions = app(CommunicationDefinitionRegistry::class)->all();

        $this->assertCount(1, $definitions);
        $this->assertSame('test.transactional', $definitions[0]->key);
        $this->assertSame('transactional', $definitions[0]->classification);
        $this->assertTrue($definitions[0]->emailDefault);
        $this->assertFalse($definitions[0]->internalInboxDefault);
    }

    public function test_discovered_catalog_registers_transactional_emails_with_safe_defaults(): void
    {
        config()->set('communications.definitions', [
            DiscoveredTransactionalCommunicationCatalog::class,
        ]);

        $definitions = app(CommunicationDefinitionRegistry::class)->all();
        $keys = collect($definitions)->pluck('key');

        $this->assertGreaterThan(40, $definitions);
        $this->assertTrue($keys->contains('rental.agreement.issued'));
        $this->assertTrue($keys->contains('rental.agreement.review'));
        $this->assertTrue($keys->contains('contract.hire.issued'));
        $this->assertTrue($keys->contains('finance.contract.review'));
        $this->assertTrue($keys->contains('rental.deposit.return'));
        $this->assertTrue($keys->contains('rental.referral.approval_report'));
        $this->assertTrue($keys->contains('rental.referral.staff_invoice_notice'));
        $this->assertTrue($keys->contains('rental.direct.free_week'));
        $this->assertFalse($keys->contains('rental.weekly.follow_up_report'));
        $this->assertTrue($keys->contains('rental.invoice.update_reminder'));
        $this->assertTrue($keys->contains('rental.referral.invitation'));
        $this->assertTrue($keys->contains('rental.referral.under_review'));
        $this->assertTrue($keys->contains('rental.referral.reward_available'));

        $byKey = collect($definitions)->keyBy('key');
        $this->assertSame('Referral invitation to a friend', $byKey['rental.referral.invitation']->name);
        $this->assertSame('We have your referral', $byKey['rental.referral.under_review']->name);
        $this->assertSame('Referral free week is ready', $byKey['rental.referral.reward_available']->name);
        $this->assertSame('Referral approved (director copy)', $byKey['rental.referral.approval_report']->name);
        $this->assertSame('Referral free week applied (director copy)', $byKey['rental.referral.staff_invoice_notice']->name);
        $this->assertSame('Staff free week, not a referral (director copy)', $byKey['rental.direct.free_week']->name);
        $this->assertSame('Invoice chase note to the customer', $byKey['rental.invoice.update_reminder']->name);
        $this->assertTrue($keys->contains('ecommerce.order.confirmed'));
        $this->assertTrue($keys->contains('customer.document.request'));
        $this->assertFalse($keys->contains('campaign.survey'));

        foreach ($definitions as $definition) {
            $this->assertSame('transactional', $definition->classification);
            $this->assertTrue($definition->emailDefault);
            $this->assertFalse($definition->internalInboxDefault);
            $this->assertFalse($definition->webPushDefault);
            $this->assertFalse($definition->mobilePushDefault);
        }
    }
}
