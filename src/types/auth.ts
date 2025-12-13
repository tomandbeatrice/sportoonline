/**
 * Authentication Type Definitions
 * Comprehensive types for user authentication and authorization
 */

export type UserRole = 'admin' | 'seller' | 'buyer'

export interface User {
  id: number
  name: string
  email: string
  role: UserRole
  avatar?: string
  created_at?: string
  updated_at?: string
  email_verified_at?: string | null
  phone?: string | null
  vendor_id?: number | null
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterData {
  name: string
  email: string
  password: string
  password_confirmation: string
  accept_terms: boolean
  role?: UserRole
}

export interface AuthResponse {
  token: string
  user: User
}

export interface ForgotPasswordRequest {
  email: string
}

export interface ResetPasswordRequest {
  token: string
  password: string
  password_confirmation: string
}

export interface ChangePasswordRequest {
  current_password: string
  password: string
  password_confirmation: string
}

export interface AuthError {
  message: string
  errors?: Record<string, string[]>
  remaining_attempts?: number
}

export interface LogoutResponse {
  message: string
}

export interface MeResponse {
  user: User
}
