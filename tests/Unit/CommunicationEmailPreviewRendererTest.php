<?php

namespace Tests\Unit;

use App\Models\CommunicationDefinition;
use App\Services\Communications\CommunicationEmailPreviewRenderer;
use Tests\TestCase;

class CommunicationEmailPreviewRendererTest extends TestCase
{
    public function test_rental_payment_reversed_preview_does_not_error(): void
    {
        $preview = $this->previewFor('rental.payment.reversed', 'Rental Payment Reversed');

        $this->assertTrue($preview['available'], (string) ($preview['error'] ?? ''));
        $this->assertStringContainsString('Booking No', $preview['html']);
        $this->assertStringNotContainsString('Undefined array key', $preview['html']);
    }

    public function test_rental_deposit_return_preview_uses_receipt_layout(): void
    {
        $preview = $this->previewFor('rental.deposit.return', 'Rental Deposit Return');

        $this->assertTrue($preview['available'], (string) ($preview['error'] ?? ''));
        $this->assertGreaterThan(2000, strlen($preview['html']));
        $this->assertStringContainsString('Rental Deposit Return', $preview['html']);
        $this->assertStringContainsString('Amount Returned', $preview['html']);
        $this->assertStringContainsString('Dear', $preview['html']);
    }

    public function test_core_rental_previews_render_full_html(): void
    {
        foreach ([
            'rental.payment.receipt' => 'Rental Payment Receipt',
            'rental.payment.reversed' => 'Rental Payment Reversed',
            'rental.deposit.return' => 'Rental Deposit Return',
        ] as $key => $name) {
            $preview = $this->previewFor($key, $name);
            $this->assertTrue($preview['available'], $key.': '.((string) ($preview['error'] ?? '')));
            $this->assertGreaterThan(1500, strlen((string) $preview['html']), $key.' preview HTML is too short.');
        }
    }

    /**
     * @return array{
     *     available: bool,
     *     subject: string,
     *     html: string,
     *     source: string,
     *     error: string|null,
     *     uses_sample_data: bool
     * }
     */
    private function previewFor(string $key, string $name): array
    {
        $definition = new CommunicationDefinition([
            'key' => $key,
            'name' => $name,
            'classification' => 'transactional',
            'category' => 'rentals',
            'priority' => 'important',
            'template_view' => 'emails.templates.agreement-controller-universal',
            'active' => true,
        ]);

        return app(CommunicationEmailPreviewRenderer::class)->forDefinition($definition);
    }
}
