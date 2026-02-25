<!-- File: resources/js/components/Shop/OrderSummeryCheckout.vue -->
<template>
    <div class="border bg-white sticky top-4">
        <div class="px-6 pt-6">
            <h3 class="text-xl font-medium mb-4">Order Summary</h3>

            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary-500"></div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="text-red-500 text-center py-4">
                {{ error }}
            </div>

            <!-- Scrollable cart items section with flex-grow -->
            <div v-else class="max-h-[calc(100vh-300px)] overflow-y-auto mb-6">
                <div v-for="item in cartItems" :key="item.id" class="mb-4">
                    <div class="w-full h-48 overflow-hidden rounded mb-2">
                        <img 
                            :src="item.image_url" 
                            :alt="item.name" 
                            class="w-full h-full object-contain"
                        >
                    </div>
                    <div>
                        <h4 class="font-medium">{{ item.name }}</h4>
                        <div class="flex justify-between text-gray-600 mt-1">
                            <span>Quantity: {{ item.quantity }}</span>
                            <span>£{{ calculateItemPrice(item) }}</span>
                        </div>
                    </div>
                    <hr class="my-4 border-gray-400" />
                </div>
            </div>
        </div>

        <!-- Summary section -->
        <div class="border-t pt-2 space-y-1 bg-gray-200 px-6 pb-6">
            <div class="flex justify-between">
                <span class="text-gray-700">Subtotal</span>
                <span>£{{ formatPrice(subtotal) }}</span>
            </div>
            <div v-if="shippingCost > 0" class="flex justify-between">
                <span class="text-gray-700">Shipping</span>
                <span>£{{ formatPrice(shippingCost) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-bold">Total</span>
                <span class="font-bold">£{{ formatPrice(total) }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useCartStore } from '@/stores/modules/cart'
import { shopAPI } from '@/services/api'

const cartStore = useCartStore()
const loading = ref(true)
const error = ref(null)
const shippingCost = ref(0)
const orderItems = ref([])

// Get cart items from store
const cartItems = computed(() => orderItems.value)

// Calculate subtotal
const subtotal = computed(() => {
    return cartItems.value.reduce((total, item) => {
        return total + (item.unit_price * item.quantity)
    }, 0)
})

// Calculate total with shipping
const total = computed(() => {
    return subtotal.value + shippingCost.value
})

// Helper function to calculate item price
function calculateItemPrice(item) {
    return item.unit_price
}

// Format price to 2 decimal places
function formatPrice(price) {
    return Number(price).toFixed(2)
}

// Fetch order summary data
async function fetchOrderSummary() {
    try {
        loading.value = true
        error.value = null
        const response = await shopAPI.getOrderSummary()
        if (response.success) {
            orderItems.value = response.items
            shippingCost.value = parseFloat(response.shipping_cost)
        }
    } catch (err) {
        console.error('Error fetching order summary:', err)
        error.value = 'Failed to load order summary'
    } finally {
        loading.value = false
    }
}

onMounted(async () => {
    await fetchOrderSummary()
})

// Emit the total price when it changes
watch(total, (newTotal) => {
    emit('update-total', newTotal);
});

// Add the emit function to the setup
const emit = defineEmits(['update-total']);
</script>