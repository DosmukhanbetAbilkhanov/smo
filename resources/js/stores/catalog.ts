import { categoryApi, productApi } from '@/services/api';
import type {
    Category,
    PaginatedResponse,
    Product,
    ProductFilters,
} from '@/types/api';
import { defineStore } from 'pinia';
import { ref } from 'vue';

/**
 * Catalog Store
 * Manages categories and products state
 */
export const useCatalogStore = defineStore('catalog', () => {
    // State
    const categories = ref<Category[]>([]);
    const currentCategory = ref<Category | null>(null);
    const products = ref<PaginatedResponse<Product> | null>(null);
    const currentProduct = ref<Product | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    // Actions
    async function fetchCategories() {
        loading.value = true;
        error.value = null;

        try {
            const response = await categoryApi.getAll();
            categories.value = response.data;
            return response.data;
        } catch (err: unknown) {
            error.value =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to fetch categories';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function fetchCategoryById(id: number) {
        loading.value = true;
        error.value = null;

        try {
            const response = await categoryApi.getById(id);
            currentCategory.value = response.data;
            return response.data;
        } catch (err: unknown) {
            error.value =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to fetch category';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function fetchCategoryBySlug(slug: string) {
        loading.value = true;
        error.value = null;

        try {
            const response = await categoryApi.getBySlug(slug);
            currentCategory.value = response.data;
            return response.data;
        } catch (err: unknown) {
            error.value =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to fetch category';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function fetchProducts(filters?: ProductFilters) {
        loading.value = true;
        error.value = null;

        try {
            const response = await productApi.getAll(filters);
            products.value = response.data;
            return response.data;
        } catch (err: unknown) {
            error.value =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to fetch products';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function fetchProductById(id: number) {
        loading.value = true;
        error.value = null;

        try {
            const response = await productApi.getById(id);
            currentProduct.value = response.data;
            return response.data;
        } catch (err: unknown) {
            error.value =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to fetch product';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function searchProducts(query: string, filters?: ProductFilters) {
        loading.value = true;
        error.value = null;

        try {
            const response = await productApi.search(query, filters);
            products.value = response.data;
            return response.data;
        } catch (err: unknown) {
            error.value =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to search products';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearCurrentCategory() {
        currentCategory.value = null;
    }

    function clearCurrentProduct() {
        currentProduct.value = null;
    }

    function clearError() {
        error.value = null;
    }

    return {
        // State
        categories,
        currentCategory,
        products,
        currentProduct,
        loading,
        error,
        // Actions
        fetchCategories,
        fetchCategoryById,
        fetchCategoryBySlug,
        fetchProducts,
        fetchProductById,
        searchProducts,
        clearCurrentCategory,
        clearCurrentProduct,
        clearError,
    };
});
