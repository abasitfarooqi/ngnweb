<!-- File: resources/js/views/Shop/BasketPage.vue -->

<template>
  <DefaultLayout>
    <!-- Breadcrumb Navigation -->
    <nav class="container mx-auto px-4 py-3 flex items-center space-x-2 text-sm">
      <router-link to="/shop" class="text-primary-500 hover:text-primary-600">Shop</router-link>
      <span class="text-gray-500">|</span>
      <router-link :to="previousProductPath" v-if="previousProductPath"
        class="text-primary-500 hover:text-primary-600">{{ previousProductName }}</router-link>
      <span v-if="previousProductPath" class="text-gray-500">|</span>
      <span class="text-gray-700">Basket</span>
    </nav>

    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">Shopping Cart</h1>

      <!-- Loading Indicator -->
      <div v-if="loading" class="flex justify-center items-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-500"></div>
      </div>

      <!-- Cart Content -->
      <template v-else>
        <!-- Empty Cart Message -->
        <div v-if="!cartStore.cartItemCount" class="text-center py-8">
          <p class="text-xl text-gray-600 mb-4">Your cart is empty</p>
          <router-link to="/shop"
            class="inline-block bg-primary-500 text-white px-6 py-2 hover:bg-primary-600 transition-colors">
            Continue Shopping
          </router-link>
        </div>

        <!-- Cart Items and Shipping Options -->
        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left Column: Cart Items and Shipping Options -->
          <div class="lg:col-span-2">
            <!-- Cart Items -->
            <div class="cart-items mb-6">
              <h2 class="text-xl font-medium mb-4">Items in Your Basket</h2>
              <div v-if="cartItems.length === 0" class="text-gray-600">
                Your basket is empty.
              </div>
              <div v-else>
                <ProductCartCard v-for="item in cartItems" :key="item.id" :product="item.product"
                  :quantity="item.quantity" @update-quantity="updateCart(item.id, $event)"
                  @remove-item="removeItem(item.id)" />
              </div>
            </div>

            <!-- Shipping Options -->
            <div class="shipping-options mb-6">
              <h2 class="text-xl font-medium mb-4">Shipping Method</h2>

              <!-- Show loading state -->
              <div v-if="shippingMethods.length === 0" class="text-gray-600">
                Loading shipping methods...
              </div>

              <!-- Show selected method summary if method is chosen -->
              <div v-else-if="selectedShippingMethod && isShippingMethodConfirmed"
                class="border rounded-lg p-4 bg-gray-50">
                <div class="flex justify-between items-start">
                  <div>
                    <div class="flex items-center gap-2">
                      <h3 class="font-medium">Selected: </h3>
                      <span v-if="selectedShippingDetails?.in_store_pickup" class="text-primary-600">
                        Collection from {{ selectedBranch?.name }} Branch
                      </span>
                      <span v-else-if="!selectedShippingDetails?.in_store_pickup" class="text-primary-600">
                        Home Delivery
                      </span>
                    </div>
                    <!-- Collection details -->
                    <div v-if="selectedShippingDetails?.in_store_pickup && selectedBranch" class="mt-2">
                      <p class="text-sm text-gray-500">{{ selectedBranch.address }}</p>
                      <p class="text-sm text-gray-500">Opening Hours: MON-SAT 9AM-6PM</p>
                      <p class="text-sm text-gray-500">SUNDAY: CLOSED</p>
                    </div>
                    <!-- Delivery details -->
                    <div v-else-if="!selectedShippingDetails?.in_store_pickup" class="mt-2">
                      <p class="text-sm text-gray-500">Standard delivery time: 3-5 working days</p>
                    </div>
                  </div>
                  <button @click="resetShippingMethod"
                    class="text-primary-500 hover:text-primary-600 text-sm underline">
                    Change
                  </button>
                </div>
              </div>

              <!-- Show shipping method selection -->
              <div v-else>
                <!-- In-store Pickup Option -->
                <div class="mb-6">
                  <div class="border rounded-lg p-4 cursor-pointer relative"
                    :class="{ 'border-primary-500 bg-primary-50': selectedShippingMethod === pickupMethod?.id }"
                    @click="handleShippingMethodChange(pickupMethod)">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-3">
                        <input type="radio" :value="pickupMethod?.id"
                          :checked="selectedShippingMethod === pickupMethod?.id"
                          class="form-radio h-5 w-5 text-primary-500" />
                        <div>
                          <span class="font-medium">Collect from Store</span>
                          <p class="text-sm text-gray-500">Free</p>
                        </div>
                      </div>
                      <!-- Cancel button for pickup method -->
                      <button v-if="selectedShippingMethod === pickupMethod?.id" @click.stop="resetShippingMethod"
                        class="text-gray-500 hover:text-gray-700">
                        <span class="text-sm">Cancel</span>
                      </button>
                    </div>
                  </div>

                  <!-- Branch Selector (only show if pickup is selected) -->
                  <div v-if="selectedShippingMethod === pickupMethod?.id"
                    class="mt-4 border-l-2 border-primary-100 pl-4">
                    <BranchSelector v-model="selectedBranchId" @update:modelValue="handleBranchSelection" />
                    <div class="mt-4 flex justify-end">
                      <button @click="confirmShippingMethod" :disabled="!selectedBranchId"
                        class="bg-primary-500 text-white px-4 py-2 rounded disabled:opacity-50">
                        Confirm Collection Store
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Delivery Option -->
                <div>
                  <div class="border rounded-lg p-4 cursor-pointer relative" :class="{
                    'border-primary-500 bg-primary-50': selectedShippingMethod === deliveryMethod?.id,
                    'opacity-50 cursor-not-allowed': deliveryMethod && !deliveryMethod.is_enabled
                  }" @click="handleShippingMethodChange(deliveryMethod)">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-3">
                        <input type="radio" :value="deliveryMethod?.id"
                          :checked="selectedShippingMethod === deliveryMethod?.id"
                          :disabled="deliveryMethod && !deliveryMethod.is_enabled"
                          class="form-radio h-5 w-5 text-primary-500" />
                        <div>
                          <span class="font-medium">Home Delivery</span>
                          <p class="text-sm text-gray-500">Delivery to your address</p>
                          <p v-if="deliveryMethod && !deliveryMethod.is_enabled" class="text-sm text-red-500 mt-1">
                            Currently unavailable
                          </p>
                        </div>
                      </div>
                      <!-- Cancel button for delivery method -->
                      <button v-if="selectedShippingMethod === deliveryMethod?.id" @click.stop="resetShippingMethod"
                        class="text-gray-500 hover:text-gray-700">
                        <span class="text-sm">Cancel</span>
                      </button>
                    </div>
                  </div>

                  <!-- Confirm delivery selection -->
                  <div v-if="selectedShippingMethod === deliveryMethod?.id"
                    class="mt-4 border-l-2 border-primary-100 pl-4">
                    <div class="flex justify-end">
                      <button @click="confirmShippingMethod" class="bg-primary-500 text-white px-4 py-2 rounded">
                        Confirm Delivery Method
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-8">
              <div v-if="!cartStore.cartItemCount" class="text-center text-gray-600">
                Add items to your cart to proceed
              </div>
              <div v-else-if="!isShippingMethodConfirmed" class="text-center text-gray-600">
                Please select a shipping method to continue
              </div>
              <button v-else @click="proceedToCheckout"
                class="w-full bg-primary-500 text-white py-3 px-6 rounded-lg hover:bg-primary-600 transition-colors">
                Proceed to Checkout
              </button>
            </div>
          </div>

          <!-- Right Column: Order Summary -->
          <div class="lg:col-span-1">
            <OrderSummaryCard :subtotal="subtotal" :tax="tax" :total="total" :can-checkout="canCheckout"
              :loading="cartStore.loading" :vat-rate="vatRate" @checkout="proceedToCheckout" />
          </div>

        </div>

      </template>
    </div>
  </DefaultLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '@/stores/modules/cart';
