<!-- File: resources/js/components/Shop/ProductCard.vue -->
<template>
  <!-- <Card> -->
    <div class="product-card">
      <Toast />
      <router-link :to="`/shop/product/${product.slug || product.id}`" class="product-name font-three fw-600">
        <div class="product-image ngn-image-effect img-inout-effect-1">
          <div class="image-box w-full flex items-center justify-center overflow-hidden">
            <img :src="getImageUrl(product.image_url)" :alt="product.name || 'Product image'" @error="handleImageError"
              class="object-contain w-full h-full" />
          </div>

          <!-- Stock Badge -->
          <span v-if="product.in_stock" class="stock-badge font-three fw-600 in-stock">In Stock</span>
          <span v-else class="stock-badge font-three fw-600 out-of-stock">Out of Stock</span>

          <!-- New Badges -->
          <div v-if="product.discount_percentage" class="u-tag" data-type="saving">
            {{ product.discount_percentage }}% OFF
          </div>
          <div v-if="product.points" class="u-tag" data-type="points">
            {{ product.points }} points
          </div>
        </div>
      </router-link>
      <router-link :to="`/shop/product/${product.slug}`"   class="product-info">
        <div class="product-content">
          <router-link :to="`/shop/product/${product.slug}`" class="product-name font-three fw-600">
            <h3 class="product-name font-three fw-600">{{ product.name }}</h3>
          </router-link>

          <p class="product-brand">{{ product.brand }}</p>
          <div class="product-price ngn-active-color">
            <span v-if="product.normal_price === 0" class="current-price ngn-active-color">Ask for price</span>
            <span v-else class="current-price ngn-active-color">£{{ product.discount_price || product.normal_price
              }}</span>
            <span v-if="product.discount_price && product.normal_price !== 0" class="discount-price">£{{
              product.normal_price }}</span>
          </div>
          <div class="product-meta mb-2">
            <span class="product-rating">★ {{ product.rating }}</span>
            <span class="product-reviews">({{ product.review_count }})</span>
          </div>
        </div>

      </router-link>

      <Button :disabled="false" @click="product.in_stock ? handleAddToCart(product) : handlePhoneCall()"
        variant="primary" class="ngn-btn">
        {{ product.in_stock ? 'Add to Cart' : 'Enquiry' }}
      </Button>

    </div>
  <!-- </Card> -->

</template>

<script setup lang="ts">
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import Toast from '@/components/ui/Toast.vue';
import { useToastStore } from '@/stores/modules/toast';
import { useCartStore } from '@/stores/modules/cart';
import { useRouter } from 'vue-router';

const router = useRouter();
const toastStore = useToastStore();
const cartStore = useCartStore();

interface Product {
  id: number;
  name: string;
  slug: string;
  image_url: string;
  normal_price: number;
  discount_price?: number;
  discount_percentage?: number;
  points?: number;
  brand: string;
  in_stock: boolean;
  rating: number;
  review_count: number;
  global_stock?: number;
}

defineProps<{
  product: Product
}>();

const emit = defineEmits(['add-to-cart', 'add-to-wishlist']);

const handlePhoneCall = () => {
  window.location.href = 'tel:02083141498';
};

const handleAddToCart = async (product: Product) => {
  try {
    // takes to product page using slug
    router.push(`/shop/product/${product.slug}`);
    // await cartStore.addToCart(product.id);
    // toastStore.triggerToast('Item added to cart!', 'success');
    // emit('add-to-cart', product);
  } catch (error) {
    // toastStore.triggerToast('Failed to add item to cart.', 'error');
    // console.error('Error adding to cart:', error);
  }
};

const addToWishlist = (product: Product) => {
  emit('add-to-wishlist', product);
};


const getImageUrl = (url: string | null): string => {
  // If no URL provided, return placeholder
  if (!url) {
    return 'https://neguinhomotors.co.uk/assets/img/no-image.png';
  }

  // Try to parse if it's a JSON string array
  try {
    if (typeof url === 'string' && url.startsWith('[')) {
      const parsedUrl = JSON.parse(url);
      if (Array.isArray(parsedUrl) && parsedUrl.length > 0) {
        return `/assets/images/store/products/${parsedUrl[0]}`;
      }
    }
  } catch (e) {
    console.log('JSON parse error:', e);
  }

  // If it starts with public/assets, remove 'public' prefix and construct full path
  if (url.startsWith('public/assets/')) {
    return url.replace('public/', '/');
  }

  // If it's already an array (not string)
  if (Array.isArray(url) && url.length > 0) {
    return `/assets/images/store/products/${url[0]}`;
  }

  // If it's a full HTTPS URL, return as is
  if (url.startsWith('http://') || url.startsWith('https://')) {
    return url;
  }

  // If it's just a filename, construct the full path
  if (!url.includes('/')) {
    return `/assets/images/store/products/${url}`;
  }

  // Default case
  return url;
};

const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement;
  img.src = 'https://neguinhomotors.co.uk/assets/img/no-image.png';
};
</script>
<style scoped>
@import '@/assets/styles/ProductCard.module.css';
</style>
