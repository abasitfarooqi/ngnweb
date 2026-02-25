<template>
<div class="account-information-container">
        <div class="container account-information-container" style="margin-top: -32px;padding-top: 62px;">
            <div class="flex flex-col lg:flex-row gap-3">
                <div class="w-full lg:w-1/6 mb-6 lg:mb-0">
                    <Sidebar />
                </div>
                <div class="w-full lg:w-3/4 mt-0 m-0 ngn-box-style-container">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-semibold">Your Payment Methods</h2>
                    </div>
                    <div class="">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Card Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Card Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="paymentMethod in paymentMethods" :key="paymentMethod.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ paymentMethod.card_type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">**** **** **** {{ paymentMethod.card_number.slice(-4) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ paymentMethod.expiry_date }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <router-link :to="{ name: 'PaymentMethodManage', params: { id: paymentMethod.id }}"
                                            class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700">
                                            Manage
                                        </router-link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</div>

</template>

<script setup>
import { ref, onMounted } from 'vue';
import Sidebar from '@/components/accountinformation/Sidebar.vue';

const paymentMethods = ref([]);

onMounted(async () => {
    // TODO: Replace with actual API call
    paymentMethods.value = [
        {
            id: 1,
            card_type: 'Visa',
            card_number: '4111111111111111',
            expiry_date: '12/24'
        }
    ];
});

</script>

<style scoped>
.container {
    margin-top: 2rem;
}

.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
}

.table th,
.table td {
    padding: 0.75rem;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}

.table th {
    vertical-align: bottom;
    border-bottom: 2px solid #dee2e6;
}

.btn-info {
    background-color: #17a2b8;
    color: #fff;
    padding: 0.375rem 0.75rem;
    border: none;
    border-radius: 0.25rem;
    text-decoration: none;
}

.btn-info:hover {
    background-color: #138496;
}
</style>