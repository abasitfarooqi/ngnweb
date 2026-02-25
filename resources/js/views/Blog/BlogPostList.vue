<template>
    <DefaultLayout>
        <div class="container mx-auto p-4" itemscope itemtype="https://schema.org/Blog">
            <h1 class="text-3xl font-bold mb-6" itemprop="headline">Blog Posts</h1>
            <div v-if="loading" class="text-center">Loading posts...</div>
            <div v-if="error" class="text-red-500 text-center">{{ error }}</div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" v-if="!loading && !error">
                <div v-for="post in posts" :key="post.id" class="bg-white shadow-md rounded-lg overflow-hidden" itemscope itemprop="blogPost" itemtype="https://schema.org/BlogPosting">
                    <router-link :to="{ name: 'BlogPostDetail', params: { slug: post.slug } }">
                        <img :src="`https://neguinhomotors.co.uk/storage/${post.images[0]?.path}`" alt="Post Image" class="w-full h-48 object-cover" itemprop="image" />
                        <div class="p-4">
                            <h2 class="text-xl font-semibold" itemprop="headline">{{ post.title }}</h2>
                            <p class="text-gray-600 mt-2" itemprop="articleBody">{{ post.seo_description }}</p>
                        </div>
                    </router-link>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>

<script>
import { ref, onMounted } from 'vue';
import { shopAPI } from '@/services/api';

export default {
    setup() {
        const posts = ref([]);
        const loading = ref(true);
        const error = ref(null);

        onMounted(async () => {
            try {
                posts.value = await shopAPI.getPosts();
            } catch (err) {
                error.value = 'Failed to load posts. Please try again later.';
            } finally {
                loading.value = false;
            }
        });

        return { posts, loading, error };
    },
};
</script>

<style scoped>
/* Add any additional styles here if needed */
</style>