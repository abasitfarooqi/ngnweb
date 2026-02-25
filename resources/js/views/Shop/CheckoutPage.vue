<!-- File: resources/js/views/Shop/CheckoutPage.vue -->

<template>
  <DefaultLayout>
    <div class="container mx-auto py-8 px-4">
      <h2 class="text-2xl font-bold mb-6 text-center">Order Checkout</h2>
      <div class="flex flex-col md:flex-row gap-6">
        <!-- Checkout Steps Section -->
        <div class="md:w-3/4">
          <CheckoutSteps :totalPrice="totalPrice" />
        </div>

        <!-- Order Summary Section -->
        <div class="md:w-1/4">
          <OrderSummeryCheckout @update-total="updateTotalPrice" />
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup>
import { ref } from 'vue';
import DefaultLayout from '@/components/layout/DefaultLayout.vue';
import OrderSummeryCheckout from '@/components/Shop/OrderSummeryCheckout.vue';
import CheckoutSteps from '@/components/Shop/CheckoutSteps.vue';

// Add a reactive variable to store the total price
const totalPrice = ref(0);

// Function to update the total price
function updateTotalPrice(newTotal) {
    totalPrice.value = newTotal;
}
</script>

<style scoped>
@import '@/assets/styles/CheckoutPage.module.css';
</style>