<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MotorcycleDeliveryTest extends DuskTestCase
{
    // Removed DatabaseMigrations trait to avoid TTY mode error on Windows

    // public function testHomePageLoadsCorrectly()
    // {
    //     $this->browse(function (Browser $browser) {
    //         $browser->visit('/')
    //             ->assertSee('Neguinho Motors'); // Adjust based on expected behaviour
    //     });
    // }

    public function test_motorcycle_delivery_initial_step()
    {
        $this->browse(function (Browser $browser) {
            // Test 1: Visit the motorcycle delivery page and fill in pickup and dropoff details
            $browser->visit(route('motorcycle.delivery'))
                ->assertSee('Motorcycle Delivery Service') // Ensure the page content is correct
                ->type('pickup_postcode', 'SE6 4NU') // Example pickup postcode
                ->type('dropoff_postcode', 'SM7 1LW') // Example dropoff postcode
                ->press('Proceed to Next Step') // Press the button to proceed
                ->waitForRoute('motorcycle.delivery.store') // Wait for the next page to load
                ->assertSee('Complete Your Order - Motorcycle Delivery'); // Verify the next page title
        });
    }

    public function test_motorcycle_delivery_completion()
    {
        $this->browse(function (Browser $browser) {
            // Test 2: Fill in the remaining details and submit the order
            $browser->visit(route('motorcycle.delivery.store'))
                ->assertSee('Complete Your Order - Motorcycle Delivery') // Ensure the page content is correct
                ->type('pickup_address', '9-13 Unit 1179 Catford Hill London SE6 4NU') // Example pickup address
                ->type('dropoff_address', '329 High St, Sutton SM1 1LW') // Example dropoff address
                ->type('pick_up_datetime', now()->addDay()->format('Y-m-d\TH:i')) // Example pickup datetime
                ->type('vrm', 'KX67XEL') // Example vehicle registration number
                ->check('moveable') // Check the moveable checkbox
                ->check('documents') // Check the documents checkbox
                ->check('keys') // Check the keys checkbox
                ->type('full_name', 'John Doe') // Example full name
                ->type('phone', '07123456789') // Example phone number
                ->type('email', 'john.doe@example.com') // Example email
                ->type('address', '1 Example Street, London') // Example address
                ->select('vehicle_type_id', '1') // Select the vehicle type
                ->press('Submit Order') // Press the button to submit the order
                ->waitForRoute('motorcycle.delivery.success') // Wait for the success page to load
                ->assertSee('Order created successfully'); // Verify the success message
        });
    }
}
