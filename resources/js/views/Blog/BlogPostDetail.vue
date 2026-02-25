<template>
    <DefaultLayout>
  <div class="container mx-auto p-6 bg-white shadow-md rounded-lg" itemscope itemtype="https://schema.org/BlogPosting">
    <div v-if="loading" class="text-center text-gray-500">Loading post...</div>
    <div v-if="error" class="text-red-500 text-center font-semibold">{{ error }}</div>
    <div v-if="post && !loading && !error" itemprop="mainEntity">
      <h3 class="text-2xl font-semibold mb-1 text-gray-600" style="color: rgb(195, 25, 36);letter-spacing: 2px;" itemprop="articleSection">{{ post.category.name }}</h3>
      <h1 class="text-5xl font-extrabold text-gray-800" itemprop="headline">{{ post.title }}</h1>
    
      <div class="relative mb-6" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
        <div class="slider">
          <div class="flex overflow-x-auto">
            <div class="gallery">
              <img v-for="image in post.images" :src="`https://neguinhomotors.co.uk/storage/${image.path}`" :alt="post.title" class="w-full h-60 object-cover rounded-lg shadow-lg flex-shrink-0" :key="image.id" itemprop="url" />
            </div>
          </div>
        </div>
      </div>
      <div class="prose prose-lg text-gray-700" itemprop="articleBody">
        <p v-html="post.content"></p>
      </div>
      <meta itemprop="datePublished" content="2023-10-01" /> <!-- Replace with actual publication date -->
      <meta itemprop="author" content="Author Name" /> <!-- Replace with actual author name -->


    <ServiceEnquiryForm />


      
    </div>
  </div>
  

  </DefaultLayout>
</template>

<script>
import { ref, onMounted } from 'vue';
import { shopAPI } from '@/services/api';
import ServiceEnquiryForm from './ServiceEnquiryForm.vue';


export default {
  components: {
    ServiceEnquiryForm,
  },
  props: {
    slug: {
      type: String,
      required: true,
    },
  },
  setup(props) {
    const post = ref(null);
    const loading = ref(true);
    const error = ref(null);

    onMounted(async () => {
      try {
        post.value = await shopAPI.getPostBySlug(props.slug);
      } catch (err) {
        error.value = 'Failed to load post. Please try again later.';
      } finally {
        loading.value = false;
      }
    });

    return { post, loading, error };
  },
};
</script>

<style scoped>
/* Add any additional styles here if needed */
</style>