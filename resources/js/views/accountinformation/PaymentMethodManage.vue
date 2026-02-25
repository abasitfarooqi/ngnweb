<template>
    <DefaultLayout>
        <div class="account-information-container">
            <div class="container account-information-container" style="margin-top: -32px;padding-top: 62px;">
                <div class="flex flex-col lg:flex-row gap-3">
                    <div class="w-full lg:w-1/6 mb-6 lg:mb-0">
                        <Sidebar />
                    </div>
                    <div class="w-full lg:w-3/4 mt-0 m-0 ngn-box-style-container">
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-credit-card text-2xl text-blue-600"></i>
                                <h2 class="text-2xl font-semibold">Manage Payment Method</h2>
                            </div>
                        </div>
                        <div class="p-6">
                            <form @submit.prevent="updatePaymentMethod">
                                <div class="mb-4 relative">
                                    <label for="card_number" class="block mb-2 font-medium">
                                        <i class="far fa-credit-card mr-2"></i>Card Number
                                    </label>
                                    <input type="text" id="card_number" v-model="cardNumber" required 
                                        class="w-full p-2 pl-10 border rounded focus:ring-2 focus:ring-blue-500 ngn-input-sm" 
                                        placeholder="1234 5678 9012 3456"/>
                                    <i class="fas fa-credit-card absolute left-3 top-[38px] text-gray-400"></i>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="mb-4 relative">
                                        <label for="expiry_date" class="block mb-2 font-medium">
                                            <i class="far fa-calendar-alt mr-2"></i>Expiry Date
                                        </label>
                                        <input type="text" id="expiry_date" v-model="expiryDate" required
                                            class="w-full p-2 pl-10 border rounded focus:ring-2 focus:ring-blue-500 ngn-input-sm"
                                            placeholder="MM/YY" />
                                        <i class="far fa-calendar absolute left-3 top-[38px] text-gray-400"></i>
                                    </div>
                                    <div class="mb-4 relative">
                                        <label for="cvv" class="block mb-2 font-medium">
                                            <i class="fas fa-lock mr-2"></i>CVV
                                        </label>
                                        <input type="password" id="cvv" v-model="cvv" required maxlength="4"
                                            class="w-full p-2 pl-10 border rounded focus:ring-2 focus:ring-blue-500 ngn-input-sm"
                                            placeholder="***" />
                                        <i class="fas fa-shield-alt absolute left-3 top-[38px] text-gray-400"></i>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <button type="submit" class="ngn-btn-sm flex items-center gap-2">
                                        <i class="fas fa-save"></i>
                                        Update Payment Method
                                    </button>
                                    <button type="button" class="ngn-btn-shape effect-on-btn ngn-bg flex items-center gap-2">
                                        <i class="fas fa-times"></i>
                                        Cancel
                                    </button>
                                </div>
                            </form>
                            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                                <div class="flex items-center gap-2 text-blue-600">
                                    <i class="fas fa-info-circle"></i>
                                    <p class="text-sm">Your payment information is securely encrypted and stored</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>

<script setup>
import { ref } from 'vue';
import Sidebar from '@/components/accountinformation/Sidebar.vue';

const cardNumber = ref('');
const expiryDate = ref('');
const cvv = ref('');

const updatePaymentMethod = () => {
    // Handle form submission
    console.log('Updating payment method...');
}
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
}

.ngn-btn-primary {
    background-color: #007bff;
    color: #fff;
}

.ngn-btn-primary:hover {
    background-color: #0056b3;
}
</style>