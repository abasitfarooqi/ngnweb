<!-- File: resources/js/views/Shop/PaymentPage.vue -->
<!-- it will be deleted after payment page is done -->
<template>
  <DefaultLayout>
    <div class="container mx-auto py-8 px-4">
      <h1 class="text-3xl font-bold mb-6 text-center">Payment</h1>
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-4">Select Payment Method</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="method in paymentMethods"
            :key="method.id"
            class="flex items-center space-x-4 p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100"
            @click="selectMethod(method)"
          >
            <input
              type="radio"
              :id="method.name"
              name="payment"
              :value="method.name"
              v-model="selectedMethod"
              class="form-radio h-5 w-5 text-primary-500"
            />
            <label :for="method.name" class="flex items-center space-x-2">
              <img :src="method.icon" :alt="method.name" class="w-8 h-8">
              <span>{{ method.name }}</span>
            </label>
          </div>
        </div>
        <button class="btn-primary mt-6 w-full" @click="proceedToPayment" :disabled="!selectedMethod || loading">
          Proceed to Payment
        </button>
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import DefaultLayout from '@/components/layout/DefaultLayout.vue';
import { useRouter } from 'vue-router';
import { shopAPI } from '@/services/api';
import { useCartStore } from '@/stores/modules/cart';
import { useShippingStore } from '@/stores/modules/shipping';
import { useToastStore } from '@/stores/modules/toast';

const router = useRouter();
const cartStore = useCartStore();
const shippingStore = useShippingStore();
const toastStore = useToastStore();

const paymentMethods = ref([
  {
    id: 1,
    name: 'Credit/Debit Card',
    icon: '/assets/images/payments/card.png',
  },
  {
    id: 2,
    name: 'Google Pay',
    icon: '/assets/images/payments/google-pay.png',
  },
  {
    id: 3,
    name: 'Klarna Pay Now',
    icon: '/assets/images/payments/klarna.png',
  },
  {
    id: 4,
    name: 'Cash on Delivery',
    icon: '/assets/images/payments/cod.png',
  },
  {
    id: 5,
    name: 'Cash on Pickup',
    icon: '/assets/images/payments/cash-pickup.png',
  },
]);

const selectedMethod = ref('');
const loading = ref(false);

const selectMethod = (method) => {
  selectedMethod.value = method.name;
};

const proceedToPayment = async () => {
  if (selectedMethod.value) {
    loading.value = true;
    try {
      const orderData = {
        payment_method: selectedMethod.value,
        shipping_method: shippingStore.method,
        shipping_details: shippingStore.method === 'home_delivery' ? shippingStore.homeDelivery : shippingStore.storePickup,
        cart_items: cartStore.items,
      };
      const response = await shopAPI.placeOrder(orderData);
      // Assuming the API returns an order ID or confirmation
      toastStore.triggerToast('Order placed successfully!', 'success');
      router.push({ name: 'orderConfirmation', params: { orderId: response.data.orderId } });
    } catch (error) {
      toastStore.triggerToast('Failed to process payment.', 'error');
      console.error('Payment error:', error);
    } finally {
      loading.value = false;
    }
  } else {
    toastStore.triggerToast('Please select a payment method.', 'warning');
  }
};
</script>

<style scoped>
.btn-primary {
  @apply bg-primary-500 text-white hover:bg-primary-600 rounded px-4 py-2;
}
</style>