<!-- resources/js/components/Shop/ProductGrid.vue -->
<template>
  <main class="product-grid-container">

    <!-- <h1 class="title font-bold font-three hidden lg:block md:block">All Products</h1> -->
    <div class="product-grid-header">
      <!-- Flex Container for Filters -->
      <div class="flex items-center gap-2">
        <!-- Search Bar -->
        <input type="text" :value="searchQuery" @input="$emit('update:searchQuery', $event.target.value)"
          placeholder="Search products..." class="w-full  " />

        <!-- Search Icon -->
        <div class="-ml-[29px] -mt-[18px] hidden sm:block">
          <svg class="h-5 w-5 text-gray-400 search-icon-shophome" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
            fill="currentColor">
            <path fill-rule="evenodd"
              d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
              clip-rule="evenodd" />
          </svg>
        </div>

        <!-- Sort By Select -->
        <div class="relative w-full ">
          <select :value="sortBy" @change="$emit('update:sortBy', $event.target.value)" class="w-full"
            :disabled="loading" style="appearance: auto;">
            <option value="newest">Newest First (Default)</option>
            <option value="price_low">Price: Low to High</option>
            <option value="price_high">Price: High to Low</option>
            <option value="name">Name: A to Z</option>
          </select>
        </div>

        <!-- Items Per Page Select -->
        <div class="relative w-full ml-0 ">
          <select :value="itemsPerPage" @change="$emit('update:itemsPerPage', Number($event.target.value))"
            class="w-full" :disabled="loading" style="appearance: auto;">
            <option value="">Items Per Page</option>
            <option :value="12">12 per page</option>
            <option :value="24">24 per page</option>
            <option :value="36">36 per page</option>
            <option :value="48">48 per page</option>
          </select>

        </div>
        <!-- Total Items Display -->
        <div class="text-sm text-gray-600 hidden lg:block md:block">
          <span class="h-text--primary-50">{{ totalItems }}</span> products
        </div>
      </div>

      <h1 class="font-bold font-three md:hidden" style="font-size: 1.5rem;margin-top: -10px;">All Products</h1>
      <!-- Total Items Display -->
      <div class="text-sm text-gray-600 mb-2 md:hidden">
        <span class="h-text--primary-50">{{ totalItems }}</span> products
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center min-h-[400px]">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="flex justify-center items-center min-h-[400px]">
      <div class="text-center">
        <p class="text-red-600 mb-4">{{ error }}</p>
        <button @click="$emit('retry')"
          class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
          Try Again
        </button>
      </div>
    </div>

    <!-- No Products Message -->
    <div v-else-if="products.length === 0" class="flex flex-col items-center justify-center min-h-[400px]">
      <p class="text-xl font-semibold text-gray-600">No results were found.</p>
      <p class="text-gray-500 mt-2">You can try adjusting your filters</p>
    </div>

    <!-- Product Grid -->
    <div v-else class="product-grid">
      <ProductCard v-for="product in products" :key="product.id" :product="product"
        @add-to-cart="$emit('add-to-cart', $event)" @add-to-wishlist="$emit('add-to-wishlist', $event)" />
    </div>

    <!-- Pagination -->
    <div class="pagination-container mt-8 flex justify-center">
      <div class="flex items-center gap-4">
        <button @click="$emit('update:currentPage', 1)" :disabled="currentPage === 1" class="pagination-button"
          :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
              d="M15.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 010 1.414zm-6 0a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 00-1.414 1.414L14.586 10l-4.293 4.293a1 1 0 000 1.414z"
              clip-rule="evenodd" />
          </svg>
        </button>

        <button @click="previousPage" :disabled="currentPage === 1" class="pagination-button"
          :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
              d="M12.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 1.414L8.414 10l4.293 4.293a1 1 0 010 1.414z"
              clip-rule="evenodd" />
          </svg>
        </button>
        <!-- pagination numbering -->
        <div class="flex gap-1 justify-center ">
          <button v-for="page in displayedPages" :key="page" @click="$emit('update:currentPage', page)"
            class="pagination-number ngn-btn"
            :class="{ 'bg-ngn-primary ngn-btn-black text-white hover:bg-ngn-primary-600': currentPage === page }">
            {{ page }}
          </button>
        </div>

        <button @click="nextPage" :disabled="currentPage === totalPages" class="pagination-button"
          :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
              d="M7.293 15.707a1 1 0 001.414 0l5-5a1 1 0 000-1.414l-5-5a1 1 0 00-1.414 1.414L14.586 10l-4.293 4.293a1 1 0 000 1.414z"
              clip-rule="evenodd" />
          </svg>
        </button>

        <button @click="$emit('update:currentPage', totalPages)" :disabled="currentPage === totalPages"
          class="pagination-button" :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
              d="M4.293 15.707a1 1 0 001.414 0l5-5a1 1 0 000-1.414l-5-5a1 1 0 00-1.414 1.414L8.586 10 4.293 14.293a1 1 0 000 1.414zm6 0a1 1 0 001.414 0l5-5a1 1 0 000-1.414l-5-5a1 1 0 00-1.414 1.414L14.586 10l-4.293 4.293a1 1 0 000 1.414z"
              clip-rule="evenodd" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Add Toast component here -->
    <Toast />
  </main>
</template>

<script setup>
import { computed } from 'vue';
import ProductCard from './ProductCard.vue';
import Toast from '@/components/ui/Toast.vue';

const props = defineProps({
  products: { type: Array, required: true },
  loading: { type: Boolean, required: true },
  error: { type: String, default: null },
  currentPage: { type: Number, required: true },
  totalPages: { type: Number, required: true },
  totalItems: { type: Number, required: true },
  itemsPerPage: { type: Number, required: true },
  sortBy: { type: String, required: true },
  searchQuery: { type: String, default: '' }
});

const emit = defineEmits([
  'update:currentPage',
  'update:itemsPerPage',
  'update:sortBy',
  'retry',
  'add-to-cart',
  'add-to-wishlist',
  'update:searchQuery'
]);

const displayedPages = computed(() => {
  const delta = 2;
  const range = [];
  const rangeWithDots = [];
  let l;

  range.push(1);

  if (props.totalPages <= 1) return range;

  for (let i = props.currentPage - delta; i <= props.currentPage + delta; i++) {
    if (i < props.totalPages && i > 1) {
      range.push(i);
    }
  }
  range.push(props.totalPages);

  for (let i of range) {
    if (l) {
      if (i - l === 2) {
        rangeWithDots.push(l + 1);
      } else if (i - l !== 1) {
        rangeWithDots.push('...');
      }
    }
    rangeWithDots.push(i);
    l = i;
  }

  return rangeWithDots;
});

const previousPage = () => {
  if (props.currentPage > 1) {
    emit('update:currentPage', props.currentPage - 1);
  }
};

const nextPage = () => {
  if (props.currentPage < props.totalPages) {
    emit('update:currentPage', props.currentPage + 1);
  }
};
</script>

<style scoped>
@import '@/assets/styles/ProductGrid.module.css';
</style>