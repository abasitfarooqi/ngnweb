<template>
  <DefaultLayout>
    <div class="login-container">
      <h2 class="text-2xl font-bold mb-4">Reset Password</h2>
      <div v-if="error" class="error-message mb-4 p-3 bg-red-100 text-red-700 rounded">
        {{ error }}
      </div>
      <div v-if="success" class="success-message mb-4 p-3 bg-green-100 text-green-700 rounded">
        {{ success }}
      </div>
      <form @submit.prevent="handleResetRequest" class="space-y-4">
        <div class="form-group">
          <label class="block text-sm font-medium text-gray-700">Reset Method</label>
          <div class="mt-2 space-x-4">
            <label class="inline-flex items-center">
              <input
                type="radio"
                v-model="type"
                value="email"
                class="form-radio text-primary-600"
              />
              <span class="ml-2">Email</span>
            </label>
            <label class="inline-flex items-center">
              <input
                type="radio"
                v-model="type"
                value="phone"
                class="form-radio text-primary-600"
                disabled
              />
              <span class="ml-2 text-gray-400">Phone (Coming Soon)</span>
            </label>
          </div>
        </div>

        <div v-if="type === 'email'" class="form-group">
          <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
          <input
            type="email"
            id="email"
            v-model="identifier"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            :disabled="loading"
            placeholder="Enter your email address"
          />
        </div>

        <div v-else-if="type === 'phone'" class="form-group">
          <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
          <input
            type="tel"
            id="phone"
            v-model="identifier"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            :disabled="loading || type === 'phone'"
            placeholder="Enter your phone number"
          />
        </div>

        <button
          type="submit"
          :disabled="loading || type === 'phone'"
          class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50"
        >
          <template v-if="loading">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Sending Reset Link...
          </template>
          <template v-else>
            Send Reset Link
          </template>
        </button>

        <div class="text-center mt-4">
          <router-link
            to="/accountinformation/login"
            class="text-sm text-primary-600 hover:text-primary-500"
          >
            Back to Login
          </router-link>
        </div>
      </form>
    </div>
  </DefaultLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import DefaultLayout from '@/components/layout/DefaultLayout.vue';
import { authAPI } from '@/services/api';

const router = useRouter();
const type = ref('email');
const identifier = ref('');
const loading = ref(false);
const error = ref('');
const success = ref('');

const handleResetRequest = async () => {
  if (loading.value) return;
  
  error.value = '';
  success.value = '';
  loading.value = true;

  try {
    const response = await authAPI.forgotPassword({
      identifier: identifier.value,
      type: type.value
    });

    success.value = response.data.message || 'Password reset instructions have been sent to your email.';
    identifier.value = '';
  } catch (err) {
    error.value = err.response?.data?.message || 'An error occurred while processing your request.';
  } finally {
    loading.value = false;
  }
};
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