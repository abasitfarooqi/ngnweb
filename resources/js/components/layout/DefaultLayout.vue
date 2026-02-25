<!-- File: resources/js/components/layout/DefaultLayout.vue -->
<template>
  <div class="default-layout">
    <!-- <Navigation /> -->
    <main class="">
        <slot></slot>
    </main>
  </div>
</template>

<script setup>
import Navigation from '@/components/common/Navigation.vue';
</script>

<style scoped>
.default-layout {
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
  padding: 10px;
}

main.shop-page-setting{
  padding: 10px 0 10px 0 !important;
}
footer {
  text-align: center;
  padding: 10px;
  background-color: #f8f9fa;
}
</style>
