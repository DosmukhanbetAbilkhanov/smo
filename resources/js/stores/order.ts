import { orderApi } from '@/services/api';
import type { CreateOrderData, Order } from '@/types/api';
import { defineStore } from 'pinia';
import { ref } from 'vue';

/**
 * Order Store
 * Manages order state and operations
 */
export const useOrderStore = defineStore('order', () => {
    // State
    const orders = ref<Order[]>([]);
    const currentOrder = ref<Order | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    // Actions
    async function fetchOrders() {
        loading.value = true;
        error.value = null;

        try {
            const response = await orderApi.getAll();
            orders.value = response.data;
            return response.data;
        } catch (err: unknown) {
            error.value =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to fetch orders';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function fetchOrderById(id: number) {
        loading.value = true;
        error.value = null;

        try {
            const response = await orderApi.getById(id);
            currentOrder.value = response.data;
            return response.data;
        } catch (err: unknown) {
            error.value =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to fetch order';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createOrder(data: CreateOrderData) {
        loading.value = true;
        error.value = null;

        try {
            const response = await orderApi.create(data);
            currentOrder.value = response.data;
            return response.data;
        } catch (err: unknown) {
            error.value =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to create order';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function cancelOrder(id: number) {
        loading.value = true;
        error.value = null;

        try {
            const response = await orderApi.cancel(id);
            // Update current order if it's the same one
            if (currentOrder.value?.id === id) {
                currentOrder.value = response.data;
            }
            // Update in orders list
            const index = orders.value.findIndex((o) => o.id === id);
            if (index !== -1) {
                orders.value[index] = response.data;
            }
            return response.data;
        } catch (err: unknown) {
            error.value =
                err && typeof err === 'object' && 'message' in err
                    ? (err.message as string)
                    : 'Failed to cancel order';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearCurrentOrder() {
        currentOrder.value = null;
    }

    function clearError() {
        error.value = null;
    }

    return {
        // State
        orders,
        currentOrder,
        loading,
        error,
        // Actions
        fetchOrders,
        fetchOrderById,
        createOrder,
        cancelOrder,
        clearCurrentOrder,
        clearError,
    };
});
