/**
 * API Type Definitions
 * These interfaces match the API resources from the backend
 */

// Locale type
export type Locale = 'ru' | 'kz';

// Base interfaces
export interface City {
    id: number;
    name_ru: string;
    name_kz: string;
    name: string; // Computed accessor from backend
}

export interface Unit {
    id: number;
    name_ru: string;
    name_kz: string;
    abbreviation_ru: string;
    abbreviation_kz: string;
}

export interface UserType {
    id: number;
    name_ru: string;
    name_kz: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    phone?: string;
    type_id: number;
    type?: UserType;
    city_id: number;
    city?: City;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Company {
    id: number;
    name: string;
    bin: string;
    legal_address: string;
    city_id: number;
    city?: City;
    user_id: number;
    user?: User;
    created_at: string;
    updated_at: string;
}

export interface Shop {
    id: number;
    name: string;
    description?: string;
    logo?: string;
    company_id: number;
    company?: Company;
    city_id: number;
    city?: City;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

// Category interfaces
export interface Category {
    id: number;
    name_ru: string;
    name_kz: string;
    slug: string;
    description_ru?: string;
    description_kz?: string;
    parent_id: number | null;
    parent?: Category;
    children?: Category[];
    image?: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

// Nomenclature and Product Spec interfaces
export interface ProductSpec {
    id: number;
    nomenclature_id: number;
    key_ru: string;
    key_kz: string;
    value_ru: string;
    value_kz: string;
    created_at: string;
    updated_at: string;
}

export interface Nomenclature {
    id: number;
    name_ru: string;
    name_kz: string;
    description_ru?: string;
    description_kz?: string;
    unit_id: number;
    unit?: Unit;
    category_id: number;
    category?: Category;
    manufacturer?: string;
    model?: string;
    image?: string;
    specs?: ProductSpec[];
    created_at: string;
    updated_at: string;
}

// Product interfaces
export interface Product {
    id: number;
    name_ru: string;
    name_kz: string;
    nomenclature_id: number;
    nomenclature?: Nomenclature;
    shop_id: number;
    shop?: Shop;
    quantity: number;
    price: number;
    images?: string[];
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

// Cart interfaces
export interface CartItem {
    id: number;
    cart_id: number;
    product_id: number;
    product?: Product;
    quantity: number;
    price: number;
    created_at: string;
    updated_at: string;
}

export interface Cart {
    id: number;
    user_id: number;
    shop_id: number;
    shop?: Shop;
    items?: CartItem[];
    items_count: number;
    total: number;
    created_at: string;
    updated_at: string;
}

export interface UnifiedCart {
    carts: Cart[];
    total_items: number;
    total_amount: number;
}

// Order interfaces
export interface OrderItem {
    id: number;
    order_id: number;
    product_id: number;
    product?: Product;
    quantity: number;
    price: number;
    subtotal: number;
    created_at: string;
    updated_at: string;
}

export type OrderStatus =
    | 'pending'
    | 'confirmed'
    | 'processing'
    | 'shipped'
    | 'delivered'
    | 'cancelled'
    | 'refunded';

export interface Order {
    id: number;
    order_number: string;
    user_id: number;
    shop_id: number;
    shop?: Shop;
    status: OrderStatus;
    subtotal: number;
    total: number;
    delivery_address: string;
    delivery_entry?: string;
    delivery_floor?: string;
    delivery_apartment?: string;
    delivery_intercom?: string;
    delivery_city_id: number;
    delivery_city?: City;
    contact_phone: string;
    delivery_notes?: string;
    items?: OrderItem[];
    items_count: number;
    created_at: string;
    updated_at: string;
}

// API Response interfaces
export interface ApiResponse<T = unknown> {
    success: boolean;
    message?: string;
    data: T;
}

export interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

export interface ApiError {
    success: false;
    message: string;
    errors?: Record<string, string[]>;
}

// Form data interfaces
export interface AddToCartData {
    product_id: number;
    quantity: number;
}

export interface UpdateCartItemData {
    quantity: number;
}

export interface CreateOrderData {
    shop_id: number;
    delivery_address: string;
    delivery_entry?: string;
    delivery_floor?: string;
    delivery_apartment?: string;
    delivery_intercom?: string;
    delivery_city_id: number;
    contact_phone: string;
    delivery_notes?: string;
}

// Filter and search interfaces
export interface ProductFilters {
    city_id?: number;
    category_id?: number;
    shop_id?: number;
    min_price?: number;
    max_price?: number;
    search?: string;
    sort?: 'price_asc' | 'price_desc' | 'newest' | 'name';
    per_page?: number;
    page?: number;
}

export interface CategoryFilters {
    parent_id?: number | null;
    is_active?: boolean;
}

export interface CityData {
    selected: City | null;
    available: City[];
}
