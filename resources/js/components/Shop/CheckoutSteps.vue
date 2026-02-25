<!-- FILE: resources/js/components/Shop/CheckoutSteps.vue -->
<template>
  <div class="checkout-steps space-y-4">
    <!-- Add loading overlay when checking status -->
    <div v-if="isCheckingStatus" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-xl text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500 mx-auto mb-4"></div>
        <p class="text-lg font-medium text-gray-700">Verifying Order Status...</p>
        <p class="text-sm text-gray-500 mt-2">Please wait a moment</p>
      </div>
    </div>

    <!-- Add payment processing overlay -->
    <div v-if="isPaymentProcessing" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-xl text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500 mx-auto mb-4"></div>
        <p class="text-lg font-medium text-gray-700">Processing Payment...</p>
        <p class="text-sm text-gray-500 mt-2">Please do not refresh or leave this page</p>
      </div>
    </div>

    <!-- Navigation Banner when all steps completed -->
    <div v-if="isDeliveryCompleted && isPaymentCompleted && isConfirmationCompleted"
      class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <h2 class="text-lg font-medium text-green-800">Order Successfully Placed!</h2>
            <p class="text-sm text-green-600">What would you like to do next?</p>
          </div>
        </div>
        <div class="flex gap-3">
          <button @click="goToOrders"
            class="bg-white text-green-700 px-4 py-2 rounded border border-green-300 hover:bg-green-50 transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
              <path fill-rule="evenodd"
                d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                clip-rule="evenodd" />
            </svg>
            View My Orders
          </button>
          <button v-if="userStore.user?.is_admin" @click="goToAdmin"
            class="bg-white text-gray-700 px-4 py-2 rounded border border-gray-300 hover:bg-gray-50 transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd"
                d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z"
                clip-rule="evenodd" />
              <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z" />
            </svg>
            Go to Admin Panel
          </button>
          <button @click="goToShop"
            class="bg-primary-500 text-white px-4 py-2 rounded hover:bg-primary-600 transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd"
                d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
            Continue Shopping
          </button>
        </div>
      </div>
    </div>

    <!-- Delivery/Pickup Section -->
    <div class="border bg-white" :class="{ 'completed-step': isDeliveryCompleted }">
      <div class="p-4" :class="{ 'border-b': isDeliveryExpanded }">
        <div class="flex items-center justify-between"
          :class="{ 'cursor-not-allowed': isDeliveryCompleted, 'cursor-pointer': !isDeliveryCompleted }"
          @click="toggleDelivery">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center"
              :class="isDeliveryCompleted ? 'bg-green-500 text-white' : 'bg-gray-200'">
              <svg v-if="isDeliveryCompleted" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd" />
              </svg>
              <span v-else>1</span>
            </div>
            <h3 class="text-lg font-medium">Delivery / Pickup Confirmation</h3>
          </div>
          <div class="flex items-center gap-2">
            <span v-if="isDeliveryCompleted" class="text-sm text-green-500">Completed</span>
            <svg v-if="!isDeliveryCompleted" class="w-5 h-5 transform transition-transform"
              :class="{ 'rotate-180': isDeliveryExpanded }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
              fill="currentColor">
              <path fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Shipping Method Selection -->
      <div v-show="isDeliveryExpanded && !isDeliveryCompleted" class="p-4">
        <!-- Loading Indicator for Checkout Step Data -->
        <div v-if="stepLoading" class="text-center my-4">
          <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-500 mx-auto"></div>
          <p class="mt-2 text-gray-600">Loading checkout information...</p>
        </div>

        <!-- MAIN CONTENT -->
        <template v-else>
          <div class="shipping-options mb-6">
            <h2 class="text-xl font-medium mb-4">Shipping Method</h2>

            <!-- Loading State for shipping methods -->
            <div v-if="shippingMethodsLoading" class="text-gray-600">
              Loading shipping methods...
            </div>

            <!-- Show selected shipping method summary if chosen and confirmed -->
            <div v-else-if="selectedShippingMethod && isShippingMethodConfirmed"
              class="border rounded-lg p-4 bg-gray-50">
              <div class="flex justify-between items-start">
                <div>
                  <div class="flex items-center gap-2">
                    <h3 class="font-medium">Selected: </h3>
                    <span v-if="selectedShippingDetails?.in_store_pickup" class="text-primary-600">
                      Collection from {{ selectedBranch?.name }} Branch
                    </span>
                    <span v-else class="text-primary-600">
                      Home Delivery
                    </span>
                  </div>

                  <!-- Collection Details -->
                  <div v-if="selectedShippingDetails?.in_store_pickup && selectedBranch" class="mt-2">
                    <p class="text-sm text-gray-500">{{ selectedBranch.address }}</p>
                    <p class="text-sm text-gray-500">Opening Hours: MON-SAT 9AM-6PM</p>
                    <p class="text-sm text-gray-500">SUNDAY: CLOSED</p>
                  </div>

                  <!-- Delivery Details -->
                  <div v-else-if="!selectedShippingDetails?.in_store_pickup" class="mt-2">
                    <p class="text-sm text-gray-500">Standard delivery time: 3-5 working days</p>
                  </div>
                </div>
                <button @click.stop="resetShippingMethod"
                  class="text-primary-500 hover:text-primary-600 text-sm underline">
                  Change
                </button>
              </div>
            </div>

            <!-- Show shipping method selection options -->
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
                        :disabled="pickupMethod && !pickupMethod.is_enabled"
                        class="form-radio h-5 w-5 text-primary-500" />
                      <div>
                        <span class="font-medium">Collect from Store</span>
                        <p class="text-sm text-gray-500">Free</p>
                      </div>
                    </div>
                    <!-- Cancel Button for Pickup Method -->
                    <button v-if="selectedShippingMethod === pickupMethod?.id" @click.stop="resetShippingMethod"
                      class="text-gray-500 hover:text-gray-700">
                      <span class="text-sm">Cancel</span>
                    </button>
                  </div>
                </div>

                <!-- Branch Selector (only show if pickup is selected) -->
                <div v-if="selectedShippingMethod === pickupMethod?.id" class="mt-4 border-l-2 border-primary-100 pl-4">
                  <!-- Pass local "branches" to BranchSelector (NOT shippingStore branches) -->
                  <BranchSelector v-model="selectedBranchId" :branches="localBranches"
                    @update:modelValue="handleBranchSelection" />
                  <div class="mt-4 flex justify-end">
                    <button @click="confirmShippingMethod" :disabled="!selectedBranchId"
                      class="bg-primary-500 text-white px-4 py-2 rounded disabled:opacity-50">
                      Confirm Collection Store
                    </button>
                  </div>
                </div>
              </div>

              <!-- Home Delivery Option -->
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
                    <!-- Cancel Button for Delivery Method -->
                    <button v-if="selectedShippingMethod === deliveryMethod?.id" @click.stop="resetShippingMethod"
                      class="text-gray-500 hover:text-gray-700">
                      <span class="text-sm">Cancel</span>
                    </button>
                  </div>
                </div>

                <!-- Confirm Delivery Selection -->
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

          <!-- Continue to Payment -->
          <button @click="continueToPayment" :disabled="!isShippingMethodConfirmed"
            class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:opacity-50">
            Continue to Payment
          </button>
        </template>
      </div>
    </div>

    <!-- Payment Section -->
    <div class="border rounded-lg bg-white"
      :class="{ 'opacity-50': !isDeliveryCompleted, 'completed-step': isPaymentCompleted }">
      <div class="p-4" :class="{ 'border-b': isPaymentExpanded }">
        <div class="flex items-center justify-between" :class="{
          'cursor-not-allowed': !isDeliveryCompleted || isPaymentCompleted,
          'cursor-pointer': isDeliveryCompleted && !isPaymentCompleted
        }" @click="togglePayment">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center"
              :class="isPaymentCompleted ? 'bg-green-500 text-white' : 'bg-gray-200'">
              <svg v-if="isPaymentCompleted" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd" />
              </svg>
              <span v-else>2</span>
            </div>
            <h3 class="text-lg font-medium">Payment</h3>
          </div>
          <div class="flex items-center gap-2">
            <span v-if="isPaymentCompleted" class="text-sm text-green-500">Completed</span>
            <svg v-if="!isPaymentCompleted && isDeliveryCompleted" class="w-5 h-5 transform transition-transform"
              :class="{ 'rotate-180': isPaymentExpanded }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
              fill="currentColor">
              <path fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
          </div>
        </div>
      </div>
      <div v-show="isPaymentExpanded && !isPaymentCompleted" class="p-4">
        <div class="space-y-4">
          <h4 class="text-lg font-medium">Select Payment Method</h4>

          <!-- PayPal Payment Button -->
          <div class="border rounded-lg p-4 hover:border-[#0070ba] transition-colors">
            <button @click="initiatePayPalPayment"
              class="w-full bg-[#0070ba] text-white font-bold px-6 py-3 rounded hover:bg-[#003087] transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
              :disabled="loading || isCheckingStatus || isPaymentProcessing">
              <span v-if="loading || isCheckingStatus || isPaymentProcessing" class="flex items-center gap-2">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                  </path>
                </svg>
                {{ getPaymentButtonText() }}
              </span>
              <span v-else class="flex items-center gap-2">
                <span class="font-paypal text-xl">PayPal</span>
              </span>
            </button>
            <p class="text-sm text-gray-500 text-center mt-2">Safe and secure payments by PayPal</p>
          </div>

          <div v-if="totalPrice > 30" class="border rounded-lg p-4 hover:border-[#0070ba] transition-colors">
            <button @click="initiatePayPalPayment"
              class="w-full bg-[#0070ba] text-white font-bold px-6 py-3 rounded hover:bg-[#003087] transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
              :disabled="loading || isCheckingStatus || isPaymentProcessing">
              <span v-if="loading || isCheckingStatus || isPaymentProcessing" class="flex items-center gap-2">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                  </path>
                </svg>
                {{ getPaymentButtonText() }}
              </span>
              <span v-else class="flex items-center gap-2">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/PayPal_Logo_Icon_2014.svg" alt="PayPal Logo" class="h-5 w-5">
                <span class="text-xl">Pay in 3 interest-free payments.</span>
              </span>
            </button>
            <p class="text-sm text-gray-500 text-center mt-2">Safe and secure payments by PayPal</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Confirmation Section -->
    <div class="border rounded-lg bg-white"
      :class="{ 'opacity-50': !isPaymentCompleted, 'completed-step': isConfirmationCompleted }">
      <div class="p-4" :class="{ 'border-b': isConfirmationExpanded }">
        <div class="flex items-center justify-between" :class="{
          'cursor-not-allowed': !isPaymentCompleted || isConfirmationCompleted,
          'cursor-pointer': isPaymentCompleted && !isConfirmationCompleted
        }" @click="toggleConfirmation">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center"
              :class="isConfirmationCompleted ? 'bg-green-500 text-white' : 'bg-gray-200'">
              <svg v-if="isConfirmationCompleted" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd" />
              </svg>
              <span v-else>3</span>
            </div>
            <h3 class="text-lg font-medium">Order processing</h3>
          </div>
          <div class="flex items-center gap-2">
            <span v-if="isConfirmationCompleted" class="text-sm text-green-500">Completed</span>
            <svg v-if="!isConfirmationCompleted && isPaymentCompleted" class="w-5 h-5 transform transition-transform"
              :class="{ 'rotate-180': isConfirmationExpanded }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
              fill="currentColor">
              <path fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
          </div>
        </div>
      </div>
      <div v-show="isConfirmationExpanded && !isConfirmationCompleted" class="p-4">
        <!-- Confirmation content here -->
        <div class="text-center">
          <div class="mb-4">
            <svg class="w-16 h-16 text-green-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-xl font-medium mt-2">Payment Successful!</h3>
            <p class="text-gray-600 mt-1">Your order has been confirmed and will be processed shortly.</p>
          </div>
          <div class="flex justify-center gap-4">
            <button @click="goToOrders"
              class="bg-primary-500 text-white px-6 py-3 rounded hover:bg-primary-600 transition-colors flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                <path fill-rule="evenodd"
                  d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                  clip-rule="evenodd" />
              </svg>
              See Orders
            </button>
            <button v-if="userStore.user?.is_admin" @click="goToAdmin"
              class="bg-gray-800 text-white px-6 py-3 rounded hover:bg-gray-700 transition-colors flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                  d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z"
                  clip-rule="evenodd" />
                <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z" />
              </svg>
              Go to Admin
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useCartStore } from '@/stores/modules/cart'
import { useShippingStore } from '@/stores/modules/shipping'
import { shopAPI } from '@/services/api'
import { useUserStore } from '@/stores/modules/user'
import { useToastStore } from '@/stores/modules/toast'