import { useShippingStore } from '@/stores/modules/shipping';
import { shopAPI } from '@/services/api';

import ProductCartCard from '@/components/Shop/ProductCartCard.vue';
import DefaultLayout from '@/components/layout/DefaultLayout.vue';
import OrderSummaryCard from '@/components/Shop/OrderSummaryCard.vue';
import BranchSelector from '@/components/Shop/BranchSelector.vue';
import { useUserStore } from '@/stores/modules/user';
import { useToastStore } from '@/stores/modules/toast';


const router = useRouter();
const cartStore = useCartStore();
const shippingStore = useShippingStore();
const userStore = useUserStore();
const toastStore = useToastStore();

interface CartProduct {
  id: number;
  name: string;
  sku: string;
  image_url: string;
  normal_price: number;
  brand: string;
  pos_vat: number;
  discount_price?: number | null;
  global_stock: number;
  slug: string;
  variation: any;
}

interface CartItem {
  id: number;
  quantity: number;
  product: CartProduct;
}

interface ShippingMethod {
  id: number;
  name: string;
  slug: string;
  logo: string;
  link_url: string;
  description: string;
  shipping_amount: string;
  is_enabled: boolean;
  in_store_pickup: boolean;
  created_at: string | null;
  updated_at: string | null;
}

interface Branch {
  id: number;
  name: string;
  address: string;
  latitude: string;
  longitude: string;
  created_at: string;
  updated_at: string;
}

