<?php

namespace Tests\Unit;

use App\Support\UploadLimit;
use Tests\TestCase;

class UploadLimitTest extends TestCase
{
    public function test_it_stays_within_php_and_app_caps(): void
    {
        $this->assertGreaterThan(0, UploadLimit::maxBytes());
        $this->assertLessThanOrEqual(UploadLimit::APP_MAX_BYTES, UploadLimit::maxBytes());
        $this->assertSame((int) floor(UploadLimit::maxBytes() / 1024), UploadLimit::maxKilobytes());
        $this->assertStringEndsWith(' MB', UploadLimit::label());
    }
}
