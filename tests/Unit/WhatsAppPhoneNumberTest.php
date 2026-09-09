<?php

namespace Tests\Unit;

use App\Support\WhatsAppPhoneNumber;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WhatsAppPhoneNumberTest extends TestCase
{
    #[DataProvider('phoneNumbers')]
    public function test_phone_numbers_are_normalized_for_whatsapp(string $input, ?string $expected): void
    {
        $this->assertSame($expected, WhatsAppPhoneNumber::normalize($input));
    }

    public static function phoneNumbers(): array
    {
        return [
            'uk local' => ['07751 790568', '447751790568'],
            'uk international' => ['+44 7751 790568', '447751790568'],
            'uk country code' => ['447751790568', '447751790568'],
            'india international' => ['+91 94451 79152', '919445179152'],
            'india country code' => ['919445179152', '919445179152'],
            'empty' => ['', null],
            'invalid' => ['abc', null],
        ];
    }
}