interface PendingOrderResponse {
  success: boolean;
  order: {
    id: number;
    items: {
      id: number;
      product_id: number;
      product_name: string;
      sku: string;
      quantity: number;
      unit_price: number;
      total_price: number;
      tax: number;
      discount: number;
      line_total: number;
    }[];
    total_amount: number;
    grand_total: number;
    shipping_cost: number;
    tax: number;
    discount: number;
    currency: string;
    shipping_method_id: number;
    payment_method_id: number;
    customer_address_id: number;
    order_status: string;
    payment_status: string;
    shipping_status: string;
    branch_id: number;
    created_at: string;
    updated_at: string;
  };
}

interface ApiResponse {
  success: boolean;
  message?: string;
}

interface DeliveryMethodResponse {
  success: boolean;
  message?: string;
}

// State
const loading = computed(() => cartStore.loading);
const cartItems = ref<CartItem[]>([]);
const isLoading = ref(false);
const shippingMethods = ref<ShippingMethod[]>([]);
const selectedShippingMethod = ref<number | null>(null);
const selectedShippingDetails = computed(() =>
  shippingMethods.value.find(method => method.id === selectedShippingMethod.value)
);
const selectedBranchId = ref<number | undefined>(undefined);
const selectedBranch = ref<Branch | null>(null);
const isShippingMethodConfirmed = ref(false);
const branches = ref<Branch[]>([]);

// Breadcrumb navigation state
const previousProductPath = ref<string | null>(null);
const previousProductName = ref<string | null>(null);

