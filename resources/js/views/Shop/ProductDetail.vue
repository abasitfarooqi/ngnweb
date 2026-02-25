<!-- File: resources/js/views/Shop/ProductDetail.vue -->
<template>
  <DefaultLayout>
    <div>
      <h2>Product Detail</h2>
      <p>Details for product with ID: {{ id }}</p>
    </div>
  </DefaultLayout>
</template>

<script setup>
import DefaultLayout from '@/components/layout/DefaultLayout.vue';
import { useRoute } from 'vue-router';
const route = useRoute();
const id = route.params.id;
</script>

<style scoped></style>
