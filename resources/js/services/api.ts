import type {
    AddToCartData,
    ApiError,
    ApiResponse,
    Cart,
    CartItem,
    Category,
    CreateOrderData,
    Order,
    PaginatedResponse,
    Product,
    ProductFilters,
    UpdateCartItemData,
} from '@/types/api';
import axios, { AxiosError, AxiosInstance } from 'axios';

/**
 * API Client Service
 * Handles all HTTP requests to the Laravel API
 */

// Create axios instance with default config
const apiClient: AxiosInstance = axios.create({
    baseURL: '/api/v1',
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
    },
    withCredentials: true, // Required for Laravel Sanctum cookie-based auth
    withXSRFToken: true, // Automatically send XSRF token
});

// Request interceptor - add locale header
apiClient.interceptors.request.use(
    (config) => {
        // Get locale from localStorage or default to 'ru'
        const locale = localStorage.getItem('locale') || 'ru';
        config.headers['Accept-Language'] = locale;

        return config;
    },
    (error) => {
        return Promise.reject(error);
    },
);

// Response interceptor - handle errors
apiClient.interceptors.response.use(
    (response) => response,
    (error: AxiosError<ApiError>) => {
        // Handle common error scenarios
        if (error.response) {
            const { status, data } = error.response;

            // Handle 401 Unauthorized - redirect to login
            if (status === 401) {
                window.location.href = '/login';
            }

            // Handle 419 CSRF token mismatch - reload page to get fresh token
            if (status === 419) {
                window.location.reload();
            }

            // Return error data
            return Promise.reject(data);
        }

        // Network error or other error
        return Promise.reject({
            success: false,
            message: error.message || 'Network error',
        });
    },
);

/**
 * Category API
 */
export const categoryApi = {
    getAll: () =>
        apiClient
            .get<ApiResponse<Category[]>>('/categories')
            .then((r) => r.data),

    getById: (id: number) =>
        apiClient
            .get<ApiResponse<Category>>(`/categories/${id}`)
            .then((r) => r.data),

    getBySlug: (slug: string) =>
        apiClient
            .get<ApiResponse<Category>>(`/categories/slug/${slug}`)
            .then((r) => r.data),
};

/**
 * Product API
 */
export const productApi = {
    getAll: (filters?: ProductFilters) =>
        apiClient
            .get<ApiResponse<PaginatedResponse<Product>>>('/products', {
                params: filters,
            })
            .then((r) => r.data),

    getById: (id: number) =>
        apiClient
            .get<ApiResponse<Product>>(`/products/${id}`)
            .then((r) => r.data),

    search: (query: string, filters?: ProductFilters) =>
        apiClient
            .get<ApiResponse<PaginatedResponse<Product>>>('/products/search', {
                params: { query, ...filters },
            })
            .then((r) => r.data),
};

/**
 * Cart API
 */
export const cartApi = {
    getCart: () =>
        apiClient.get<ApiResponse<Cart>>('/cart').then((r) => r.data),

    addItem: (data: AddToCartData) =>
        apiClient
            .post<ApiResponse<CartItem>>('/cart/items', data)
            .then((r) => r.data),

    updateItem: (itemId: number, data: UpdateCartItemData) =>
        apiClient
            .patch<ApiResponse<CartItem>>(`/cart/items/${itemId}`, data)
            .then((r) => r.data),

    removeItem: (itemId: number) =>
        apiClient
            .delete<ApiResponse<void>>(`/cart/items/${itemId}`)
            .then((r) => r.data),

    clearCart: () =>
        apiClient.delete<ApiResponse<void>>('/cart').then((r) => r.data),
};

/**
 * Order API
 */
export const orderApi = {
    getAll: () =>
        apiClient.get<ApiResponse<Order[]>>('/orders').then((r) => r.data),

    getById: (id: number) =>
        apiClient.get<ApiResponse<Order>>(`/orders/${id}`).then((r) => r.data),

    create: (data: CreateOrderData) =>
        apiClient.post<ApiResponse<Order>>('/orders', data).then((r) => r.data),

    cancel: (id: number) =>
        apiClient
            .post<ApiResponse<Order>>(`/orders/${id}/cancel`)
            .then((r) => r.data),
};

// Export the configured axios instance for custom requests
export default apiClient;