// Get previous product info from session storage on mount
onMounted(async () => {
  try {
    // Get previous product info from session storage
    const previousProduct = sessionStorage.getItem('previous_product');
    if (previousProduct) {
      const { path, name } = JSON.parse(previousProduct);
      previousProductPath.value = path;
      previousProductName.value = name;
    }

    // Initialize cart based on user authentication status
    if (userStore.isAuthenticated) {
      try {
        // Try to fetch pending order for logged-in user
        const pendingOrderResponse = await shopAPI.getCartPendingOrder() as PendingOrderResponse;

        if (pendingOrderResponse.success && pendingOrderResponse.order) {
          // Use pending order data
          const orderData = pendingOrderResponse.order;

          // Map order items to cart format and store in session
          const cartItems = orderData.items.map(item => ({
            id: item.product_id,
            quantity: item.quantity,
            product: {
              id: item.product_id,
              name: item.product_name,
              sku: item.sku
            }
          }));

          // Store cart items in session before initialization
          sessionStorage.setItem('cart', JSON.stringify(cartItems));

          // Fetch branch details for the stored branch_id
          let branchDetails: Branch | null = null;
          if (orderData.branch_id) {
            try {
              const branchesResponse = await shopAPI.getBranches() as Branch[];
              const foundBranch = branchesResponse.find((branch: Branch) => branch.id === orderData.branch_id);
              branchDetails = foundBranch || null;
            } catch (error) {
              console.error('Error fetching branch details:', error);
            }
          }

          // Fetch shipping method details
          let shippingMethod: ShippingMethod | null = null;
          if (orderData.shipping_method_id) {
            try {
              const shippingMethodsResponse = await shopAPI.getShippingMethods() as ShippingMethod[];
              const foundMethod = shippingMethodsResponse.find((method: ShippingMethod) => method.id === orderData.shipping_method_id);
              shippingMethod = foundMethod || null;
            } catch (error) {
              console.error('Error fetching shipping method details:', error);
            }
          }

          // Reconstruct shipping details from order data
          if (shippingMethod && (orderData.branch_id || !shippingMethod.in_store_pickup)) {
            const shippingData = {
              method_id: orderData.shipping_method_id,
              is_store_pickup: shippingMethod.in_store_pickup,
              branch_id: orderData.branch_id,
              branch_details: branchDetails
            };

            // Store shipping details in session
            sessionStorage.setItem('shipping_details', JSON.stringify(shippingData));

            // Update component state
            selectedShippingMethod.value = shippingData.method_id;
            selectedBranchId.value = shippingData.branch_id;
            selectedBranch.value = shippingData.branch_details;
            isShippingMethodConfirmed.value = true;

            // Set shipping method in store
            if (shippingData.is_store_pickup) {
              shippingStore.setShippingMethod('collect_in_store');
            } else {
              shippingStore.setShippingMethod('home_delivery');
            }
          }

          // Initialize cart from session (which now contains pending order items)
          await cartStore.initializeCart();

          console.log('Using pending order data:', orderData);
        } else {
          // If no pending order, initialize from session
          await cartStore.initializeCart();
        }
      } catch (error: any) {
        // Check if it's a 404 error (no pending order)
        if (error.response && error.response.status === 404) {
          console.log('No pending order found, initializing from session');
          await cartStore.initializeCart();
        } else {
          console.error('Error fetching pending order:', error);
          // Fallback to session cart if API fails
          await cartStore.initializeCart();
        }
      }
    } else {
      // For guest users, just initialize from session
      await cartStore.initializeCart();
    }

    await Promise.all([
      fetchProductDetails(),
      fetchShippingMethods(),
      fetchBranches()
    ]);
  } catch (error) {
    console.error('Failed to initialize:', error);
  }
});

// Fetch product details for each cart item
const fetchProductDetails = async () => {
  try {
    const items = await Promise.all(
      (cartStore.items as { id: number; quantity: number }[]).map(async (item) => {
        const product = await shopAPI.getProductById(item.id);
        return {
          id: item.id,
          quantity: item.quantity,
          product: {
            id: (product as any).id,
            name: (product as any).name,
            sku: (product as any).sku,
            image_url: (product as any).image_url,
            brand: (product as any).brand,
            pos_vat: (product as any).pos_vat,
            normal_price: parseFloat((product as any).normal_price),
            discount_price: (product as any).discount_price ? parseFloat((product as any).discount_price) : undefined,
            global_stock: parseFloat((product as any).global_stock),
            slug: (product as any).slug,
            variation: (product as any).variation
          }
        } as CartItem;
      })
    );
    cartItems.value = items;
  } catch (error) {
    console.error('Error fetching product details:', error);
  }
};

// Fetch shipping methods
const fetchShippingMethods = async () => {
  try {
    // should be fetched from the api
    const response = await shopAPI.getShippingMethods();

    shippingMethods.value = response;
  } catch (error) {
    console.error('Failed to fetch shipping methods:', error);
  }
};

// Fetch branches
const fetchBranches = async () => {
  try {
    const response = await shopAPI.getBranches();
    branches.value = response;
  } catch (error) {
    console.error('Error fetching branches:', error);
  }
};

const subtotal = computed(() => {
  const total = cartItems.value.reduce((sum, item) => {
    const price = item.product.discount_price || item.product.normal_price;
    const priceExVat = (price * 100) / 120;
    return sum + (priceExVat * item.quantity);
  }, 0);
  return total.toFixed(2);
});

const tax = computed(() => {
  return cartItems.value.reduce((sum, item) => {
    const price = item.product.discount_price || item.product.normal_price;
    const priceExVat = (price * 100) / 120;
    const vatAmount = price - priceExVat;
    return sum + (vatAmount * item.quantity);
  }, 0).toFixed(2);
});

const vatRate = computed(() => 20);

