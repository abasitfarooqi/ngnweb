<template>
  <div class="account-information-container">
    <div class="container account-information-container" style="margin-top: -32px;padding-top: 62px;">
      <div class="flex flex-col lg:flex-row gap-3">
        <div class="w-full lg:w-1/6 mb-6 lg:mb-0">
          <Sidebar />
        </div>
        <div class="w-full lg:w-3/4 mt-0 m-0 ngn-box-style-container">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold flex items-center">
              <i class="fa fa-boxes mr-1"></i> Your Orders
            </h2>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <i class="fa fa-hashtag mr-1"></i> Order No
                  </th>
                  <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <i class="fa fa-info-circle mr-1"></i> Status
                  </th>
                  <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <i class="fa fa-info-circle mr-1"></i> Order Date
                  </th>
                  <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <i class="fa fa-dollar-sign mr-1"></i> Total Cost
                  </th>
                  <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <i class="fa fa-info-circle mr-1"></i> Shipping Method
                  </th>
                  <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <i class="fa fa-cogs mr-1"></i> Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-100">
                  <td class="px-6 py-1 whitespace-nowrap">{{ order.id }}</td>
                  <td class="px-6 py-1 whitespace-nowrap">{{ order.order_status }}</td>
                  <td class="px-6 py-1 whitespace-nowrap">
                    {{ formatDate(order.order_date) }}
                  </td>
                  <td class="px-6 py-1 whitespace-nowrap">£{{ Number(order.grand_total).toFixed(2) }}</td>
                  <td class="px-6 py-1 whitespace-nowrap">
                    {{ order.shipping_method.name }}
                    <div class="text-xs text-gray-500">{{ order.branch.name + ' - ' + order.branch.address }}</div>
                  </td>

                  <td class="px-6 py-1 whitespace-nowrap">
                    <router-link :to="{ name: 'OrderDetails', params: { id: order.id } }"
                      class="ngn-btn-shape effect-on-btn ngn-bg">
                      <i class="fa fa-eye mr-1"></i> View
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
import { shopAPI } from '@/services/api';

const orders = ref([]);

onMounted(async () => {
  try {
    const response = await shopAPI.getCustomerOrders();
    orders.value = response;
  } catch (error) {
    console.error('Error fetching orders:', error);
  }
});

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}
</script>

<style scoped>
.container {
  margin-top: 2rem;
}

.ngn-btn-shape {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  background-color: #17a2b8;
  color: #fff;
  text-decoration: none;
  transition: background-color 0.3s;
}

.ngn-btn-shape:hover {
  background-color: #138496;
}
</style>