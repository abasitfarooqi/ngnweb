<?php

namespace Tests\Unit;

use App\Support\RentingReferralIdentity;
use Tests\TestCase;

class RentingReferralIdentityTest extends TestCase
{
    public function test_it_normalises_uk_mobile_and_email(): void
    {
        $this->assertSame('07123456789', RentingReferralIdentity::phone('+44 7123 456789'));
        $this->assertSame('a@example.com', RentingReferralIdentity::email('  A@Example.com '));
        $this->assertNull(RentingReferralIdentity::email(''));
        $this->assertNull(RentingReferralIdentity::phone('Not Provided'));
        $this->assertNull(RentingReferralIdentity::license('Not Provided'));
        $this->assertSame('SMITH123', RentingReferralIdentity::license(' smith 123 '));
    }

    public function test_it_detects_similar_names_without_merging(): void
    {
        $this->assertTrue(RentingReferralIdentity::namesLookSimilar('John Smith', 'john smith'));
        $this->assertTrue(RentingReferralIdentity::namesLookSimilar('Jon Smith', 'John Smith'));
        $this->assertFalse(RentingReferralIdentity::namesLookSimilar('John Smith', 'Amelia Patel'));
    }

    public function test_placeholder_licence_is_ignored(): void
    {
        $this->assertTrue(RentingReferralIdentity::isPlaceholder('n/a'));
        $this->assertFalse(RentingReferralIdentity::isPlaceholder('SMITH801010A99AA'));
    }
}
