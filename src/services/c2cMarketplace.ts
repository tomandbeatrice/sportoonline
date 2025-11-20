import axios from 'axios'

/**
 * C2C Marketplace Service
 * Handles all C2C marketplace dashboard API calls
 */

const API_BASE = '/api/c2c'

export interface DashboardData {
  role: 'seller' | 'buyer' | 'admin'
  stats: any
  [key: string]: any
}

export interface WorkflowRequest {
  workflow_id: string
  params?: Record<string, any>
}

export interface QuickActionRequest {
  action_id: string
  params?: Record<string, any>
}

/**
 * Get dashboard data based on current user role
 */
export async function getDashboardData(): Promise<DashboardData> {
  try {
    const response = await axios.get(`${API_BASE}/dashboard`)
    return response.data
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error)
    throw error
  }
}

/**
 * Execute a workflow
 */
export async function executeWorkflow(workflow: WorkflowRequest): Promise<any> {
  try {
    const response = await axios.post(`${API_BASE}/workflow`, workflow)
    return response.data
  } catch (error) {
    console.error('Failed to execute workflow:', error)
    throw error
  }
}

/**
 * Execute a quick action
 */
export async function executeQuickAction(action: QuickActionRequest): Promise<any> {
  try {
    const response = await axios.post(`${API_BASE}/quick-action`, action)
    return response.data
  } catch (error) {
    console.error('Failed to execute quick action:', error)
    throw error
  }
}

/**
 * Seller-specific: Get product performance
 */
export async function getSellerProductPerformance(): Promise<any> {
  try {
    const response = await axios.get('/api/seller/products/performance')
    return response.data
  } catch (error) {
    console.error('Failed to fetch product performance:', error)
    throw error
  }
}

/**
 * Seller-specific: Get pending orders
 */
export async function getSellerPendingOrders(): Promise<any> {
  try {
    const response = await axios.get('/api/seller/orders?status=pending')
    return response.data
  } catch (error) {
    console.error('Failed to fetch pending orders:', error)
    throw error
  }
}

/**
 * Buyer-specific: Get active orders
 */
export async function getBuyerOrders(): Promise<any> {
  try {
    const response = await axios.get('/api/orders')
    return response.data
  } catch (error) {
    console.error('Failed to fetch buyer orders:', error)
    throw error
  }
}

/**
 * Buyer-specific: Get recommendations
 */
export async function getBuyerRecommendations(): Promise<any> {
  try {
    const response = await axios.get('/api/recommendations')
    return response.data
  } catch (error) {
    console.error('Failed to fetch recommendations:', error)
    throw error
  }
}

/**
 * Admin-specific: Get pending approvals
 */
export async function getAdminPendingApprovals(): Promise<any> {
  try {
    const response = await axios.get('/api/admin/approvals/pending')
    return response.data
  } catch (error) {
    console.error('Failed to fetch pending approvals:', error)
    throw error
  }
}

/**
 * Admin-specific: Get active disputes
 */
export async function getAdminDisputes(): Promise<any> {
  try {
    const response = await axios.get('/api/admin/disputes?status=active')
    return response.data
  } catch (error) {
    console.error('Failed to fetch disputes:', error)
    throw error
  }
}

/**
 * Admin-specific: Approve seller application
 */
export async function approveSellerApplication(applicationId: number): Promise<any> {
  try {
    const response = await axios.post(`/api/admin/sellers/approve/${applicationId}`)
    return response.data
  } catch (error) {
    console.error('Failed to approve seller:', error)
    throw error
  }
}

/**
 * Admin-specific: Resolve dispute
 */
export async function resolveDispute(disputeId: number, resolution: any): Promise<any> {
  try {
    const response = await axios.post(`/api/admin/disputes/${disputeId}/resolve`, resolution)
    return response.data
  } catch (error) {
    console.error('Failed to resolve dispute:', error)
    throw error
  }
}

/**
 * Universal: Get recent activities
 */
export async function getRecentActivities(limit: number = 10): Promise<any> {
  try {
    const response = await axios.get(`${API_BASE}/activities?limit=${limit}`)
    return response.data
  } catch (error) {
    console.error('Failed to fetch activities:', error)
    throw error
  }
}

/**
 * Universal: Get notifications
 */
export async function getNotifications(unreadOnly: boolean = false): Promise<any> {
  try {
    const response = await axios.get(`/api/notifications?unread=${unreadOnly}`)
    return response.data
  } catch (error) {
    console.error('Failed to fetch notifications:', error)
    throw error
  }
}

/**
 * Universal: Mark notification as read
 */
export async function markNotificationRead(notificationId: number): Promise<any> {
  try {
    const response = await axios.put(`/api/notifications/${notificationId}/read`)
    return response.data
  } catch (error) {
    console.error('Failed to mark notification as read:', error)
    throw error
  }
}

export default {
  getDashboardData,
  executeWorkflow,
  executeQuickAction,
  getSellerProductPerformance,
  getSellerPendingOrders,
  getBuyerOrders,
  getBuyerRecommendations,
  getAdminPendingApprovals,
  getAdminDisputes,
  approveSellerApplication,
  resolveDispute,
  getRecentActivities,
  getNotifications,
  markNotificationRead
}
