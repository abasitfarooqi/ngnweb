<template>
    <div class="border p-6 bg-white sticky top-4">
        <h2 class="text-xl font-medium mb-4">Order Summary</h2>

        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-medium">£{{ subtotal }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-600">VAT ({{ vatRate }}%)</span>
                <span class="font-medium">£{{ tax }}</span>
            </div>

            <div class="border-t pt-3">
                <div class="flex justify-between">
                    <span class="font-medium">Total</span>
                    <span class="font-medium">£{{ total }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps({
    subtotal: {
        type: String,
        required: true
    },
    tax: {
        type: String,
        required: true
    },
    total: {
        type: String,
        required: true
    },
    vatRate: {
        type: Number,
        required: true
    }
});
</script>