import BranchSelector from '@/components/Shop/BranchSelector.vue'

// Steps UI States
const router = useRouter()
const route = useRoute()
const cartStore = useCartStore()
const shippingStore = useShippingStore()
const userStore = useUserStore()
const toastStore = useToastStore()

// Step expand/collapse
const isDeliveryExpanded = ref(true)
const isDeliveryCompleted = ref(false)

const isPaymentExpanded = ref(false)
const isPaymentCompleted = ref(false)

const isConfirmationExpanded = ref(false)
const isConfirmationCompleted = ref(false)

// Data loading flags
const stepLoading = ref(true)
const shippingMethodsLoading = ref(true)

// Shipping data
const shippingMethods = ref([])
const selectedShippingMethod = ref(null)
const isShippingMethodConfirmed = ref(false)

// Local branches array (instead of shippingStore.setBranches)
const localBranches = ref([])

// Currently selected branch info
const selectedBranchId = ref(null)
const selectedBranch = ref(null)

// Cart items
const cartItems = ref([])

// Add new refs
const loading = ref(false)

// Add new ref for status checking
const isCheckingStatus = ref(false)

// Add new ref for payment processing state
const isPaymentProcessing = ref(false)

// Add a prop to accept the total price
const props = defineProps({
    totalPrice: Number
});

