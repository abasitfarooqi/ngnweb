<template>
<div class="account-information-container">
    <div class="container account-information-container" style="margin-top: -32px;padding-top: 62px;">
        <div class="flex flex-col lg:flex-row gap-3">
            <div class="w-full lg:w-1/6 mb-6 lg:mb-0">
                <Sidebar />
            </div>
            <div class="w-full lg:w-3/4 mt-0 m-0 ngn-box-style-container">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-2xl font-semibold">User Profile</h2>
                </div>
                <div class="p-1">
                    <form @submit.prevent="updateProfile">
                        <div class="mb-0">
                            <label for="first_name" class="block mb-2 text-sm">First Name</label>
                            <input type="text" id="first_name" v-model="customer.first_name" required
                                class="w-full focus:ring-2 ngn-input-sm" />
                        </div>
                        <div class="mb-0">
                            <label for="last_name" class="block mb-2 text-sm">Last Name</label>
                            <input type="text" id="last_name" v-model="customer.last_name" required
                                class="w-full focus:ring-2 ngn-input-sm" />
                        </div>
                        <div class="mb-0">
                            <label for="email" class="block mb-2 text-sm">Email</label>
                            <input type="email" id="email" v-model="customer.email" required
                                class="w-full focus:ring-2 ngn-input-sm" />
                        </div>
                        <div class="mb-0">
                            <label for="phone" class="block mb-2 text-sm">Phone</label>
                            <input type="text" id="phone" v-model="customer.phone" required
                                class="w-full focus:ring-2 ngn-input-sm" />
                        </div>
                        <div class="flex space-x-4">
                            <button type="submit" class="ngn-btn-sm ">
                                Update Profile
                            </button>
                            <router-link to="/accountinformation/change-password"
                                class="ngn-btn-shape effect-on-btn ngn-bg">
                                Change Password
                            </router-link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router'; // Import useRoute to access route parameters
import Sidebar from '@/components/accountinformation/Sidebar.vue';
import { authAPI } from '@/services/api'; // Import the authAPI

// Define reactive variables
const customer = ref({
    first_name: '',
    last_name: '',
    email: '',
    phone: ''
});

// Get the user ID from the route parameters
const route = useRoute();
const userId = route.params.id; // Assuming the route is defined with :id

// Load user data on component mount
onMounted(async () => {
    try {
        const response = await authAPI.getUserById(userId); // Fetch user data by ID
        Object.assign(customer.value, response.data); // Assign data to customer
    } catch (error) {
        console.error('Error loading user data:', error);
    }
});

// Handle profile update
const updateProfile = async () => {
    try {
        await authAPI.updateProfile(userId, {
            first_name: customer.value.first_name,
            last_name: customer.value.last_name,
            email: customer.value.email,
            phone: customer.value.phone
        });
        // Show success message or handle success case
    } catch (error) {
        console.error('Error updating profile:', error);
        // Handle error case
    }
};
</script>

<style scoped>
/* ... existing styles ... */
</style>