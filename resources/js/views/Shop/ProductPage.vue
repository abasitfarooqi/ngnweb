<!-- File: resources/js/views/Shop/ProductPage.vue -->
<template>
  <DefaultLayout>
    <div class="container mx-auto pb-5 pt-8 px-4">
      <!-- Loading Indicator -->
      <div v-if="isLoading" class="flex justify-center items-center h-64">
        <svg class="animate-spin h-10 w-10 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none"
          viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
      </div>

      <!-- Error Message -->
      <div v-else-if="error" class="flex justify-center items-center h-64">
        <div class="text-center">
          <p class="text-red-500 text-lg mb-4">{{ errorMessage }}</p>
          <button @click="getProduct" class="btn-primary">
            Retry
          </button>
        </div>
      </div>

      <!-- Product Content -->
      <div v-else class="flex flex-col lg:flex-row gap-8">
        <!-- Image Gallery -->
        <div class="w-full lg:w-1/2">
          <div class="main-image-container relative rounded-lg overflow-hidden">
            <img :src="currentImage" :alt="selectedVariation?.slug" class="main-image w-full h-96 object-contain" />
            <div
              class="absolute inset-0 flex items-centre justify-centre opacity-0 transition-opacity duration-300 bg-black bg-opacity-50 hover:opacity-100 cursor-pointer"
              @click="toggleZoom">
              <span class="text-white text-lg font-semibold">Click to Zoom</span>
            </div>
            <!-- Zoom Modal -->
            <div v-if="isZoomed" class="fixed inset-0 flex items-centre justify-centre bg-black bg-opacity-75 z-50"
              @click="toggleZoom" aria-modal="true" role="dialog" aria-labelledby="zoomedImage">
              <img :src="currentImage" :alt="selectedVariation?.slug"
                class="zoomed-image max-w-full max-h-full object-contain" id="zoomedImage" />
            </div>
          </div>

          <!-- Thumbnails -->
          <div class="thumbnail-container flex justify-centre mt-4 space-x-4">
            <div v-for="(img, index) in images" :key="index" class="thumbnail-wrapper cursor-pointer"
              @click="selectImage(img)">
              <img :src="img" :alt="`Thumbnail ${index + 1}`"
                class="thumbnail w-20 h-20 object-contain border-2 border-transparent rounded transition-colours duration-200"
                :class="{ 'border-primary-500': currentImage === img }" />
            </div>
          </div>
        </div>

        <!-- Product Details -->
        <div class="w-full lg:w-1/2 flex flex-col">
          <!-- Dynamic Product Title -->
          <h1 class="text-3xl md:text-4xl font-bold mb-2">
            {{ selectedVariation?.name || redundantData.meta_title }}
          </h1>

          <!-- SKU Display -->
          <p v-if="selectedVariation?.total_balance > 0" class="font-semibold mb-1 font-one">
            SKU: {{ selectedVariation?.sku }}
          </p>

          <!-- Message When All Variations Are Out of Stock -->
          <p v-if="!selectedVariation" class="text-md text-red-500 mb-4">
            All variations are currently out of stock.
          </p>

          <!-- Price Display -->
          <div class="mb-2">
            <span class="text-3xl font-semibold font-black text-active font-one" style="font-size: 35px !important;">
              £{{ selectedVariation?.discount_price || redundantData.normal_price }}
            </span>
            <span v-if="selectedVariation?.discount_price" class="text-xl font-semibold font-black text-active font-one" style="font-size: 35px !important;">
              £{{ redundantData.normal_price }}
            </span>
          </div>

          <!-- Color Information -->
          <div class="mb-4">
            <h2 class="font-semibold">Color</h2>
            <p class="mt-2 text-gray-600">{{ redundantData.colour }}</p>
          </div>

          <!-- Stock Information -->
          <div class="mb-4">
            <h2 class="font-semibold">Stock</h2>
            <p class="mt-2 text-gray-600">
              {{ selectedVariation?.total_balance > 0 ? 'In Stock' : 'Out of Stock' }}
            </p>
          </div>

          <!-- Size Selection -->
          <div class="mb-4">
            <h2 class="font-semibold">Size</h2>
            <div class="flex flex-wrap gap-2 mt-2">
              <button v-for="item in products" :key="item.id" @click="selectVariation(item)"
                :disabled="item.total_balance === 0" :class="[
                  'px-4 py-2 border focus:outline-none focus:ring-2 focus:ring-primary-500 font-medium',
                  item.total_balance === 0
                    ? 'bg-ngn-primary hover:bg-ngn-primary-1000 transition-colors ngn-btn '
                    : (selectedVariation?.id === item.id
                      ? 'bg-ngn-primary hover:bg-ngn-primary-1000 transition-colors ngn-btn ngn-btn ngn-bg border border-activate'
                      : 'bg-ngn-primary hover:bg-ngn-primary-1000 transition-colors ngn-btn  ')
                ]" :title="item.total_balance === 0 ? 'Out of Stock' : 'Select Variation'"
                :aria-disabled="item.total_balance === 0" role="button">
                {{ item.variation || 'One Size' }}
              </button>
            </div>
          </div>

          <!-- Description -->
          <div class="mb-4">
            <h2 class="font-semibold">Description</h2>
            <p class="mt-2 text-gray-600">{{ redundantData.description }}</p>
          </div>

          <!-- Add to Basket Button -->
          <div class="mt-auto">
            <button
              class="bg-ngn-primary hover:bg-ngn-primary-1000 transition-colors ngn-btn  duration-300 flex items-center justify-center"
              @click="addToBasket"
              :disabled="!selectedVariation || isAddingToCart || (selectedVariation.total_balance <= 0)"
              style="border-radius: 0">
              <svg v-if="isAddingToCart" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
              </svg>
              {{
                isAddingToCart ? 'ADDING TO CART...' : (selectedVariation?.total_balance > 0 ? 'ADD TO CART' :
                  'OUT OF STOCK') }}
            </button>
          </div>
        </div>
      </div>


    </div>
    <div class="description-section" style="background:#efefef;margin:0;">
      <div class="container py-4">
        <!-- Tabs for Additional Information -->
        <div v-if="!isLoading && !error" class="" style="">
          <div class="border-b border-gray-300" style="">
            <nav class="-mb-px flex ">
              <button
                :class="currentTab === 'Description' ? 'text-xl text-active' : 'text-xl bg-transparent text-black'"
                class="whitespace-nowrap py-2 px-5 text-xl font-medium font-one border-b-2"
                :style="currentTab === 'Description' ? 'border-bottom: 2px solid #C31924;font-size:20px;letter-spacing:1.3px;' : 'letter-spacing:1.3px;font-size:20px;border-bottom: 2px solid transparent;'"
                @click="currentTab = 'Description'">
                Description
              </button>
              <button
                :class="currentTab === 'Specs' ? 'text-xl text-active' : 'text-xl bg-transparent text-black'"
                class="whitespace-nowrap py-2 px-5 text-xl font-medium font-one border-b-2"
                :style="currentTab === 'Specs' ? 'border-bottom: 2px solid #C31924;font-size:20px;letter-spacing:1.3px;' : 'letter-spacing:1.3px;font-size:20px;border-bottom: 2px solid transparent;'"
                @click="currentTab = 'Specs'">
                Specifications
              </button>
            </nav>
          </div>
          <div class="px-2 py-3">
            <div v-if="currentTab === 'Description'">
              <div class="text-black-700 text-black" v-html="redundantData.extended_description"></div>
            </div>
            <div v-if="currentTab === 'Specs'">
              <ul class="list-disc list-inside text-black text-black-700">
                <li><strong>Normal Price:</strong> £{{ redundantData.normal_price }}</li>
                <li><strong>Stock:</strong> {{ redundantData.global_stock }}</li>
                <li><strong>Meta Title:</strong> {{ redundantData.meta_title }}</li>
                <li><strong>Meta Description:</strong> {{ redundantData.meta_description }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DefaultLayout>

  <!-- Cart Success Modal -->
  <div v-if="showCartModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity backdrop-blur-sm"></div>

    <!-- Modal panel -->
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="relative bg-white  max-w-md w-full p-6 pb-1 shadow-xl transform transition-all">
        <!-- Close button -->
        <button @click="showCartModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <!-- Success message -->
        <div class="text-center mb-6">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h3 class="text-xl font-medium text-gray-900 mb-2">Item Added to Cart!</h3>
          <p class="text-sm text-gray-500">Your item has been successfully added to your shopping cart.</p>
        </div>

        <!-- Action buttons -->
        <div class="flex flex-col gap-3 sm:flex-row sm:gap-4">
          <button @click="continueShopping"
            class="flex-1 px-1 py-1 transition-colors duration-200 bg-ngn-primary hover:bg-ngn-primary-1000 transition-colors ngn-btn ">
            Continue Shopping
          </button>
          <button @click="goToCheckout"
            class="flex-1 px-1 py-1 transition-colors duration-200 bg-ngn-primary hover:bg-ngn-primary-1000 transition-colors ngn-btn ngn-bg">
            Checkout
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import DefaultLayout from '@/components/layout/DefaultLayout.vue';
import { shopAPI } from '@/services/api';
import { useToastStore } from '@/stores/modules/toast';
import { useCartStore } from '@/stores/modules/cart';
import { useRouter } from 'vue-router';
import { useUserStore } from '@/stores/modules/user';

const router = useRouter();
const showCartModal = ref(false);
const currentTab = ref('Description');
const product = ref({});
const redundantData = ref({});
const products = ref([]);
const selectedVariation = ref(null);
const isLoading = ref(false);
const isAddingToCart = ref(false);
const error = ref(false);
const errorMessage = ref('');
const isZoomed = ref(false);

// Image states
const images = ref([]);
const currentImage = ref('');

const toastStore = useToastStore();
const cartStore = useCartStore();
const userStore = useUserStore();

// Initialize cart on component mount
onMounted(() => {
  cartStore.initializeCart();
  getProduct();
});

const props = defineProps({
  slug: {
    type: String,
    required: true,
  },
});

const getProduct = async () => {
  isLoading.value = true;
  error.value = false;
  errorMessage.value = '';

  try {
    // Add 1 second delay
    await new Promise(resolve => setTimeout(resolve, 1000));
    const data = await shopAPI.getProductBySlug(props.slug);
    product.value = data;
    redundantData.value = data.redundantData;
    products.value = data.products;

    // Select the first available variation with total_balance > 0
    selectedVariation.value = products.value.find(p => p.total_balance > 0) || null;

    // If no image_array or image_url is provided, use a placeholder
    if (!data.redundantData.image_array && !data.redundantData.image_url && !product.value.image_url) {
      images.value = ['https://neguinhomotors.co.uk/assets/img/no-image.png'];
    } else {
      // Try to parse image_array if it's a JSON string array
      try {
        if (typeof data.redundantData.image_array === 'string' && data.redundantData.image_array.startsWith('[')) {
          const parsedArray = JSON.parse(data.redundantData.image_array);
          if (Array.isArray(parsedArray) && parsedArray.length > 0) {
            images.value = parsedArray.map(image => `/assets/images/store/products/${image}`);
          }
        }
      } catch (e) {
        console.log('JSON parse error:', e);
      }

      // If image_array is already an array and not empty, use it
      if (Array.isArray(data.redundantData.image_array) && data.redundantData.image_array.length > 0) {
        images.value = data.redundantData.image_array.map(image => `/assets/images/store/products/${image}`);
      } else if (data.redundantData.image_url) {
        // Fallback to image_url if image_array is empty
        images.value = [data.redundantData.image_url];
      } else if (product.value.image_url) {
        // Check if product.image_url exists and use it if available
        images.value = [product.value.image_url];
      }
    }
    // Set the current image to the first image in the array, if available
    currentImage.value = images.value[0] || '';
  } catch (err) {
    console.error('ERROR FETCH PRODUCT: ', err);
    error.value = true;
    if (err.response && err.response.data && err.response.data.message) {
      errorMessage.value = err.response.data.message;
    } else {
      errorMessage.value = 'Failed to load product. Please try again.';
    }
  } finally {
    isLoading.value = false;
  }
};
  
const selectVariation = (variation) => {
  if (variation.total_balance > 0) {
    selectedVariation.value = variation;
  } else {
    // Optionally, provide feedback if a user somehow tries to select an out-of-stock variation
    toastStore.triggerToast('This variation is out of stock.', 'warning');
  }
};

const addToBasket = async () => {
  if (!selectedVariation.value) {
    toastStore.triggerToast('Please select a variation before adding to the basket.', 'warning');
    return;
  }

  try {
    isAddingToCart.value = true;
    // Add 1 second delay
    await new Promise(resolve => setTimeout(resolve, 1000));

    // If user is authenticated, add to pending order
    if (userStore.isAuthenticated) {
      try {
        const response = await shopAPI.addOrderItem({
          product_id: selectedVariation.value.id,
          quantity: 1
        });

        if (response.success) {
          // Initialize cart to reflect the changes
          await cartStore.initializeCart();
        } else {
          throw new Error(response.message || 'Failed to add item to order');
        }
      } catch (error) {
        console.error('Failed to add to pending order:', error);
        // If API call fails, fall back to session storage
        await cartStore.addToCart(selectedVariation.value);
      }
    } else {
      // For guest users, use session storage
      await cartStore.addToCart(selectedVariation.value);
    }

    // Save product info to session storage for breadcrumb
    const productName = redundantData.value?.name || selectedVariation.value?.name;
    const variationName = selectedVariation.value?.variation;
    const displayName = productName + (variationName ? ` - ${variationName}` : '');

    sessionStorage.setItem('previous_product', JSON.stringify({
      path: router.currentRoute.value.fullPath,
      name: displayName
    }));

    toastStore.triggerToast('Item added to cart!', 'success');
    showCartModal.value = true;
  } catch (error) {
    toastStore.triggerToast('Failed to add item to cart.', 'error');
    console.error('Error adding to cart:', error);
  } finally {
    isAddingToCart.value = false;
  }
};

const selectImage = (img) => {
  currentImage.value = img;
};

const toggleZoom = () => {
  isZoomed.value = !isZoomed.value;
};

const continueShopping = () => {
  showCartModal.value = false;
  router.push('/shop');
};

const goToCheckout = () => {
  // Save product info to session storage for breadcrumb if not already saved
  if (!sessionStorage.getItem('previous_product')) {
    const productName = redundantData.value?.name || selectedVariation.value?.name;
    const variationName = selectedVariation.value?.variation;
    const displayName = productName + (variationName ? ` - ${variationName}` : '');

    sessionStorage.setItem('previous_product', JSON.stringify({
      path: router.currentRoute.value.fullPath,
      name: displayName
    }));
  }
  showCartModal.value = false;
  router.push('/shop/basket');
};
</script>

<style scoped>
@import '@/assets/styles/ProductPage.module.css';
</style>
