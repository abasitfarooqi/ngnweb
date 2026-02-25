<!-- File: resources/js/components/ui/Card.vue -->
<template>
  <div class="card">
    <slot></slot>
  </div>
</template>

<script setup>
// Card UI component
</script>

<style scoped>
.card {
  /* Card styles */
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 0px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.card{
  box-shadow: 0 0 0 !important;
  box-shadow: none !important;
}
</style>
