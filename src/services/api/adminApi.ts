import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000'

export interface AdminStats {
  total_users: number
  total_sellers: number
  total_buyers: number
  total_products: number
  total_orders: number
  total_revenue: number
  pending_orders: number
  shipped_orders: number
  delivered_orders: number
  last_24h_orders: number
  last_24h_revenue: number
  active_campaigns: number
  conversion_rate: number
  avg_order_value: number
}

export interface SellerSales {
  seller_id: number
  seller_name: string
  total_orders: number
  total_revenue: number
  total_payout: number
  commission: number
}

export interface RecentTransaction {
  id: number
  order_id: number
  buyer_name: string
  seller_name: string
  product_name: string
  amount: number
  status: string
  created_at: string
}

export interface FinancialReport {
  summary: {
    total_orders: number
    total_revenue: number
    total_platform_fees: number
    total_seller_payouts: number
  }
  sales_by_seller: SellerSales[]
  recent_transactions: RecentTransaction[]
}

export const adminApi = {
  async getStats(): Promise<AdminStats> {
    const response = await axios.get(`${API_URL}/api/admin/stats`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    return response.data
  },

  async getFinancialReport(): Promise<FinancialReport> {
    const response = await axios.get(`${API_URL}/api/admin/financial-report`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    return response.data
  },

  async getRealtimeMetrics(): Promise<any> {
    const response = await axios.get(`${API_URL}/api/admin/realtime-metrics`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    return response.data
  }
}
