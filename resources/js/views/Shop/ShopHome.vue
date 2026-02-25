<!-- resources/js/views/Shop/ShopHome.vue -->
<template>
  <DefaultLayout>
    <div class="shop-container container">

      <!-- Filters Sidebar -->
      <ProductFilters :brands="availableBrands" :categories="availableCategories" :has-image="hasImage"
        @filter-change="handleFilterChange" @reset="handleFilterReset" />

      <!-- Product Grid -->
      <ProductGrid :products="products" :loading="loading" :error="error" :current-page="currentPage"
        :total-pages="totalPages" :total-items="totalItems" :items-per-page="itemsPerPage" :sort-by="sortBy"
        :search-query="searchQuery" @update:current-page="updateCurrentPage" @update:items-per-page="updateItemsPerPage"
        @update:sort-by="updateSortBy" @update:search-query="searchQuery = $event" @retry="fetchProducts"
        @add-to-cart="handleAddToCart" @add-to-wishlist="addToWishlist" />
    </div>
  </DefaultLayout>
</template>

<script setup>
// File URI: resources/js/views/Shop/ShopHome.vue

// Step 1: Imports and Type Definitions
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import DefaultLayout from '@/components/layout/DefaultLayout.vue';
import ProductFilters from '@/components/Shop/ProductFilters.vue';
import ProductGrid from '@/components/Shop/ProductGrid.vue';
import { shopAPI } from '@/services/api';
import { useCartStore } from '@/stores/modules/cart';
import { useToastStore } from '@/stores/modules/toast';

/**
 * @typedef {Object} PaginationMeta
 * @property {number} current_page
 * @property {number} last_page
 * @property {number} per_page
 * @property {number} total
 */

/**
 * @typedef {Object} Product
 * @property {number} id
 * @property {string} name
 * @property {string} image_url
 * @property {number} normal_price
 * @property {number} [discount_price]
 * @property {number} [discount_percentage]
 * @property {number} [points]
 * @property {string} brand
 * @property {boolean} in_stock
 * @property {number} rating
 * @property {number} review_count
 * @property {number} [global_stock]
 */

// Step 2: Router Instances
const route = useRoute();
const router = useRouter();
const cartStore = useCartStore();
const toastStore = useToastStore();

// Step 3: Reactive State Variables
const products = ref([]);
const availableBrands = ref([]);
const availableCategories = ref([]);
const currentPage = ref(parseInt(route.query.page) || 1);
const itemsPerPage = ref(parseInt(route.query.per_page) || 12);
const sortBy = ref(route.query.sort || 'newest');
const searchQuery = ref(route.query.search || '');
const categoryFilter = ref(route.query.category || '');
const categories = ref(route.query.categories || '');
const brandFilter = ref(route.query.brand || '');
const brands = ref(route.query.brands || '');
const hasImage = ref(route.query.has_image === 'true');

const loading = ref(false);
const error = ref(null);
const paginationMeta = ref({
  current_page: 1,
  last_page: 1,
  per_page: 12,
  total: 0
});

// Step 4: Computed Properties
const totalItems = computed(() => paginationMeta.value.total);
const totalPages = computed(() => paginationMeta.value.last_page);

// Step 5: Actions and Methods
// Fetch brands from API
const fetchBrands = async () => {
  try {
    availableBrands.value = await shopAPI.getBrands();

  } catch (err) {
    console.error('Error fetching brands:', err);
  }
};

// Fetch categories from API
const fetchCategories = async () => {
  try {
    availableCategories.value = await shopAPI.getCategories();

  } catch (err) {
    console.error('Error fetching categories:', err);
  }
};

// Step 5: Actions and Methods
// Fetch products from API
const fetchProducts = async () => {
  loading.value = true;
  error.value = null;
  try {
    const filters = {};
    if (searchQuery.value.trim()) filters.search = searchQuery.value.trim();
    if (categoryFilter.value) filters.category = categoryFilter.value;
    if (brandFilter.value) filters.brand = brandFilter.value;
    if (categories.value) filters.categories = categories.value;
    if (brands.value) filters.brands = brands.value;
    if (hasImage.value) filters.has_image = hasImage.value;

    const { data, meta } = await shopAPI.getProducts({
      page: currentPage.value,
      per_page: itemsPerPage.value,
      has_image: hasImage.value,
      sort: sortBy.value,
      filters
    });

    console.log(data);


    products.value = data;
    paginationMeta.value = meta;
    updateURL();

  } catch (err) {
    console.error('Error fetching products:', err);
    error.value = 'Failed to load products. Please try again later.';
  } finally {
    loading.value = false;
  }
};

// Update URL with current query parameters
const updateURL = () => {
  const query = {
    page: currentPage.value,
    per_page: itemsPerPage.value,
    sort: sortBy.value,
    has_image: hasImage.value
  };

  if (searchQuery.value.trim()) query.search = searchQuery.value.trim();
  if (categoryFilter.value) query.category = categoryFilter.value;
  if (brandFilter.value) query.brand = brandFilter.value;
  if (categories.value) query.categories = categories.value;
  if (brands.value) query.brands = brands.value;
  if (hasImage.value) query.has_image = hasImage.value;

  router.replace({
    path: '/shop',
    query
  });
};

// Handle filter changes
const handleFilterChange = (filters) => {

  if (filters.search !== undefined) searchQuery.value = filters.search;

  if (filters.categories && filters.categories.length > 0) {
    categoryFilter.value = filters.categories.join(',');
  } else {
    categoryFilter.value = '';
  }

  if (filters.brands && filters.brands.length > 0) {
    brandFilter.value = filters.brands.join(',');
  } else {
    brandFilter.value = '';
  }

  if (filters.has_image && filters.has_image === true) {
    hasImage.value = true;
  } else {
    hasImage.value = false;
  }

  currentPage.value = 1;
};

// Reset all filters
const handleFilterReset = () => {
  searchQuery.value = '';
  categoryFilter.value = '';
  brandFilter.value = '';
  currentPage.value = 1;
  categories.value = '';
  brands.value = '';
  hasImage.value = false;
  updateURL();
};

// Update current page
const updateCurrentPage = (page) => {
  currentPage.value = page;
};

// Update items per page
const updateItemsPerPage = (items) => {
  itemsPerPage.value = items;
};

// Update sort parameter
const updateSortBy = (sort) => {
  sortBy.value = sort;
};

// Add product to cart
const handleAddToCart = async (product) => {
  try {
    await cartStore.addToCart(product.id);
    toastStore.triggerToast('Item added to cart!', 'success');
  } catch {
    toastStore.triggerToast('Failed to add item.', 'error');
  }
};

// Add product to wishlist
const addToWishlist = async (product) => {
  try {
    await shopAPI.addToWishlist(product.id);
    toastStore.triggerToast('Item added to wishlist!', 'success');
  } catch {
    toastStore.triggerToast('Failed to add to wishlist.', 'error');
  }
};

// Step 6: Watchers (Watch for changes in the filters and update the products) 
watch(
  [
    currentPage,
    itemsPerPage,
    sortBy,
    searchQuery,
    categoryFilter,
    brandFilter,
    categories,
    brands,
    hasImage
  ],
  () => {
    fetchProducts();
  }
);

// Step 7: Lifecycle Hooks
// Initialize state from URL on mount
onMounted(() => {
  fetchBrands();
  fetchCategories();
  // cartStore.fetchCart();
  fetchProducts();
});
</script>

<style scoped>
@import '@/assets/styles/ShopHome.module.css';
</style>
