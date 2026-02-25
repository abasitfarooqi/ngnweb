<!-- File: resources/js/components/common/Navigation.vue -->
<template>
  <nav class="bg-white shadow" style="z-index:40;">
    
    <div class="container ">
      <div class="flex justify-between h-16">
        <div class="flex">
          <router-link to="/shop" class="flex-shrink-0 flex items-center">
            Shop
          </router-link>
          <div class="flex items-center">
            <div class="relative group">
              <button class="flex items-center px-3 py-2 text-gray-700 hover:text-gray-900  text-sm font-medium">
                Shop Pages
              <svg class="w-4 h-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
            <div class="absolute left-0 w-48  shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden group-hover:block">
              <div class="py-1">
                <router-link to="/shop/product-page/1" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Product Page</router-link>
                <router-link to="/shop/basket" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Basket Page</router-link>
                <router-link to="/shop/details" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Details Page</router-link>
                <router-link to="/shop/payment" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Payment Page</router-link>
                <router-link to="/shop/categories" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Categories</router-link>
                <router-link to="/shop/brands" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Brands</router-link>
              </div>
            </div>
          </div>

          
          <!-- Legals Dropdown -->
          <div class="relative group">
            <button class="flex items-center px-3 py-2 text-gray-700 hover:text-gray-900  text-sm font-medium">
              Legals
              <svg class="w-4 h-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
            <div class="absolute left-0 w-64  shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden group-hover:block">
              <div class="py-1">
                <router-link to="/legals/return-policy" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Return Policy</router-link>
                <router-link to="/legals/refund-policy" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Refund Policy</router-link>
                <router-link to="/legals/warranty-claim-policy" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Warranty Claim Policy</router-link>
                <router-link to="/legals/damaged-goods-policy" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Damaged Goods Policy</router-link>
                <router-link to="/legals/privacy-policy" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Privacy Policy</router-link>
                <router-link to="/legals/cookie-policy" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cookie Policy</router-link>
                <router-link to="/legals/shipping-policy" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Shipping Policy</router-link>
                <router-link to="/legals/cancellation-policy" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cancellation Policy</router-link>
                <router-link to="/legals/terms-conditions" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Terms & Conditions</router-link>
                <router-link to="/legals/payment-policy" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Payment Policy</router-link>
                <router-link to="/legals/user-agreement" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">User Agreement</router-link>
                <router-link to="/legals/accessibility-statement" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Accessibility Statement</router-link>
                <router-link to="/legals/data-protection-agreement" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Data Protection Agreement</router-link>
                <router-link to="/legals/click-collect" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Click & Collect</router-link>
                <router-link to="/legals/frequently-asked-questions" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Frequently Asked Questions</router-link>
              </div>
            </div>
          </div>


          </div>
        </div>
        
        

      
  <div class="flex items-center">
    <!-- Authenticated User: Account Settings Dropdown -->
    <template v-if="userStore.isAuthenticated">
      <div class="relative group ml-4">
        <button class="flex items-center px-3 py-2 text-gray-700 hover:text-gray-900  text-sm font-medium">
          Account Settings
          <svg
            class="w-4 h-4 ml-1"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
        <div class="absolute right-0 w-64  shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden group-hover:block">
          <div class="py-1">
            <!-- Authenticated User Links -->
            <router-link
              to="/accountinformation"
              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
            >
              Profile
            </router-link>
            <div class="border-t border-gray-100"></div>
            <button
              @click="handleLogout"
              class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
            >
              <template v-if="userStore.loading">
                <svg
                  class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                Logging out...
              </template>
              <template v-else>
                Logout
              </template>
            </button>
          </div>
        </div>
      </div>
    </template>

    <!-- Unauthenticated User: Login and Register Links -->
    <template v-else>
      <router-link
        to="/accountinformation/login"
        class="px-3 py-2 text-gray-700 hover:text-gray-900  text-sm font-medium"
      >
        Login
      </router-link>
      <router-link
        to="/accountinformation/register"
        class="ml-4 px-3 py-2 text-gray-700 hover:text-gray-900  text-sm font-medium"
      >
        Register
      </router-link>
    </template>
  </div>



      </div>
    </div>
  </nav>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useUserStore } from '@/stores/modules/user';

const router = useRouter();
const userStore = useUserStore();

const handleLogout = async () => {
  const success = await userStore.logout();
  if (success) {
    await router.push('/accountinformation/login');
    // Force to reload the page as the user is logged out
    window.location.reload();
  }
};
</script>

<style scoped>
nav {
  
}
ul {
  list-style-type: none;
}
li {
  display: inline;
  margin-right: 15px;
}
</style>
