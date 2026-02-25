<template>
  <DefaultLayout>
    <div class="register-container">
      <h2 class="text-2xl font-bold mb-4">Register</h2>

      <!-- General error messages -->
      <div v-if="errors?.general" class="mb-4 p-3 bg-red-100 text-red-700 rounded">
        <p v-for="error in errors.general" :key="error">{{ error }}</p>
      </div>

      <!-- Field-specific validation errors -->
      <div v-if="errors" class="mb-4">
        <div v-for="(errorArray, field) in errors" :key="field" v-if="field !== 'general'"
          class="error-message p-3 mb-2 bg-red-100 text-red-700 rounded">
          <p v-for="error in errorArray" :key="error">{{ error }}</p>
        </div>
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div class="form-group">
            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
            <input type="text" id="first_name" v-model="formData.first_name" required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
            <span v-if="errors?.first_name" class="text-sm text-red-600">{{ errors.first_name[0] }}</span>
          </div>
          <div class="form-group">
            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
            <input type="text" id="last_name" v-model="formData.last_name" required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
            <span v-if="errors?.last_name" class="text-sm text-red-600">{{ errors.last_name[0] }}</span>
          </div>
        </div>

        <div class="form-group">
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" id="email" v-model="formData.email" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
          <span v-if="errors?.email" class="text-sm text-red-600">{{ errors.email[0] }}</span>
        </div>

        <div class="form-group">
          <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
          <input type="tel" id="phone" v-model="formData.phone" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
          <span v-if="errors?.phone" class="text-sm text-red-600">{{ errors.phone[0] }}</span>
        </div>

        <div class="form-group">
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <input type="password" id="password" v-model="formData.password" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
          <span v-if="errors?.password" class="text-sm text-red-600">{{ errors.password[0] }}</span>
        </div>

        <div class="form-group">
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
          <input type="password" id="password_confirmation" v-model="formData.password_confirmation" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
        </div>


        <div class="flex items-center justify-center space-x-2">
          <input type="checkbox" id="terms" name="terms"
            class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500" required>
          <label for="terms" class="text-sm text-gray-700 mt-1  ">
            I accept the agreement
            <a href="/legals/terms-conditions" class="text-primary-600 hover:text-primary-500 underline">Terms and
              Conditions</a>
          </label>
        </div>



        <button type="submit" :disabled="userStore.loading"
          class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50">
          {{ userStore.loading ? 'Registering...' : 'Register' }}
        </button>



        <div class="text-center mt-4">
          <router-link to="/accountinformation/login" class="text-sm text-primary-600 hover:text-primary-500">
            Already have an account? Login here
          </router-link>

        </div>
      </form>
    </div>
  </DefaultLayout>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import DefaultLayout from '@/components/layout/DefaultLayout.vue';
import { useUserStore } from '@/stores/modules/user';

const router = useRouter();
const userStore = useUserStore();
const errors = ref(null);

const formData = reactive({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
});

const handleRegister = async () => {
  try {
    errors.value = null;

    const success = await userStore.register(formData);
    if (success) {
      router.push('/accountinformation');
    }
  } catch (error) {


    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else if (error.response?.data?.message) {
      errors.value = { general: [error.response.data.message] };
    } else {
      errors.value = { general: ['An unexpected error occurred'] };
    }
  }
};
</script>

<style scoped>
.register-container {
  max-width: 600px;
  margin: 2rem auto;
  padding: 2rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  background-color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>