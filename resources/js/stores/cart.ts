import { cartApi } from '@/services/api';
import type {
    AddToCartData,
    Cart,
    UnifiedCart,
    UpdateCartItemData,
} from '@/types/api';
import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

/**
 * Cart Store
 * Manages shopping cart state and operations using Pinia
 */
export const useCartStore = defineStore('cart', () => {
    // State
    const carts = ref<Cart[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);
    const initialized = ref(false);

    // Computed
    const itemsCount = computed(() => {
        return carts.value.reduce((sum, cart) => sum + cart.items_count, 0);
    });

    const total = computed(() => {
        return carts.value.reduce((sum, cart) => sum + cart.total, 0);
    });

    const isEmpty = computed(() => {
        return carts.value.length === 0 || itemsCount.value === 0;
    });

    const items = computed(() => {
        return carts.value.flatMap((cart) => cart.items || []);
    });

    // Actions
    async function initialize() {
        if (initialized.value) return;

        try {
            await fetchCart();
            initialized.value = true;
        } catch (err) {
            console.warn('Could not initialize cart (user may not be authenticated)');
            initialized.value = true;
        }
    }

    async function fetchCart() {
        loading.value = true;
        error.value = null;

        try {
            const response = await cartApi.getCart();
            carts.value = response.data.carts || [];
            return response.data;
        } catch (err: unknown) {
            const errorMessage =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to fetch cart';
            error.value = errorMessage;
            console.error('Failed to fetch cart:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function addItem(data: AddToCartData) {
        loading.value = true;
        error.value = null;

        try {
            const response = await cartApi.addItem(data);

            // Refresh cart to get updated data
            await fetchCart();

            // Show success message
            console.log('Item added to cart successfully');

            return response.data;
        } catch (err: unknown) {
            const errorMessage =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to add item to cart';
            error.value = errorMessage;
            console.error('Failed to add item to cart:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateQuantity(itemId: number, data: UpdateCartItemData) {
        loading.value = true;
        error.value = null;

        try {
            const response = await cartApi.updateItem(itemId, data);

            // Refresh cart to get updated data
            await fetchCart();

            return response.data;
        } catch (err: unknown) {
            const errorMessage =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to update cart item';
            error.value = errorMessage;
            console.error('Failed to update cart item:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function removeItem(itemId: number) {
        loading.value = true;
        error.value = null;

        try {
            await cartApi.removeItem(itemId);

            // Refresh cart to get updated data
            await fetchCart();
        } catch (err: unknown) {
            const errorMessage =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to remove cart item';
            error.value = errorMessage;
            console.error('Failed to remove cart item:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function clearCart() {
        loading.value = true;
        error.value = null;

        try {
            await cartApi.clearCart();
            carts.value = [];
        } catch (err: unknown) {
            const errorMessage =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to clear cart';
            error.value = errorMessage;
            console.error('Failed to clear cart:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    function reset() {
        carts.value = [];
        loading.value = false;
        error.value = null;
        initialized.value = false;
    }

    return {
        // State
        carts,
        loading,
        error,
        initialized,
        // Computed
        itemsCount,
        total,
        isEmpty,
        items,
        // Actions
        initialize,
        fetchCart,
        addItem,
        updateQuantity,
        removeItem,
        clearCart,
        clearError,
        reset,
    };
});