const total = computed(() => {
  return cartItems.value.reduce((sum, item) => {
    const price = item.product.discount_price || item.product.normal_price;
    return sum + (price * item.quantity);
  }, 0).toFixed(2);
});

const canCheckout = computed(() =>
  cartStore.cartItemCount > 0 && isShippingMethodConfirmed.value
);

const pickupMethod = computed(() => {
  const method = shippingMethods.value.find(method => method.in_store_pickup);
  return method || null; // Return null if no pickup method is found
});

const deliveryMethod = computed(() => {
  const method = shippingMethods.value.find(method => !method.in_store_pickup);
  return method || null; // Return null if no delivery method is found
});

// Methods
const updateCart = async (productId: number, newQuantity: number) => {
  try {
    if (userStore.isAuthenticated) {
      // For authenticated users, update through API using product ID
      const response = await shopAPI.updateCartItemQuantity(productId, newQuantity) as ApiResponse;
      if (response.success) {
        // First update the cart store
        await cartStore.initializeCart();

        // Then fetch fresh product details to ensure sync
        const product = await shopAPI.getProductById(productId);

        // Update local cart item with fresh data
        const itemIndex = cartItems.value.findIndex(item => item.id === productId);
        if (itemIndex !== -1) {
          cartItems.value[itemIndex] = {
            id: productId,
            quantity: newQuantity,
            product: {
              id: (product as any).id,
              name: (product as any).name,
              sku: (product as any).sku,
              image_url: (product as any).image_url,
              brand: (product as any).brand,
              pos_vat: (product as any).pos_vat,
              normal_price: parseFloat((product as any).normal_price),
              discount_price: (product as any).discount_price ? parseFloat((product as any).discount_price) : undefined,
              global_stock: parseFloat((product as any).global_stock),
              slug: (product as any).slug,
              variation: (product as any).variation
            }
          };
        }

        // Update session storage with new cart data
        const sessionCart = cartStore.items.map((item: { id: number; quantity: number }) => ({
          id: item.id,
          quantity: item.id === productId ? newQuantity : item.quantity
        }));
        sessionStorage.setItem('cart', JSON.stringify(sessionCart));
      } else {
        throw new Error(response.message || 'Failed to update quantity');
      }
    } else {
      // For guest users, update cart store first
      await cartStore.updateQuantity(productId, newQuantity);

      // Then update local cart item
      const itemIndex = cartItems.value.findIndex(item => item.id === productId);
      if (itemIndex !== -1) {
        cartItems.value[itemIndex].quantity = newQuantity;
      }
    }
  } catch (error) {
    console.error('Error updating quantity:', error);
    toastStore.triggerToast('Failed to update quantity', 'error');

    // Revert local changes on error
    await fetchProductDetails();
  }
};

const removeItem = async (productId: number) => {
  try {
    if (userStore.isAuthenticated) {
      // For authenticated users, set quantity to 0 to remove item using product ID
      const response = await shopAPI.updateCartItemQuantity(productId, 0) as ApiResponse;
      if (response.success) {
        // Immediately remove item from local cart items
        cartItems.value = cartItems.value.filter(item => item.id !== productId);
        // Update cart store
        await cartStore.initializeCart();
      } else {
        throw new Error(response.message || 'Failed to remove item');
      }
    } else {
      // For guest users, use existing cart store method
      await cartStore.removeFromCart(productId);
      // Immediately remove item from local cart items
      cartItems.value = cartItems.value.filter(item => item.id !== productId);
    }
  } catch (error) {
    console.error('Error removing item:', error);
    toastStore.triggerToast('Failed to remove item', 'error');
  }
};

const handleShippingMethodChange = async (method: ShippingMethod | null | undefined) => {
  if (!method || !method.is_enabled) return;

  if (method.id === selectedShippingMethod.value) {
    return;
  }

  // Update local state
  selectedShippingMethod.value = method.id;
  selectedBranchId.value = undefined;
  selectedBranch.value = null;

  // Update shipping store
  if (method.in_store_pickup) {
    shippingStore.setShippingMethod('collect_in_store');
  } else {
    shippingStore.setShippingMethod('home_delivery');
  }
};

