<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EcommerceViewResolver
{
    public function handle(Request $request, Closure $next)
    {
        $isEcommercePath = $request->is('shop/*') || $request->is('shop');
        $isAccountPath = $request->is('accountinformation/*') || $request->is('accountinformation');
        // $isLoginPath = $request->is('v1/customer/login');

        if (! config('ecommerce.live') && ! config('ecommerce.portal')) {
            if ($isEcommercePath) {
                return response()->view('olders.frontend.vue_store.shop_maintenance');
            }
            if ($isAccountPath) {
                return response()->view('olders.frontend.vue_store.account_maintenance');
            }
        }

        if (config('ecommerce.portal') && ! config('ecommerce.live')) {
            if ($isEcommercePath) {
                return response()->view('olders.frontend.vue_store.shop_maintenance');
            }
        }

        if (config('ecommerce.live') && ! config('ecommerce.portal')) {
            if ($isAccountPath) {
                return response()->view('olders.frontend.vue_store.account_maintenance');
            }
        }

        // if (config('ecommerce.allow_login') == false && $isLoginPath) {
        //     return response()->json(['message' => 'Login is currently disabled.'], 403);
        // }

        return response()->view('olders.frontend.vue_store.app');
    }
}
