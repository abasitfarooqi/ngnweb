<!-- File: resources/js/components/common/Pagination.vue -->
<template>
    <div class="pagination-container mt-8 flex justify-center">
      <nav aria-label="Pagination">
        <ul class="inline-flex -space-x-px">
          <!-- Previous Page Button -->
          <li>
            <button
              @click="goToPage(currentPage - 1)"
              :disabled="currentPage === 1"
              class="pagination-button px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
              aria-label="Previous Page"
            >
              Previous
            </button>
          </li>
  
          <!-- Page Numbers with Ellipsis for Large Numbers -->
          <li v-for="page in displayedPages" :key="page">
            <button
              @click="goToPage(page)"
              :aria-current="page === currentPage ? 'page' : undefined"
              :aria-label="'Go to page ' + page"
              :class="[
                'pagination-number px-4 py-2 border-t border-b border-gray-300 hover:bg-gray-100 hover:text-gray-700',
                {
                  'bg-ngn-primary-600 text-white': page === currentPage,
                  'bg-white text-gray-500': page !== currentPage,
                },
              ]"
            >
              {{ page }}
            </button>
          </li>
  
          <!-- Next Page Button -->
          <li>
            <button
              @click="goToPage(currentPage + 1)"
              :disabled="currentPage === totalPages"
              class="pagination-button px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
              aria-label="Next Page"
            >
              Next
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </template>
  
  <script lang="ts" setup>
  import { defineProps, defineEmits, computed } from 'vue';
  
  // Define Props
  const props = defineProps<{
    currentPage: number;
    totalPages: number;
  }>();
  
  // Define Emits
  const emit = defineEmits<{
    (e: 'page-changed', page: number): void;
  }>();
  
  // Calculate the range of page numbers to display
  const displayedPages = computed(() => {
    const pages = [];
    const maxPagesToShow = 5;
    let startPage = Math.max(props.currentPage - Math.floor(maxPagesToShow / 2), 1);
    let endPage = startPage + maxPagesToShow - 1;
  
    if (endPage > props.totalPages) {
      endPage = props.totalPages;
      startPage = Math.max(endPage - maxPagesToShow + 1, 1);
    }
  
    for (let i = startPage; i <= endPage; i++) {
      pages.push(i);
    }
  
    return pages;
  });
  
  // Handle page navigation
  const goToPage = (page: number) => {
    if (page < 1 || page > props.totalPages) return;
    emit('page-changed', page);
  };
  </script>
  
  <style scoped>
  .pagination-container {
    /* Center the pagination */
  }
  
  .pagination-button:hover,
  .pagination-number:hover {
    @apply bg-ngn-primary-500 text-white;
  }
  
  .pagination-number.bg-ngn-primary-600 {
    /* Active page styling */
    @apply bg-ngn-primary-600 text-white;
  }
  
  .disabled:opacity-50 {
    /* Disabled button styling */
  }
  </style>