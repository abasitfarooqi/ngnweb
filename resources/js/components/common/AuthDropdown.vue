<!-- resources/js/components/common/AuthDropdown.vue -->
<template>
  <div class="relative">
    <!-- Dropdown Trigger -->
    <button @click="toggleDropdown"
      class="flex items-center px-3 py-2 text-gray-700 hover:text-gray-900 focus:outline-none" aria-haspopup="true"
      :aria-expanded="isOpen">
      <span v-if="!isAuthenticated && !userStore.loading">Account</span>
      <span v-else-if="isAuthenticated">{{ user.name }}</span>
      <span v-else>Loading...</span>
      <svg class="w-4 h-4 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
        stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown Menu -->
    <div v-if="isOpen" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50">
      <div class="py-2">
        <!-- If Not Authenticated: Show Login/Register Tabs -->
        <div v-if="!isAuthenticated">
          <!-- Toggle Between Login and Register -->
          <div class="flex justify-center mb-4">
            <button @click="activeTab = 'login'" :class="activeTab === 'login' ? activeClass : inactiveClass"
              class="px-4 py-2 focus:outline-none">
              Login
            </button>
            <button @click="activeTab = 'register'" :class="activeTab === 'register' ? activeClass : inactiveClass"
              class="px-4 py-2 focus:outline-none">
              Register
            </button>
          </div>

          <!-- Login Form -->
          <div v-if="activeTab === 'login'" class="px-6">
            <form @submit.prevent="login">
              <div class="mb-4">
                <label for="login-email" class="block text-gray-700">Email</label>
                <input type="email" id="login-email" v-model="loginForm.email" required
                  class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300" />
              </div>
              <div class="mb-4">
                <label for="login-password" class="block text-gray-700">Password</label>
                <input type="password" id="login-password" v-model="loginForm.password" required
                  class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300" />
              </div>
              <div v-if="loginError" class="mb-4 text-red-500">
                {{ loginError }}
              </div>
              <button type="submit" class="w-full btn-primary">
                Login
              </button>
            </form>
          </div>

          <!-- Register Form -->
          <div v-else class="px-6">
            <form @submit.prevent="register">
              <div class="mb-4">
                <label for="register-name" class="block text-gray-700">Name</label>
                <input type="text" id="register-name" v-model="registerForm.name" required
                  class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300" />
              </div>
              <div class="mb-4">
                <label for="register-email" class="block text-gray-700">Email</label>
                <input type="email" id="register-email" v-model="registerForm.email" required
                  class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300" />
              </div>
              <div class="mb-4">
                <label for="register-password" class="block text-gray-700">Password</label>
                <input type="password" id="register-password" v-model="registerForm.password" required
                  class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300" />
              </div>
              <div class="mb-4">
                <label for="register-password_confirmation" class="block text-gray-700">Confirm Password</label>
                <input type="password" id="register-password_confirmation" v-model="registerForm.password_confirmation"
                  required
                  class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300" />
              </div>
              <div v-if="registerError" class="mb-4 text-red-500">
                {{ registerError }}
              </div>
              <button type="submit" class="w-full btn-primary">
                Register
              </button>
            </form>
          </div>
        </div>

        <!-- If Authenticated: Show User Info and Logout -->
        <div v-else>
          <div class="px-6 py-4">
            <p class="text-gray-800">
              Hello, <strong>{{ user.name }}</strong>
            </p>
            <p class="text-gray-600">{{ user.email }}</p>
          </div>
          <div class="border-t border-gray-200">
            <a :href="`/profile`" class="block px-6 py-2 text-gray-700 hover:bg-gray-100">
              Profile
            </a>
            <a :href="`/account/settings`" class="block px-6 py-2 text-gray-700 hover:bg-gray-100">
              Account Settings
            </a>
            <button @click="logout"
              class="w-full text-left px-6 py-2 text-gray-700 hover:bg-gray-100 focus:outline-none">
              Logout
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Import necessary functions and stores
import { ref, computed } from 'vue';
import { useUserStore } from '@/stores/modules/user'; 

const userStore = useUserStore();

// Reactive references
const isOpen = ref(false);
const activeTab = ref('login'); // 'login' or 'register'
const loginForm = ref({
  email: '',
  password: '',
});
const registerForm = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});
const loginError = ref('');
const registerError = ref('');

// Computed properties
const isAuthenticated = computed(() => userStore.isAuthenticated);
const user = computed(() => userStore.user);

// Classes for active/inactive tabs
const activeClass = 'border-b-2 border-blue-500 text-blue-500';
const inactiveClass = 'text-gray-500 hover:text-gray-700';

// Methods
const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

const login = async () => {
  try {
    await userStore.login(loginForm.value);
    isOpen.value = false;
  } catch (error) {
    loginError.value = error.response?.data?.message || 'Login failed';
  }
};

const register = async () => {
  try {
    await userStore.register(registerForm.value);
    isOpen.value = false;
  } catch (error) {
    registerError.value = error.response?.data?.message || 'Registration failed';
  }
};

const logout = async () => {
  try {
    await userStore.logout();
    isOpen.value = false;
  } catch (error) {
    console.error('Logout failed:', error);
    // Optionally, display an error message to the user
  }
};
</script>

<style scoped>
/* Dropdown styling */
.relative {
  position: relative;
}

.absolute {
  position: absolute;
}

.shadow-lg {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
    0 4px 6px -4px rgba(0, 0, 0, 0.1);
}

.z-50 {
  z-index: 50;
}

/* Optional: Transition for dropdown */
div[v-cloak] {
  display: none;
}

/* Additional Styles */
button {
  cursor: pointer;
}

.btn-primary {
  @apply bg-primary-500 text-white hover:bg-primary-600 rounded px-4 py-2;
}

.btn-secondary {
  @apply bg-secondary-500 text-white hover:bg-secondary-600 rounded px-4 py-2;
}
</style>