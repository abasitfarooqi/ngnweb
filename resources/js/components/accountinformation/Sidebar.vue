<template>
  <!-- Desktop Sidebar -->
  <div class="account-sidebar hidden md:block">
    <ul class="account-sidebar-ul">
      <li :class="{ active: $route.path === '/accountinformation' || $route.path === '/accountinformation/' }">
        <router-link to="/accountinformation/">Dashboard</router-link>
      </li>
      <li :class="{ active: $route.path === '/accountinformation/profile' }">
        <router-link to="/accountinformation/profile">Profile</router-link>
      </li>
      <li :class="{ active: $route.path === '/accountinformation/change-password' }">
        <router-link to="/accountinformation/change-password">Change Password</router-link>
      </li>
    </ul>

    <ul class="account-sidebar-ul">
      <li :class="{ active: $route.path.startsWith('/accountinformation/orders') }">
        <router-link to="/accountinformation/orders">Orders</router-link>
      </li>
      <li :class="{ active: $route.path === '/accountinformation/addresses' }">
        <router-link to="/accountinformation/addresses">Addresses</router-link>
      </li>
      <!-- <li :class="{ active: $route.path === '/accountinformation/payment-methods' }">
        <router-link to="/accountinformation/payment-methods">Payment Methods</router-link>
      </li> -->

      <li class="">
        <button @click="handleLogout" class="logout-button" :disabled="userStore.loading">
          <template v-if="userStore.loading">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
              </path>
            </svg>
            Logging out...
          </template>
          <template v-else>
            Logout
          </template>
        </button>
      </li>
    </ul>
  </div>

  <!-- Mobile Dropdown -->
  <div class="md:hidden relative">
    <button @click="isOpen = !isOpen" class="w-full p-2 text-left border rounded flex justify-between items-center">
      <span>Menu</span>
      <svg class="w-4 h-4" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
      </svg>
    </button>

    <div v-if="isOpen" class="absolute w-full bg-white border rounded mt-1 z-50">
      <router-link to="/accountinformation/" class="block p-2 hover:bg-gray-100"
        :class="{ 'bg-gray-50': $route.path === '/accountinformation/' }">
        Dashboard
      </router-link>
      <router-link to="/accountinformation/profile" class="block p-2 hover:bg-gray-100"
        :class="{ 'bg-gray-50': $route.path === '/accountinformation/profile' }">
        Profile
      </router-link>
      <router-link to="/accountinformation/change-password" class="block p-2 hover:bg-gray-100"
        :class="{ 'bg-gray-50': $route.path === '/accountinformation/change-password' }">
        Change Password
      </router-link>
      <router-link to="/accountinformation/orders" class="block p-2 hover:bg-gray-100"
        :class="{ 'bg-gray-50': $route.path === '/accountinformation/orders' }">
        Orders
      </router-link>
      <router-link to="/accountinformation/addresses" class="block p-2 hover:bg-gray-100"
        :class="{ 'bg-gray-50': $route.path === '/accountinformation/addresses' }">
        Addresses
      </router-link>
      <router-link to="/accountinformation/payment-methods" class="block p-2 hover:bg-gray-100"
        :class="{ 'bg-gray-50': $route.path === '/accountinformation/payment-methods' }">
        Payment Methods
      </router-link>
      <button @click="handleLogout" class="w-full text-left p-2 hover:bg-gray-100" :disabled="userStore.loading">
        <template v-if="userStore.loading">
          <svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
          </svg>
          Logging out...
        </template>
        <template v-else>
          Logout
        </template>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useUserStore } from '@/stores/modules/user';
import { useRouter } from 'vue-router';

const isOpen = ref(false);
const userStore = useUserStore();
const router = useRouter();

const handleLogout = async () => {
  try {
    await userStore.logout();
    router.push('/accountinformation/login');
  } catch (error) {
    console.error('Logout failed:', error);
  }
};
</script>

<style scoped></style>