import { cartApi } from '@/services/api';
import type { AddToCartData, Cart, UpdateCartItemData } from '@/types/api';
import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

/**
 * Cart Store
 * Manages shopping cart state and operations
 */
export const useCartStore = defineStore('cart', () => {
    // State
    const cart = ref<Cart | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    // Computed
    const itemsCount = computed(() => cart.value?.items_count || 0);
    const total = computed(() => cart.value?.total || 0);
    const isEmpty = computed(() => !cart.value?.items?.length);
    const items = computed(() => cart.value?.items || []);

    // Actions
    async function fetchCart() {
        loading.value = true;
        error.value = null;

        try {
            const response = await cartApi.getCart();
            cart.value = response.data;
            return response.data;
        } catch (err: unknown) {
            error.value =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to fetch cart';
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
            // Refresh cart after adding item
            await fetchCart();
            return response.data;
        } catch (err: unknown) {
            error.value =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to add item to cart';
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
            // Refresh cart after updating
            await fetchCart();
            return response.data;
        } catch (err: unknown) {
            error.value =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to update cart item';
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
            // Refresh cart after removing item
            await fetchCart();
        } catch (err: unknown) {
            error.value =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to remove cart item';
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
            cart.value = null;
        } catch (err: unknown) {
            error.value =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to clear cart';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    return {
        // State
        cart,
        loading,
        error,
        // Computed
        itemsCount,
        total,
        isEmpty,
        items,
        // Actions
        fetchCart,
        addItem,
        updateQuantity,
        removeItem,
        clearCart,
        clearError,
    };
});
