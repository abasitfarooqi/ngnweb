<template>

<div class="account-information-container">
    <div class="container account-information-container" style="margin-top: -32px;padding-top: 62px;">
        <div class="flex flex-col lg:flex-row gap-3">
            <div class="w-full lg:w-1/6 mb-6 lg:mb-0">
                <Sidebar />
            </div>
            <div class="w-full lg:w-3/4 mt-0 m-0 ngn-box-style-container">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold">Change Password</h2>
                </div>
                <form @submit.prevent="changePassword">
                    <div class="mb-0">
                        <label for="current_password" class="block mb-2 text-sm">Current Password</label>
                        <input type="password" id="current_password" 
                            class="w-full focus:ring-2 ngn-input-sm" 
                            autocomplete="current-password"
                            v-model="currentPassword" required />
                    </div>
                    <div class="mb-0">
                        <label for="new_password" class="block mb-2 text-sm">New Password</label>
                        <input type="password" id="new_password" 
                            class="w-full focus:ring-2 ngn-input-sm" 
                            autocomplete="new-password"
                            v-model="newPassword" required />
                    </div>
                    <div class="mb-0">
                        <label for="new_password_confirmation" class="block mb-2 text">Confirm New Password</label>
                        <input type="password" id="new_password_confirmation" 
                            class="w-full focus:ring-2 ngn-input-sm"
                            autocomplete="new-password"
                            v-model="newPasswordConfirmation" required />
                    </div>
                    <button type="submit" 
                        class="px-4 py-2 ngn-btn-sm">
                        Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
import Sidebar from '@/components/accountinformation/Sidebar.vue';

const currentPassword = ref('');
const newPassword = ref('');
const newPasswordConfirmation = ref('');

const changePassword = () => {
    // Handle password change logic here
    console.log('Changing password...');
};

</script>

<style scoped>
.container {
    margin-top: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.form-group input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

.ngn-btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 0.25rem;
    cursor: pointer;
    margin-right: 0.5rem;
}

</style>