const proceedToCheckout = async () => {
  if (!cartStore.cartItemCount) {
    return;
  }

  if (!isShippingMethodConfirmed.value) {
    return;
  }

  // Save shipping details to session
  saveShippingToSession();

  // Check if user is authenticated
  if (!userStore.isAuthenticated) {
    // Save current path for redirect after login
    router.push({
      path: '/accountinformation/login',
      query: { redirect: '/shop/checkout' }
    });
    return;
  }

  // If authenticated, proceed to checkout
  router.push({ name: 'CheckoutPage' });
};

const handleBranchSelection = (branchId: number) => {
  selectedBranchId.value = branchId;
  // Get branch details from BranchSelector component
  const branch = branches.value.find(b => b.id === branchId);
  if (branch) {
    selectedBranch.value = branch;
  }
};

const confirmShippingMethod = async () => {
  if (selectedShippingMethod.value === pickupMethod.value?.id && !selectedBranchId.value) {
    return;
  }

  try {
    // If user is authenticated, update the pending order's delivery method
    if (userStore.isAuthenticated) {
      const response = await shopAPI.changeDeliveryMethod(
        selectedShippingMethod.value || null,
        selectedShippingDetails.value?.in_store_pickup ? selectedBranchId.value || null : null
      ) as DeliveryMethodResponse;

      if (!response.success) {
        throw new Error(response.message || 'Failed to update delivery method');
      }
    }

    // Update local state
    isShippingMethodConfirmed.value = true;

    // Save to session storage (for both guest and authenticated users)
    saveShippingToSession();

  } catch (error) {
    console.error('Error confirming shipping method:', error);
    toastStore.triggerToast('Failed to update shipping method', 'error');
    return;
  }
};

const resetShippingMethod = async () => {
  try {
    // If user is authenticated, reset the delivery method in the database
    if (userStore.isAuthenticated) {
      // Switch to delivery method (non-pickup) to avoid branch ID requirement
      const deliveryMethodId = deliveryMethod.value?.id;
      if (!deliveryMethodId) {
        throw new Error('No delivery method available');
      }

      const response = await shopAPI.changeDeliveryMethod(
        deliveryMethodId,  // Use delivery method instead of current method
        null  // No branch ID needed for delivery
      ) as DeliveryMethodResponse;

      if (!response.success) {
        throw new Error(response.message || 'Failed to reset delivery method');
      }
    }

    // Reset local state
    selectedShippingMethod.value = null;
    selectedBranchId.value = undefined;
    selectedBranch.value = null;
    isShippingMethodConfirmed.value = false;

    // Clear from session storage
    sessionStorage.removeItem('shipping_details');

  } catch (error) {
    console.error('Error resetting shipping method:', error);
    toastStore.triggerToast('Failed to reset shipping method', 'error');
  }
};

const saveShippingToSession = () => {
  const shippingData = {
    method_id: selectedShippingMethod.value,
    is_store_pickup: selectedShippingDetails.value?.in_store_pickup || false,
    branch_id: selectedBranchId.value,
    branch_details: selectedBranch.value
  };

  // Store in session storage
  sessionStorage.setItem('shipping_details', JSON.stringify(shippingData));
};

const proceedToDetails = () => {
  if (!cartStore.cartItemCount) {
    return;
  }

  if (!isShippingMethodConfirmed.value) {
    return;
  }

  // Save shipping details to session
  saveShippingToSession();

  // Navigate to details page
  router.push('/shop/checkout');
};

// Watch for cart changes
watch(() => cartStore.items, async () => {
  await fetchProductDetails();

  // Remove previous_product from session if cart is empty
  if (cartStore.items.length === 0) {
    sessionStorage.removeItem('previous_product');
    previousProductPath.value = null;
    previousProductName.value = null;
  }
}, { deep: true });

// Add a watch for shippingMethods to handle async loading
watch(shippingMethods, () => {
  // Validate saved shipping method still exists and is enabled
  if (selectedShippingMethod.value) {
    const method = shippingMethods.value.find(m => m.id === selectedShippingMethod.value);
    if (!method || !method.is_enabled) {
      // Reset selection if method no longer exists or is disabled
      resetShippingMethod();
      sessionStorage.removeItem('shipping_details');
    }
  }
}, { immediate: true });
</script>

<style scoped>
@import '@/assets/styles/BasketPage.module.css';
</style>