<!-- File: resources/js/components/Shop/ProductCartCard.vue -->
<template>
  <div class="product-cart-card border rounded-lg p-4 mb-4 bg-white shadow-sm">
    <div>
      <p class="text-sm text-gray-500 pt-0 pb-2">
        Hurry! Items in bag aren't reserved! Don't miss out and checkout now!
      </p>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-start sm:space-x-4">
      <!-- Product Image -->
      <div class="relative w-full sm:w-24 h-40 sm:h-24 flex-shrink-0 mb-4 sm:mb-0">
        <img :src="imageUrl" :alt="product.name"
          class="absolute inset-0 w-full h-full object-contain sm:object-cover rounded-md" @error="handleImageError" />
      </div>

      <!-- Product Details -->
      <div class="flex-grow min-w-0">
        <div class="flex justify-between items-start">
          <div class="flex-grow min-w-0">
            <router-link :to="`/shop/product/${product.slug}`" class="text-lg font-medium text-gray-900 mb-1 truncate">
              <h3 class="text-lg font-medium text-gray-900 mb-1 truncate">{{ product.name }}</h3>
              <!-- <p class="text-sm text-gray-500">SKU: {{ product.sku }}</p> -->
              <p class="text-sm text-gray-500">Size: {{ product.variation }}</p>
            </router-link>
            <p class="text-sm text-gray-500">SKU: {{ product.sku }}</p>
          </div>
          <button @click="handleRemove" class="text-red-500 hover:text-red-700 focus:outline-none ml-2 flex-shrink-0"
            title="Remove item">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
          </button>
        </div>

        <!-- Price and Quantity Controls -->
        <div class="mt-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 sm:gap-2">
          <div class="flex items-baseline space-x-2">
            <span class="text-lg font-medium text-gray-900">
              £{{ product.discount_price || product.normal_price }}
            </span>
            <span v-if="product.discount_price" class="text-sm text-gray-500 line-through">
              £{{ product.normal_price }}
            </span>
          </div>

          <div class="flex items-center">
            <button @click="updateItemQuantity(quantity - 1)"
              class="w-8 h-8 rounded-l bg-gray-100 flex items-center justify-center hover:bg-gray-200 border-y border-l border-gray-200 transition-colors"
              :disabled="quantity <= 1" :class="{ 'opacity-50 cursor-not-allowed': quantity <= 1 }">
              <span class="text-gray-600 font-medium">-</span>
            </button>
            <div
              class="w-12 h-8 flex items-center justify-center bg-white border-y border-gray-200 font-medium text-gray-700">
              {{ quantity }}
            </div>
            <button @click="updateItemQuantity(quantity + 1)"
              class="w-8 h-8 rounded-r bg-gray-100 flex items-center justify-center hover:bg-gray-200 border-y border-r border-gray-200 transition-colors">
              <span class="text-gray-600 font-medium">+</span>
            </button>
          </div>
        </div>

        <!-- Stock Status -->
        <div class="mt-2">
          <span v-if="product.global_stock > 0" class="text-sm text-green-600">
            In Stock ({{ product.global_stock }} available)
          </span>
          <span v-else class="text-sm text-red-600">
            Out of Stock
          </span>
        </div>

        <!-- Variation Selection -->
        <div v-if="product.variations && product.variations.length > 0" class="mt-2">
          <label class="block text-sm font-medium text-gray-700">Select Variation:</label>
          <select v-model="selectedVariation" @change="updateVariation"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-primary-500">
            <option v-for="variation in product.variations" :key="variation.id" :value="variation.id">
              {{ variation.name }} - £{{ variation.price }}
            </option>
          </select>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';

interface Variation {
  id: number;
  name: string;
  price: number;
}

interface Product {
  id: number;
  name: string;
  sku: string;
  image_url: string;
  normal_price: number;
  discount_price?: number;
  global_stock: number;
  slug: string; // Ensure slug is included
  variation: string; // Variations structure
}

const props = defineProps<{
  product: Product;
  quantity: number;
}>();

const emit = defineEmits<{
  (e: 'update-quantity', value: number): void;
  (e: 'remove-item'): void;
}>();

const imageUrl = computed(() => {
  if (!props.product.image_url || props.product.image_url === '') {
    return 'https://neguinhomotors.co.uk/assets/img/no-image.png';
  }
  return props.product.image_url;
});

// Selected variation
const selectedVariation = ref(props.product.variations && props.product.variations.length > 0 ? props.product.variations[0].id : null);

// Handle image error
const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement;
  img.src = 'https://neguinhomotors.co.uk/assets/img/no-image.png';
};

// Update item quantity
const updateItemQuantity = (newQuantity: number) => {
  if (newQuantity >= 1) {
    emit('update-quantity', newQuantity);
  }
};

// Update variation
const updateVariation = () => {
  // Logic to handle variation change if needed
};

// Handle remove item
const handleRemove = () => {
  emit('remove-item');
};
</script>

<style scoped>
.product-cart-card input[type="number"]::-webkit-inner-spin-button,
.product-cart-card input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.product-cart-card input[type="number"] {
  -moz-appearance: textfield;
}
</style>