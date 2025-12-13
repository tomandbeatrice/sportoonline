/**
 * Central Type Definitions Export
 * Main entry point for all TypeScript type definitions
 */

// Auth types
export type {
  User,
  UserRole,
  LoginCredentials,
  RegisterData,
  AuthResponse,
  ForgotPasswordRequest,
  ResetPasswordRequest,
  ChangePasswordRequest,
  AuthError,
  LogoutResponse,
  MeResponse
} from './auth'

// Re-export existing types
export type * from './booking'
export type * from './location'
export type * from './marketplace'

/**
 * Common API Response Types
 */
export interface ApiResponse<T = any> {
  success?: boolean
  message?: string
  data?: T
  errors?: Record<string, string[]>
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number | null
  to: number | null
}

/**
 * Product Types
 */
export interface Product {
  id: number
  name: string
  description: string
  price: number
  stock: number
  category_id: number
  vendor_id: number
  images?: string[]
  created_at: string
  updated_at: string
}

/**
 * Order Types
 */
export interface Order {
  id: number
  user_id: number
  total: number
  status: string
  created_at: string
  updated_at: string
  items?: OrderItem[]
}

export interface OrderItem {
  id: number
  order_id: number
  product_id: number
  quantity: number
  price: number
  product?: Product
}

/**
 * Cart Types
 */
export interface CartItem {
  id: number
  product_id: number
  quantity: number
  product?: Product
}

/**
 * Address Types
 */
export interface Address {
  id?: number
  user_id?: number
  title: string
  first_name: string
  last_name: string
  phone: string
  address: string
  city: string
  district: string
  postal_code: string
  is_default?: boolean
  created_at?: string
  updated_at?: string
}

/**
 * Notification Types
 */
export interface Notification {
  id: string
  type: string
  data: Record<string, any>
  read_at: string | null
  created_at: string
}

/**
 * Error Handling Types
 */
export interface ValidationError {
  message: string
  errors: Record<string, string[]>
}

export interface ApiError {
  message: string
  status?: number
  errors?: Record<string, string[]>
}