// Compute shipping details for template
const selectedShippingDetails = computed(() => {
  if (!selectedShippingMethod.value) return null
  return shippingMethods.value.find(
    (method) => method.id === selectedShippingMethod.value
  ) || null
})

// For convenience if we want them:
const pickupMethod = computed(() =>
  shippingMethods.value.find((m) => m.in_store_pickup)
)
const deliveryMethod = computed(() =>
  shippingMethods.value.find((m) => !m.in_store_pickup)
)

// --------------------
// Lifecycle & Watchers
// --------------------
onMounted(async () => {
  try {
    stepLoading.value = true
    isCheckingStatus.value = true

    // First load checkout data
    await initializeCheckoutData()

    // Check URL parameters
    const urlParams = new URLSearchParams(window.location.search)
    const paymentStatus = urlParams.get('payment_status')

    if (paymentStatus === 'success') {
      isPaymentProcessing.value = true
      // Longer initial delay for payment processing
      await new Promise(resolve => setTimeout(resolve, 8000)) // 8 second initial delay

      // Start polling for order confirmation
      let attempts = 0
      const maxAttempts = 15 // 30 seconds total (15 * 2 second intervals)
      const pollInterval = 2000

      const pollOrderStatus = async () => {
        console.log(`Checking order status - attempt ${attempts + 1}/${maxAttempts}`)

        try {
          const response = await shopAPI.checkPendingOrderStatus()
          console.log('Order status response:', response)

          if (response.success && response.data.is_completed) {
            // Success case handling
            await handlePaymentSuccess()

            return true
          }

          if (attempts >= maxAttempts) {
            handlePaymentPending()
            return false
          }

          attempts++
          await new Promise(resolve => setTimeout(resolve, pollInterval))
          return pollOrderStatus()
        } catch (error) {
          console.error('Error checking order status:', error)
          if (attempts >= maxAttempts) {
            handlePaymentError()
            return false
          }
          attempts++
          await new Promise(resolve => setTimeout(resolve, pollInterval))
          return pollOrderStatus()
        }
      }

      try {
        await pollOrderStatus()
      } finally {
        const newUrl = '/shop/checkout'
        window.history.replaceState({}, document.title, newUrl)
        isCheckingStatus.value = false
        isPaymentProcessing.value = false
      }
    } else {
      // If not returning from payment, just check once
      await verifyOrderCompletion()
    }
  } catch (error) {
    console.error('Error in onMounted:', error)
    toastStore.triggerToast('Error loading checkout information', 'error')
  } finally {
    stepLoading.value = false
    isCheckingStatus.value = false
  }
})

