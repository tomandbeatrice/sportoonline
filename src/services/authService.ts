/**
 * Authentication Service
 * Handles all authentication-related API calls using Laravel Sanctum
 */

import apiClient from './apiClient'
import type {
  LoginCredentials,
  RegisterData,
  AuthResponse,
  User,
  ForgotPasswordRequest,
  ResetPasswordRequest,
  ChangePasswordRequest,
  LogoutResponse,
  MeResponse
} from '@/types/auth'

/**
 * Login user and get authentication token
 */
export async function login(credentials: LoginCredentials): Promise<AuthResponse> {
  const { data } = await apiClient.post<AuthResponse>('/login', credentials)
  return data
}

/**
 * Register new user
 */
export async function register(userData: RegisterData): Promise<AuthResponse> {
  const { data } = await apiClient.post<AuthResponse>('/register', userData)
  return data
}

/**
 * Logout current user and invalidate token
 */
export async function logout(): Promise<LogoutResponse> {
  const { data } = await apiClient.post<LogoutResponse>('/logout')
  return data
}

/**
 * Get current authenticated user
 */
export async function getCurrentUser(): Promise<User> {
  const { data } = await apiClient.get<MeResponse>('/me')
  return data.user
}

/**
 * Send password reset email
 */
export async function forgotPassword(request: ForgotPasswordRequest): Promise<{ success: boolean; message: string }> {
  const { data } = await apiClient.post('/forgot-password', request)
  return data
}

/**
 * Reset password with token
 */
export async function resetPassword(request: ResetPasswordRequest): Promise<{ success: boolean; message: string }> {
  const { data } = await apiClient.post('/reset-password', request)
  return data
}

/**
 * Change current user password
 */
export async function changePassword(request: ChangePasswordRequest): Promise<{ success: boolean; message: string }> {
  const { data } = await apiClient.post('/change-password', request)
  return data
}

/**
 * Check if user is authenticated by verifying token
 */
export function hasValidToken(): boolean {
  const token = localStorage.getItem('token')
  return !!token
}

/**
 * Store authentication token
 */
export function setAuthToken(token: string): void {
  localStorage.setItem('token', token)
}

/**
 * Remove authentication token
 */
export function removeAuthToken(): void {
  localStorage.removeItem('token')
}

/**
 * Get stored authentication token
 */
export function getAuthToken(): string | null {
  return localStorage.getItem('token')
}

// Default export for convenience
export default {
  login,
  register,
  logout,
  getCurrentUser,
  forgotPassword,
  resetPassword,
  changePassword,
  hasValidToken,
  setAuthToken,
  removeAuthToken,
  getAuthToken
}
