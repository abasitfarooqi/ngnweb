<template>
  <div class="product-grid-header flex justify-between items-center ">
    <div class="flex items-center gap-4">
      <h1 class="text-lg font-medium">SORT BY</h1>
      <select v-model="localSortBy"
        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2  "
        :disabled="loading" @change="updateSortBy">
        <option value="newest">Newest First</option>
        <option value="price_low">Price: Low to High</option>
        <option value="price_high">Price: High to Low</option>
        <option value="name">Name: A to Z</option>
      </select>

      <div class="flex items-center gap-2">
        <span class="text-sm text-gray-600">Show:</span>
        <select v-model="localItemsPerPage"
          class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2  "
          :disabled="loading" @change="updateItemsPerPage">
          <option :value="12">12</option>
          <option :value="24">24</option>
          <option :value="36">36</option>
          <option :value="48">48</option>
        </select>
      </div>
    </div>

    <div class="text-sm text-gray-600">
      <span class="text-primary-500">{{ totalItems }}</span> products found
    </div>
  </div>
</template>

<script lang="ts" setup>
import { defineProps, defineEmits, ref, watch } from 'vue';

interface Props {
  sortBy: string;
  itemsPerPage: number;
  totalItems: number;
  loading: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: 'update:sortBy', value: string): void;
  (e: 'update:itemsPerPage', value: number): void;
}>();

// Local refs to handle two-way binding
const localSortBy = ref(props.sortBy);
const localItemsPerPage = ref(props.itemsPerPage);

// Watch for prop changes to update local refs
watch(
  () => props.sortBy,
  (newVal) => {
    localSortBy.value = newVal;
  }
);

watch(
  () => props.itemsPerPage,
  (newVal) => {
    localItemsPerPage.value = newVal;
  }
);

// Emit events when local refs change
const updateSortBy = () => {
  emit('update:sortBy', localSortBy.value);
};

const updateItemsPerPage = () => {
  emit('update:itemsPerPage', Number(localItemsPerPage.value));
};
</script>

<style scoped>
.product-grid-header {
  /* You can add component-specific styles here if needed */
}
</style>