watch(shippingMethods, () => {
  if (selectedShippingMethod.value) {
    const method = shippingMethods.value.find(
      (m) => m.id === selectedShippingMethod.value
    )
    if (!method || !method.is_enabled) {
      resetShippingMethod()
      sessionStorage.removeItem('shipping_details')
      toastStore.triggerToast(
        'Selected shipping method is no longer available.',
        'warning'
      )
    }
  }
})

// Update the route watcher
watch(
  () => route.query,
  async (newQuery) => {
    if (newQuery.payment_status === 'success') {
      isCheckingStatus.value = true

      // Add longer initial delay to allow webhook/database updates
      await new Promise(resolve => setTimeout(resolve, 15000))

      // Start polling for order confirmation
      let attempts = 0
      const maxAttempts = 10 // Increased attempts
      const pollInterval = 2000

      const pollOrderStatus = async () => {
        console.log(`Checking order status - attempt ${attempts + 1}/${maxAttempts}`)

        try {
          const response = await shopAPI.checkPendingOrderStatus()
          console.log('Order status response:', response)

          if (response.success && response.data.is_completed) {
            isDeliveryCompleted.value = true
            isPaymentCompleted.value = true
            isPaymentExpanded.value = false
            isConfirmationExpanded.value = true
            isConfirmationCompleted.value = true

            sessionStorage.setItem('checkout_state', JSON.stringify({
              delivery_completed: true,
              payment_completed: true
            }))

            toastStore.triggerToast('Order confirmed and payment completed!', 'success')
            return true
          }

          sessionStorage.removeItem('cart')
          await cartStore.clearCart()

          if (attempts >= maxAttempts) {
            toastStore.triggerToast(
              'Payment received. Order confirmation may take a few moments. Please check your orders.',
              'info'
            )
            isCheckingStatus.value = false
            return false
          }

          attempts++
          await new Promise(resolve => setTimeout(resolve, pollInterval))
          return pollOrderStatus()
        } catch (error) {
          console.error('Error checking order status:', error)
          if (attempts >= maxAttempts) {
            toastStore.triggerToast('Error checking order status. Please check your orders.', 'error')
            isCheckingStatus.value = false
            return false
          }
          attempts++
          await new Promise(resolve => setTimeout(resolve, pollInterval))
          return pollOrderStatus()
        }
      }

      try {
        await pollOrderStatus()
      } finally {
        const newUrl = '/shop/checkout'
        window.history.replaceState({}, document.title, newUrl)
        isCheckingStatus.value = false
      }
    } else if (newQuery.payment_status === 'cancelled' || newQuery.payment_status === 'error') {
      // Handle payment cancellation or error
      isPaymentCompleted.value = false
      isPaymentExpanded.value = true

      // Clear any completed state from session storage
      sessionStorage.removeItem('checkout_state')

      // Show appropriate message
      const message = newQuery.payment_status === 'cancelled'
        ? 'Payment was cancelled. Please try again.'
        : 'Payment failed. Please try again.';
      toastStore.triggerToast(message, 'error')

      // Clean up the URL
      const newUrl = '/shop/checkout'
      window.history.replaceState({}, document.title, newUrl)

      // Refresh the order data
      await initializeCheckoutData()
    }
  },
  { immediate: true }
)

