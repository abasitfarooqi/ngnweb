<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ShopPageTest extends DuskTestCase
{
    public function test_shop_page_loads_correctly(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('shop.home'))
                ->waitFor('.shop-home-root', 8)
                ->assertSee('NGN Shop');
        });
    }
}
