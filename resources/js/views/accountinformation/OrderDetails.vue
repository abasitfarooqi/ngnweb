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
              <i class="fa fa-receipt mr-2"></i> Order Details - {{ order.id }}
            </h2>
          </div>
          <div class="bg-gray-100 p-4 rounded-lg shadow-md">
            <div class="mb-4">
              <p class="text-gray-600 flex items-center">
                <i class="fa fa-check-circle mr-2"></i> Order Status:
                <span class="font-medium ml-1 text-green-600">{{ order.order_status }}</span>
              </p>
              <p class="text-gray-600 flex items-center ml-[-15px] mt-[5px] font-size-28 active-color font-bold">
                <i class="fa fa-dollar-sign mr-2"></i> Total Cost:
                <span class="font-medium ml-1">£{{ order.total_amount }}</span>
              </p>
            </div>
            <div>
              <h3 class="text-xl font-semibold mb-3 flex items-center ml-[-15px] mt-[5px] font-size-28 font-bold">
                <i class="fa fa-box-open mr-2"></i> Items
              </h3>
              <div class="divide-y divide-gray-200">
                <div v-for="item in order.items" :key="item.id" class="py-2">
                  <div class="flex justify-between items-center">
                    <span>{{ item.product_name }}</span>
                    <span>£{{ Number(item.unit_price).toFixed(2) }} (Qty: {{ item.quantity }})</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-4 flex gap-2">
            <router-link :to="{ name: 'OrdersIndex' }" class="ngn-btn-shape effect-on-btn ngn-bg">
              <i class="fa fa-arrow-left mr-2"></i> Back to Orders
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Sidebar from '@/components/accountinformation/Sidebar.vue';
import { shopAPI } from '@/services/api';

const route = useRoute();
const router = useRouter();
const order = ref({});
const canCancelOrder = ref(false);

onMounted(async () => {
  try {
    const response = await shopAPI.getOrderById(route.params.id);
    order.value = response;
    const orderDate = new Date(order.value.created_at);
    const now = new Date();
    const diffInHours = (now - orderDate) / (1000 * 60 * 60);
    canCancelOrder.value = diffInHours <= 24;
  } catch (error) {
    console.error('Error fetching order details:', error);
  }
});

const goBack = () => {
  router.back();
};

// const cancelOrder = async () => {
//   try {
//     await shopAPI.cancelOrder(order.value.id);
//     alert('Order cancelled successfully');
//     router.push({ name: 'OrdersIndex' });
//   } catch (error) {
//     console.error('Error cancelling order:', error);
//     const errorMessage = error.response?.data?.message || 'Failed to cancel order. Please try again later.';
//     alert(errorMessage);
//   }
// };
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