// --------------------
// Initialization
// --------------------
async function initializeCheckoutData() {
  if (userStore.isAuthenticated) {
    await handlePendingOrder()
  } else {
    // Guest user: just load cart from session
    await cartStore.initializeCart()
  }

  await fetchShippingMethods()
  await fetchBranches()
  await fetchProductDetails()

  loadShippingFromSession()
}

async function handlePendingOrder() {
  try {
    const pendingOrderResponse = await shopAPI.getCartPendingOrder()
    if (pendingOrderResponse.success && pendingOrderResponse.order) {
      const orderData = pendingOrderResponse.order

      // Convert pending order items -> cart format
      const tmpCartItems = orderData.items.map((item) => ({
        id: item.product_id,
        quantity: item.quantity,
        product: {
          id: item.product_id,
          name: item.product_name,
          sku: item.sku
        }
      }))
      sessionStorage.setItem('cart', JSON.stringify(tmpCartItems))

      // Possibly fetch branch if order has branch_id
      let branchDetails = null
      if (orderData.branch_id) {
        try {
          const allBranches = await shopAPI.getBranches()
          // store them locally so we can match below
          localBranches.value = allBranches
          branchDetails =
            allBranches.find((b) => b.id === orderData.branch_id) || null
        } catch (error) {
          console.error('Error fetching branch details:', error)
        }
      }

      // If order has shipping_method_id, fetch method details
      let shippingMethodData = null
      if (orderData.shipping_method_id) {
        try {
          const allMethods = await shopAPI.getShippingMethods()
          shippingMethodData =
            allMethods.find((m) => m.id === orderData.shipping_method_id) ||
            null
        } catch (error) {
          console.error('Error fetching shipping method details:', error)
        }
      }

      // Reconstruct shipping details
      if (
        shippingMethodData &&
        (orderData.branch_id || !shippingMethodData.in_store_pickup)
      ) {
        const shippingData = {
          method_id: orderData.shipping_method_id,
          is_store_pickup: shippingMethodData.in_store_pickup,
          branch_id: orderData.branch_id,
          branch_details: branchDetails
        }

        sessionStorage.setItem('shipping_details', JSON.stringify(shippingData))

        selectedShippingMethod.value = shippingData.method_id
        selectedBranchId.value = shippingData.branch_id
        selectedBranch.value = shippingData.branch_details
        isShippingMethodConfirmed.value = true

        if (shippingData.is_store_pickup) {
          shippingStore.setShippingMethod('collect_in_store')
        } else {
          shippingStore.setShippingMethod('home_delivery')
        }
      }

      // Finally, init cart from session
      await cartStore.initializeCart()
    } else {
      // No pending order => just load from session
      await cartStore.initializeCart()
    }
  } catch (error) {
    console.error('Error fetching pending order:', error)
    await cartStore.initializeCart()
  }
}

