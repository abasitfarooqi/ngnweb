<?php

namespace Tests\Unit;

use App\Support\Communications\CommunicationPreviewText;
use Tests\TestCase;

class CommunicationPreviewTextTest extends TestCase
{
    public function test_it_keeps_readable_body_and_drops_email_css(): void
    {
        $html = '<html><head><style>body,table,td,a{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}table,td{mso-table-lspace:0pt;mso-table-rspace:0pt}</style></head><body><p>Dear Jane, your deposit of £150 has been returned.</p></body></html>';

        $preview = CommunicationPreviewText::fromHtml($html);

        $this->assertStringContainsString('Dear Jane', $preview);
        $this->assertStringContainsString('£150', $preview);
        $this->assertStringNotContainsString('-webkit-text-size-adjust', $preview);
        $this->assertStringNotContainsString('mso-table-lspace', $preview);
        $this->assertStringNotContainsString('body,table,td', $preview);
    }

    public function test_it_cleans_already_stripped_css_previews(): void
    {
        $stored = 'Rental Deposit Return body,table,td,a{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%} table,td{mso-table-lspace:0pt;mso-table-rspace:0pt} img{-ms-interpolation-mode:bicubi...';

        $preview = CommunicationPreviewText::readable($stored, 'Rental Deposit Return');

        $this->assertSame('Rental Deposit Return', $preview);
        $this->assertStringNotContainsString('-webkit-text-size-adjust', $preview);
    }
}
