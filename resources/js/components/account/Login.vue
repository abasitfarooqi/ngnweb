<!-- File: resources/js/components/account/Login.vue -->
<template>
  <DefaultLayout>
    <div class="login-container">
      <h2 class="text-2xl font-bold mb-4">Customer Login</h2>
      <div v-if="userStore.error" class="error-message mb-4 p-3 bg-red-100 text-red-700 rounded">
        {{ userStore.error }}
      </div>
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div class="form-group">
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" id="email" v-model="email" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            :disabled="userStore.loading" />
        </div>
        <div class="form-group">
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <input type="password" id="password" v-model="password" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            :disabled="userStore.loading" />
        </div>

        <button type="submit" :disabled="userStore.loading"
          class="w-full flex justify-center py-2 px-4 border border-transparent ngn-btn">
          <template v-if="userStore.loading">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
              </path>
            </svg>
            Logging in...
          </template>
          <template v-else>
            Login
          </template>
        </button>
        <div class="text-center mt-4">
          <router-link to="/accountinformation/forgotpassword" class="text-sm text-primary-600 hover:text-primary-500">
            Forgot Password?
          </router-link>
        </div>
        <div class="text-center mt-4">
          <router-link to="/accountinformation/register" class="text-sm text-secondary-600 hover:text-secondary-500">
            Don't have an account? Register here
          </router-link>
        </div>
      </form>
    </div>
  </DefaultLayout>
</template>

<script setup>
import { ref, onMounted, onBeforeMount } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import DefaultLayout from '@/components/layout/DefaultLayout.vue';
import { useUserStore } from '@/stores/modules/user';

const router = useRouter();
const route = useRoute();
const userStore = useUserStore();
const email = ref('');
const password = ref('');

const handleLogin = async () => {
  // Clear any previous errors
  userStore.clearError();

  console.log('⭐ handleLogin started', { email: email.value });

  const success = await userStore.login({
    email: email.value,
    password: password.value,
    sessionId: window.sessioncid
  });

  // Add debug logging
  console.log('Login attempt result:', {
    success,
    error: userStore.error,
    errorExists: !!userStore.error
  });

  if (success) {
    await userStore.validateSession();
    const redirectPath = route.query.redirect || '/accountinformation';
    router.push(redirectPath);
  }
};

onBeforeMount(async () => {
  // Check authentication status on component mount
  const isValid = await userStore.validateSession();
  if (isValid) {
    router.push('/accountinformation');
  }
});
</script>

<style scoped>
.login-container {
  max-width: 400px;
  margin: 2rem auto;
  padding: 2rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  background-color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>