// --------------------
// API & Data Fetching
// --------------------
async function fetchShippingMethods() {
  try {
    const response = await shopAPI.getShippingMethods()
    shippingMethods.value = response
  } catch (error) {
    console.error('Failed to fetch shipping methods:', error)
    toastStore.triggerToast('Failed to load shipping methods.', 'error')
  } finally {
    shippingMethodsLoading.value = false
  }
}

async function fetchBranches() {
  try {
    // Instead of shippingStore.setBranches(...), just store them locally
    localBranches.value = await shopAPI.getBranches()
  } catch (error) {
    console.error('Error fetching branches:', error)
  }
}

async function fetchProductDetails() {
  try {
    const items = await Promise.all(
      cartStore.items.map(async (item) => {
        const productData = await shopAPI.getProductById(item.id)
        return {
          id: item.id,
          quantity: item.quantity,
          product: {
            id: productData.id,
            name: productData.name,
            sku: productData.sku,
            image_url: productData.image_url,
            brand: productData.brand,
            pos_vat: productData.pos_vat,
            normal_price: parseFloat(productData.normal_price),
            discount_price: productData.discount_price
              ? parseFloat(productData.discount_price)
              : undefined,
            global_stock: parseFloat(productData.global_stock),
            slug: productData.slug,
            variation: productData.variation
          }
        }
      })
    )
    cartItems.value = items
  } catch (error) {
    console.error('Error fetching product details:', error)
  }
}

// --------------------
// Session Helpers
// --------------------
function loadShippingFromSession() {
  const shippingData = sessionStorage.getItem('shipping_details')
  if (shippingData) {
    const parsed = JSON.parse(shippingData)
    selectedShippingMethod.value = parsed.method_id || null

    if (parsed.is_store_pickup) {
      selectedBranchId.value = parsed.branch_id
      selectedBranch.value = parsed.branch_details
      shippingStore.setShippingMethod('collect_in_store')
    } else {
      shippingStore.setShippingMethod('home_delivery')
    }

    isShippingMethodConfirmed.value = true
  }
}

function saveShippingToSession() {
  const shippingData = {
    method_id: selectedShippingMethod.value,
    is_store_pickup: !!selectedShippingDetails.value?.in_store_pickup || false,
    branch_id: selectedBranchId.value,
    branch_details: selectedBranch.value
  }
  sessionStorage.setItem('shipping_details', JSON.stringify(shippingData))
}

// --------------------
// Step Actions
// --------------------
function toggleDelivery() {
  if (!isDeliveryCompleted.value) {
    isDeliveryExpanded.value = !isDeliveryExpanded.value
  }
}

function togglePayment() {
  if (!isPaymentCompleted.value && isDeliveryCompleted.value) {
    isPaymentExpanded.value = !isPaymentExpanded.value
  }
}

function toggleConfirmation() {
  if (!isConfirmationCompleted.value && isPaymentCompleted.value) {
    isConfirmationExpanded.value = !isConfirmationExpanded.value
  }
}

async function continueToPayment() {
  if (!isShippingMethodConfirmed.value) {
    toastStore.triggerToast(
      'Please confirm your shipping method before proceeding.',
      'warning'
    )
    return
  }

  // Add extra payment status check before proceeding
  try {
    isCheckingStatus.value = true
    const response = await shopAPI.checkPendingOrderStatus()

    if (response.success && response.data.is_completed) {
      // Order is already paid, update UI accordingly
      isDeliveryCompleted.value = true
      isPaymentCompleted.value = true
      isPaymentExpanded.value = false
      isConfirmationExpanded.value = true
      isConfirmationCompleted.value = true

      sessionStorage.setItem('checkout_state', JSON.stringify({
        delivery_completed: true,
        payment_completed: true
      }))

      toastStore.triggerToast('This order has already been paid!', 'info')
      return
    }

    // If not paid, proceed with payment step
    isDeliveryCompleted.value = true
    isPaymentExpanded.value = true
    isConfirmationExpanded.value = false
  } catch (error) {
    console.error('Error checking payment status:', error)
    // Continue anyway if check fails
    isDeliveryCompleted.value = true
    isPaymentExpanded.value = true
    isConfirmationExpanded.value = false
  } finally {
    isCheckingStatus.value = false
  }
}

