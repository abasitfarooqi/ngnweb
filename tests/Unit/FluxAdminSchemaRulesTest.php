<?php

namespace Tests\Unit;

use App\Models\Motorbike;
use App\Support\FluxAdminSchemaRules;
use Tests\TestCase;

class FluxAdminSchemaRulesTest extends TestCase
{
    public function test_motorbike_required_columns_match_the_database(): void
    {
        $columns = FluxAdminSchemaRules::requiredColumns('motorbikes');

        $this->assertContains('engine', $columns);
        $this->assertContains('vin_number', $columns);
        $this->assertContains('year', $columns);
        $this->assertContains('color', $columns);
        $this->assertNotContains('id', $columns);
    }

    public function test_it_only_requires_columns_present_on_the_form_bag(): void
    {
        $rules = FluxAdminSchemaRules::rulesForBag(
            Motorbike::class,
            ['engine' => '', 'make' => 'Honda', 'notes' => ''],
            'form'
        );

        $this->assertSame(['required'], $rules['form.engine']);
        $this->assertSame(['required'], $rules['form.make']);
        $this->assertArrayNotHasKey('form.notes', $rules);
        $this->assertArrayNotHasKey('form.vin_number', $rules);
    }
}
