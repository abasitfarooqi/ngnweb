<template>
  <DefaultLayout>
    <div class="reset-password-container">
      <h2 class="text-2xl font-bold mb-4">Reset Password</h2>
      <div v-if="error" class="error-message mb-4 p-3 bg-red-100 text-red-700 rounded">
        {{ error }}
      </div>
      <div v-if="success" class="success-message mb-4 p-3 bg-green-100 text-green-700 rounded">
        {{ success }}
      </div>
      <form @submit.prevent="handleResetPassword" class="space-y-4">
        <div class="form-group">
          <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
          <input
            type="email"
            id="email"
            v-model="formData.email"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            :disabled="loading"
          />
        </div>

        <div class="form-group">
          <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
          <input
            type="password"
            id="password"
            v-model="formData.password"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            :disabled="loading"
          />
        </div>

        <div class="form-group">
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
          <input
            type="password"
            id="password_confirmation"
            v-model="formData.password_confirmation"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            :disabled="loading"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50"
        >
          <template v-if="loading">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Resetting Password...
          </template>
          <template v-else>
            Reset Password
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
import { reactive, ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import DefaultLayout from '@/components/layout/DefaultLayout.vue';
import { authAPI } from '@/services/api';

const router = useRouter();
const route = useRoute();
const loading = ref(false);
const error = ref('');
const success = ref('');

const formData = reactive({
  email: '',
  password: '',
  password_confirmation: '',
  token: '',
});

onMounted(() => {
  // Get token from URL
  formData.token = route.params.token;
  // Get email from query params if available
  const email = route.query.email;
  if (email) {
    formData.email = decodeURIComponent(email);
  }
});

const handleResetPassword = async () => {
  if (loading.value) return;
  
  error.value = '';
  success.value = '';
  loading.value = true;

  try {
    const response = await authAPI.confirmResetPassword(formData);
    success.value = response.data.message || 'Password has been reset successfully.';
    setTimeout(() => {
      router.push('/accountinformation/login');
    }, 2000);
  } catch (err) {
    error.value = err.response?.data?.message || 'An error occurred while resetting your password.';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.reset-password-container {
  max-width: 400px;
  margin: 2rem auto;
  padding: 2rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  background-color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style> 