function completePayment() {
  if (isPaymentCompleted.value) return
  isPaymentCompleted.value = true
  toastStore.triggerToast(
    'Payment completed. Proceed to order confirmation.',
    'success'
  )
}

function completeConfirmation() {
  if (isConfirmationCompleted.value) return
  isConfirmationCompleted.value = true
  toastStore.triggerToast('Order placed successfully!', 'success')
}

// --------------------
// Shipping Method Logic
// --------------------
function handleShippingMethodChange(method) {
  if (!method || !method.is_enabled) return
  if (method.id === selectedShippingMethod.value) return

  selectedShippingMethod.value = method.id
  selectedBranchId.value = null
  selectedBranch.value = null
  isShippingMethodConfirmed.value = false
  sessionStorage.removeItem('shipping_details')

  if (method.in_store_pickup) {
    shippingStore.setShippingMethod('collect_in_store')
  } else {
    shippingStore.setShippingMethod('home_delivery')
  }
}

function handleBranchSelection(branchId) {
  // Called when <BranchSelector> emits "update:modelValue"
  selectedBranchId.value = branchId
  // Find the branch from localBranches
  const found = localBranches.value.find((b) => b.id === branchId) || null
  selectedBranch.value = found
}

async function confirmShippingMethod() {
  if (
    selectedShippingMethod.value === pickupMethod.value?.id &&
    !selectedBranchId.value
  ) {
    toastStore.triggerToast('Please select a branch for collection.', 'warning')
    return
  }

  try {
    // If user is authenticated, update the pending order's delivery method
    if (userStore.isAuthenticated) {
      const response = await shopAPI.changeDeliveryMethod(
        selectedShippingMethod.value,
        selectedShippingDetails.value?.in_store_pickup
          ? selectedBranchId.value
          : null
      )
      if (!response.success) {
        throw new Error(response.message || 'Failed to update delivery method')
      }
    }

    isShippingMethodConfirmed.value = true
    saveShippingToSession()
    toastStore.triggerToast('Shipping method confirmed.', 'success')
  } catch (error) {
    console.error('Error confirming shipping method:', error)
    toastStore.triggerToast('Failed to confirm shipping method.', 'error')
  }
}

async function resetShippingMethod() {
  try {
    if (userStore.isAuthenticated) {
      // Attempt to find any fallback method that is enabled
      const fallbackMethod = shippingMethods.value.find(
        (m) => m.is_enabled
      )
      // If none found, just clear shipping method
      if (!fallbackMethod) {
        selectedShippingMethod.value = null
        selectedBranchId.value = null
        selectedBranch.value = null
        isShippingMethodConfirmed.value = false
        sessionStorage.removeItem('shipping_details')
        toastStore.triggerToast(
          'No valid shipping method found. Please select one again.',
          'info'
        )
        return
      }

      // Attempt to set fallback in DB
      const response = await shopAPI.changeDeliveryMethod(
        fallbackMethod.id,
        fallbackMethod.in_store_pickup ? selectedBranchId.value : null
      )
      if (!response.success) {
        throw new Error(response.message || 'Failed to reset shipping method')
      }

      selectedShippingMethod.value = fallbackMethod.id
      selectedBranchId.value = null
      selectedBranch.value = null
      isShippingMethodConfirmed.value = false
      saveShippingToSession()
      toastStore.triggerToast('Shipping method reset.', 'info')
    } else {
      // For guest users, simply reset
      selectedShippingMethod.value = null
      selectedBranchId.value = null
      selectedBranch.value = null
      isShippingMethodConfirmed.value = false
      sessionStorage.removeItem('shipping_details')

      toastStore.triggerToast(
        'Shipping method has been reset. Please select a new method.',
        'info'
      )
    }
  } catch (error) {
    console.error('Error resetting shipping method:', error)
    toastStore.triggerToast('Failed to reset shipping method.', 'error')
  }
}

// Add helper functions for payment states
async function handlePaymentSuccess() {
  isDeliveryCompleted.value = true
  isPaymentCompleted.value = true
  isPaymentExpanded.value = false
  isConfirmationExpanded.value = true
  isConfirmationCompleted.value = true

  // Clear cart data from session storage
  sessionStorage.removeItem('cart')
  // Reset cart store
  await cartStore.clearCart()

  sessionStorage.setItem('checkout_state', JSON.stringify({
    delivery_completed: true,
    payment_completed: true,
    payment_processing: false
  }))

  toastStore.triggerToast('Order confirmed and payment completed!', 'success')
  isCheckingStatus.value = false
  isPaymentProcessing.value = false
}

