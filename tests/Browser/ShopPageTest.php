<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ShopPageTest extends DuskTestCase
{
    public function test_shop_page_loads_correctly(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('shop-motorcycle'))
                ->waitFor('#app', 4)
                ->assertTitle('Ecommerce Application - Neguinho Motors Ltd');
        });
    }
}
