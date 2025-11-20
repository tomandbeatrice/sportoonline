import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000'

export interface SellerStats {
  total_products: number
  total_orders: number
  total_revenue: number
  pending_orders: number
  shipped_orders: number
  completed_orders: number
  avg_rating: number
  total_commission: number
  seller_payout: number
}

export interface SellerProduct {
  id: number
  name: string
  price: number
  stock: number
  sold_count: number
  revenue: number
  image_url?: string
}

export const sellerApi = {
  async getStats(): Promise<SellerStats> {
    const response = await axios.get(`${API_URL}/api/seller/stats`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    return response.data
  },

  async getProducts(): Promise<SellerProduct[]> {
    const response = await axios.get(`${API_URL}/api/seller/products`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    return response.data
  },

  async getOrders(): Promise<any[]> {
    const response = await axios.get(`${API_URL}/api/seller/orders`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    return response.data
  }
}