function handlePaymentPending() {
  sessionStorage.setItem('checkout_state', JSON.stringify({
    payment_processing: true
  }))

  toastStore.triggerToast(
    'Your payment is being processed. Please wait before attempting another payment.',
    'info'
  )
  isPaymentProcessing.value = true
  isCheckingStatus.value = false
}

function handlePaymentError() {
  sessionStorage.removeItem('checkout_state')
  toastStore.triggerToast('Error confirming payment. Please check your orders page.', 'error')
  isCheckingStatus.value = false
  isPaymentProcessing.value = false
}

// Update initiatePayPalPayment function
async function initiatePayPalPayment() {
  try {
    loading.value = true
    isCheckingStatus.value = true

    // Check saved state first
    const savedState = sessionStorage.getItem('checkout_state')
    if (savedState) {
      const state = JSON.parse(savedState)

      // If payment was previously initiated, add extra verification delay
      if (state.payment_initiated) {
        toastStore.triggerToast('Verifying previous payment status...', 'info')
        await new Promise(resolve => setTimeout(resolve, 5000)) // 5 second delay for verification

        // Check if payment was completed
        const statusResponse = await shopAPI.checkPendingOrderStatus()
        if (statusResponse.success && statusResponse.data.is_completed) {
          await handlePaymentSuccess()
          return
        }
      }
    }

    // Check current payment status
    const statusResponse = await shopAPI.checkPendingOrderStatus()
    if (statusResponse.success && statusResponse.data.is_completed) {
      await handlePaymentSuccess()
      return
    }

    // If not paid, proceed with PayPal payment
    const response = await shopAPI.getOrderSummary()
    if (!response.success) {
      throw new Error('Failed to get order summary')
    }

    // Mark delivery as completed and payment as initiated
    isDeliveryCompleted.value = true

    // Save updated state to session storage
    sessionStorage.setItem(
      'checkout_state',
      JSON.stringify({
        delivery_completed: true,
        payment_initiated: true,
        payment_completed: false
      })
    )

    // Always return to checkout page
    const returnUrl = `${window.location.origin}/shop/checkout`
    window.location.href = `/paypal/direct-payment?amount=${response.total}&currency=GBP&return_url=${encodeURIComponent(returnUrl)}`
  } catch (error) {
    console.error('Failed to initiate PayPal payment:', error)
    toastStore.triggerToast('Failed to initiate payment. Please try again.', 'error')
  } finally {
    loading.value = false
    isCheckingStatus.value = false
  }
}

// Update the payment button text helper
function getPaymentButtonText() {
  const savedState = sessionStorage.getItem('checkout_state')
  if (savedState) {
    const state = JSON.parse(savedState)
    if (state.payment_initiated && isCheckingStatus.value) {
      return 'Verifying Previous Payment...'
    }
  }

  if (isPaymentProcessing) return 'Payment Processing...'
  if (isCheckingStatus.value) return 'Checking Status...'
  if (loading.value) return 'Processing...'
  return 'Pay with PayPal'
}

// Add goToOrders function in the script section
function goToOrders() {
  router.push('/accountinformation/orders')
}

// Add goToAdmin function in the script section
function goToAdmin() {
  router.push('/accountinformation/orders')
}

// Add goToShop function in the script section
function goToShop() {
  router.push('/shop')
}

// Update the verifyOrderCompletion function
async function verifyOrderCompletion() {
  try {
    const response = await shopAPI.checkPendingOrderStatus()
    if (response.success) {
      if (response.data.is_completed) {
        // Clear cart data
        sessionStorage.removeItem('cart')
        await cartStore.clearCart()

        // Update UI state
        isDeliveryCompleted.value = true
        isPaymentCompleted.value = true
        isPaymentExpanded.value = false
        isConfirmationExpanded.value = true
        isConfirmationCompleted.value = true

        sessionStorage.setItem('checkout_state', JSON.stringify({
          delivery_completed: true,
          payment_completed: true
        }))

        toastStore.triggerToast('Order confirmed and payment completed!', 'success')
        return true
      } else if (response.data.is_pending) {
        // Reset UI for pending order
        isDeliveryExpanded.value = true
        isDeliveryCompleted.value = false
        isPaymentExpanded.value = false
        isPaymentCompleted.value = false
        isConfirmationExpanded.value = false
        isConfirmationCompleted.value = false

        sessionStorage.removeItem('checkout_state')
      }
    }
    return false
  } catch (error) {
    console.error('Error verifying order completion:', error)
    return false
  }
}
</script>

<style scoped>
@import '@/assets/styles/CheckoutSteps.module.css';
</style>