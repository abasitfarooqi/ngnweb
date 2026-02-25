<!-- File: resources/js/views/Shop/CategoryList.vue -->

<template>
  <DefaultLayout>
    <div class="container mx-auto py-8 px-4">
      <h1 class="text-3xl font-bold mb-6 text-center">Categories</h1>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div
          v-for="category in categories"
          :key="category.id"
          class="img-inout-effect-1 rounded-lg overflow-hidden"
        >
          <img :src="category.image_url" :alt="category.name" class="w-full h-48 object-cover" />
          <div class="absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-300 bg-black bg-opacity-50">
            <span class="text-white text-lg font-semibold">{{ category.name }}</span>
          </div>
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import DefaultLayout from '@/components/layout/DefaultLayout.vue';
import { shopAPI } from '@/services/api';

const categories = ref([]);

const fetchCategories = async () => {
  try {
    categories.value = await shopAPI.getCategories();
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

onMounted(() => {
  fetchCategories();
});
</script>

<style scoped>
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