<!-- resources/js/components/Shop/ProductFilters.vue -->
<template>
    <aside class="filters-sidebar">
        <div class="flex items-center justify-between">
            <h1 class="text-xl ml-2">Filters</h1>
            <!-- Toggle button for mobile view -->
            <button @click="toggleMobileFilters" class="lg:hidden p-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>

        <!-- Filter sections -->
        <div :class="{ 'hidden lg:block': !isMobileFiltersOpen }">
            <div v-for="(section, index) in filterSections" :key="index" class="filter-section">
                <button @click="section.isOpen = !section.isOpen"
                    class="w-full flex items-center justify-between p-2 text-left transition-colors">
                    <span class="font-medium text-gray-900">{{ section.title }}</span>
                    <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': section.isOpen }"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <div v-show="section.isOpen" class="filter-content">
                    <div v-if="section.type === 'checkbox'" class="space-y-2">
                        <template v-if="section.key === 'brands' || section.key === 'categories'">
                            <label v-for="option in filterOptions[section.key]" :key="option.id"
                                class="flex items-center p-2 hover:bg-gray-50 cursor-pointer group">
                                <input type="checkbox" :value="option.id" v-model="selectedFilters[section.key]"
                                    class="w-5 h-5 text-danger-600 border-gray-200"
                                    @change="emitFilterChange">
                                <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900">
                                    {{ option.name }}
                                </span>
                            </label>
                        </template>
                        <template v-else>
                            <label v-for="option in filterOptions[section.key]" :key="option"
                                class="flex items-center p-2 hover:bg-gray-50 cursor-pointer group">
                                <input type="checkbox" :value="option" v-model="selectedFilters[section.key]"
                                    class="w-5 h-5 text-danger-600 border-gray-200"
                                    @change="emitFilterChange">
                                <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900">
                                    {{ option }}
                                </span>
                            </label>
                        </template>
                    </div>

                    <div v-else-if="section.type === 'color'" class="space-y-2">
                        <label v-for="color in filterOptions.colors" :key="color.value"
                            class="flex items-center p-2 hover:bg-gray-50 rounded-md cursor-pointer group">
                            <input type="checkbox" :value="color.value" v-model="selectedFilters.colors"
                                class="w-4 h-4 text-danger-600 border-gray-300"
                                @change="emitFilterChange">
                            <span class="ml-3 w-6 h-6 rounded-full border border-gray-200"
                                :style="{ backgroundColor: color.hex }"></span>
                            <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900">
                                {{ color.name }}
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4 px-3">
                <!-- Has Image Checkbox -->
                <label class="flex items-center p-2 hover:bg-gray-50 cursor-pointer group mb-4">
                    <input type="checkbox" v-model="localHasImage" @change="handleHasImageChange"
                        class="w-5 h-5 text-danger-600 border-gray-200">
                    <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900">
                        Show Products with Image
                    </span>
                </label>

                <button @click="handleReset"
                    class="w-full py-2 px-4 bg-ngn-primary text-white bg-ngn-primary-600 btn-ngn-effect transition-colors">
                    Reset Filters
                </button>
            </div>
        </div>
    </aside>
</template>

<script setup lang="ts">
import { ref, watch, defineEmits, defineProps } from 'vue';

interface BrandOption {
    id: number;
    name: string;
}

interface CategoryOption {
    id: number;
    name: string;
}

interface ColorOption {
    name: string;
    value: string;
    hex: string;
}

interface FilterOptions {
    brands: BrandOption[];
    categories: CategoryOption[];
    sizes: string[];
    colors: ColorOption[];
}

interface SelectedFilters {
    brands: number[];
    categories: number[];
    sizes: string[];
    colors: string[];
}

interface FilterSection {
    title: string;
    key: keyof FilterOptions;
    type: 'checkbox' | 'color';
    isOpen: boolean;
}

const props = defineProps({
    brands: {
        type: Array as () => BrandOption[],
        default: () => []
    },
    categories: {
        type: Array as () => CategoryOption[],
        default: () => []
    },
    hasImage: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['filter-change', 'reset']);

const isMobileFiltersOpen = ref(false);
const localHasImage = ref(props.hasImage);

const toggleMobileFilters = () => {
    isMobileFiltersOpen.value = !isMobileFiltersOpen.value;
};

const filterSections = ref<FilterSection[]>([
    { title: 'Brands', key: 'brands', type: 'checkbox', isOpen: true },
    { title: 'Categories', key: 'categories', type: 'checkbox', isOpen: false },
]);

const filterOptions = ref<FilterOptions>({
    brands: [],
    categories: [],
    sizes: ['XS', 'S', 'M', 'L', 'XL', 'XXL', '2XL', '3XL'],
    colors: [
        { name: 'Black', value: 'black', hex: '#000000' },
        { name: 'White', value: 'white', hex: '#FFFFFF' },
        { name: 'Red', value: 'red', hex: '#FF0000' },
        { name: 'Blue', value: 'blue', hex: '#0000FF' },
        { name: 'Silver', value: 'silver', hex: '#C0C0C0' },
        { name: 'Matte Black', value: 'matte_black', hex: '#1C1C1C' },
    ],
});

const selectedFilters = ref<SelectedFilters>({
    brands: [],
    categories: [],
    sizes: [],
    colors: [],
});

watch(
    () => props.brands,
    (newBrands) => {
        filterOptions.value.brands = newBrands;
    },
    { immediate: true }
);

watch(
    () => props.categories,
    (newCategories) => {
        filterOptions.value.categories = newCategories;
    },
    { immediate: true }
);

watch(
    () => props.hasImage,
    (newValue) => {
        localHasImage.value = newValue;
    }
);

const handleHasImageChange = () => {
    emit('filter-change', {
        ...selectedFilters.value,
        has_image: localHasImage.value
    });
};

const emitFilterChange = () => {
    emit('filter-change', {
        ...selectedFilters.value,
        has_image: localHasImage.value
    });
};

const handleReset = () => {
    selectedFilters.value = {
        brands: [],
        categories: [],
        sizes: [],
        colors: [],
    };
    localHasImage.value = false;
    emit('reset');
};
</script>

<style scoped>
@import '@/assets/styles/ProductFilters.module.css';
</style>