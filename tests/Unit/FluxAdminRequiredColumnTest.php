<?php

namespace Tests\Unit;

use App\Support\FluxAdminRequiredColumn;
use Tests\TestCase;

class FluxAdminRequiredColumnTest extends TestCase
{
    public function test_parses_mysql_missing_default(): void
    {
        $column = FluxAdminRequiredColumn::columnFromMessage(
            "SQLSTATE[HY000]: General error: 1364 Field 'engine' doesn't have a default value"
        );

        $this->assertSame('engine', $column);
        $this->assertSame(
            'Please fill in engine. This field is required.',
            FluxAdminRequiredColumn::message($column)
        );
    }

    public function test_parses_mysql_cannot_be_null(): void
    {
        $this->assertSame(
            'booking_id',
            FluxAdminRequiredColumn::columnFromMessage("SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'booking_id' cannot be null")
        );
    }
}
