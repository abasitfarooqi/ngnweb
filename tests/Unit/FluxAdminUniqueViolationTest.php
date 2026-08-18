<?php

namespace Tests\Unit;

use App\Support\FluxAdminUniqueViolation;
use Tests\TestCase;

class FluxAdminUniqueViolationTest extends TestCase
{
    public function test_parses_mysql_customers_email_unique_key(): void
    {
        $column = FluxAdminUniqueViolation::columnFromMessage(
            "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'a@b.com' for key 'customers_email_unique'",
            'insert into `customers` (`email`) values (?)'
        );

        $this->assertSame('email', $column);
        $this->assertSame(
            'This email is already in use. Please use a different email.',
            FluxAdminUniqueViolation::message($column)
        );
    }

    public function test_parses_mysql_qualified_unique_key(): void
    {
        $column = FluxAdminUniqueViolation::columnFromMessage(
            "Duplicate entry 'NGN-1' for key 'ngn_products.ngn_products_sku_unique'"
        );

        $this->assertSame('sku', $column);
        $this->assertSame('This SKU is already in use.', FluxAdminUniqueViolation::message($column));
    }

    public function test_parses_sqlite_unique_constraint(): void
    {
        $this->assertSame(
            'email',
            FluxAdminUniqueViolation::columnFromMessage('UNIQUE constraint failed: customers.email')
        );
    }

    public function test_maps_form_bag_error_key(): void
    {
        $component = new class
        {
            public array $form = ['email' => 'a@b.com'];
        };

        $this->assertSame('form.email', FluxAdminUniqueViolation::errorKey($component, 'email'));
    }

    public function test_maps_form_data_bag_error_key(): void
    {
        $component = new class
        {
            public array $formData = ['email' => 'a@b.com'];
        };

        $this->assertSame('formData.email', FluxAdminUniqueViolation::errorKey($component, 'email'));
    }
}
