<!-- File: resources/js/views/Shop/DetailsPage.vue -->

<template>
    <DefaultLayout>
      <div class="container mx-auto py-8 px-4">
        <h1 class="text-3xl font-bold mb-6 text-center">Delivery Details</h1>
        <div class="flex flex-col md:flex-row gap-6">
          <!-- Left Side: Delivery Address -->
          <div class="md:w-1/2">
            <div class="bg-white shadow rounded-lg p-6">
              <h2 class="text-2xl font-semibold mb-4">Shipping Address</h2>
              <form @submit.prevent="submitAddress">
                <div class="mb-4">
                  <label class="block text-gray-700">Full Name</label>
                  <input type="text" v-model="address.fullName" required class="w-full mt-1 p-2 border border-gray-300 rounded">
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700">Address Line 1</label>
                  <input type="text" v-model="address.address1" required class="w-full mt-1 p-2 border border-gray-300 rounded">
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700">Address Line 2</label>
                  <input type="text" v-model="address.address2" class="w-full mt-1 p-2 border border-gray-300 rounded">
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700">City</label>
                  <input type="text" v-model="address.city" required class="w-full mt-1 p-2 border border-gray-300 rounded">
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700">Postal Code</label>
                  <input type="text" v-model="address.postalCode" required class="w-full mt-1 p-2 border border-gray-300 rounded">
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700">Country</label>
                  <input type="text" v-model="address.country" required class="w-full mt-1 p-2 border border-gray-300 rounded">
                </div>
                <button type="submit" class="btn-primary w-full">Save Address</button>
              </form>
            </div>
          </div>
          <!-- Right Side: Delivery Information -->
          <div class="md:w-1/2">
            <div class="bg-white shadow rounded-lg p-6">
              <h2 class="text-2xl font-semibold mb-4">Delivery Information</h2>
              <p class="text-gray-700 mb-2"><strong>Free delivery via DPD</strong> on orders over £25 for UK mainland delivery addresses.</p>
              <p class="text-gray-700"><strong>Estimated delivery:</strong> Fri 3rd Jan</p>
              <p class="text-gray-600 mt-4">Delivery disclaimer: Additional charges may apply for deliveries outside the UK mainland.</p>
              <button class="btn-primary mt-6 w-full" @click="viewBasket">View Basket</button>
            </div>
          </div>
        </div>
      </div>
    </DefaultLayout>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import DefaultLayout from '@/components/layout/DefaultLayout.vue';
  
  const address = ref({
    fullName: '',
    address1: '',
    address2: '',
    city: '',
    postalCode: '',
    country: '',
  });
  
  const submitAddress = () => {
    // Placeholder function for submitting address
    alert('Address saved successfully!');
  };
  
  const viewBasket = () => {
    // Navigate to Basket Page
    // Assuming you have access to router
    // Replace with actual navigation logic as needed
    window.location.href = '/shop/basket';
  };
  </script>
  
  <style scoped>
  .btn-primary {
    @apply bg-primary-500 text-white hover:bg-primary-600 rounded px-4 py-2;
  }
  
  .img-inout-effect-1 {
    @apply relative;
  }
  
  .img-inout-effect-1 img {
    @apply transition-transform duration-300 ease-in-out;
  }
  
  .img-inout-effect-1:hover img {
    @apply scale-110;
  }
  
  .img-inout-effect-1::before,
  .img-inout-effect-1::after {
    @apply absolute inset-0 flex items-center justify-center;
    content: "";
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
  }
  
  .img-inout-effect-1:hover::before,
  .img-inout-effect-1:hover::after {
    @apply opacity-100;
  }
  
  .img-inout-effect-1 div {
    @apply absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-300;
  }
  </style>