import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000'

export interface BuyerStats {
  total_orders: number
  total_spent: number
  pending_orders: number
  delivered_orders: number
  total_favorites: number
  total_addresses: number
}

export interface BuyerOrder {
  id: number
  order_number: string
  total_amount: number
  status: string
  created_at: string
  items: any[]
}

export const buyerApi = {
  async getStats(): Promise<BuyerStats> {
    const response = await axios.get(`${API_URL}/api/buyer/stats`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    return response.data
  },

  async getOrders(): Promise<BuyerOrder[]> {
    const response = await axios.get(`${API_URL}/api/buyer/orders`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    return response.data
  },

  async getFavorites(): Promise<any[]> {
    const response = await axios.get(`${API_URL}/api/buyer/favorites`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    return response.data
  }
}
