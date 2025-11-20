import { ref } from 'vue'
import axios from 'axios'

export type PaymentMethod = 'stripe' | 'iyzico' | 'test'
export type PaymentStatus = 'pending' | 'paid' | 'failed'

export interface Payment {
  id: number
  user_id: number
  order_id: number
  method: PaymentMethod
  amount: number
  status: PaymentStatus
  paid_at: string | null
  created_at: string
}

export interface PaymentResponse {
  payment_id: number
  message?: string
}

export interface PaymentPayload {
  order_id: number
  method: PaymentMethod
  amount: number
  transaction_id?: string
}

export function usePayment() {
  const loading = ref(false)
  const error = ref<{ message?: string } | string | null>(null)
  const payments = ref<Payment[]>([])
  const response = ref<PaymentResponse | null>(null)

  // 💳 Ödeme oluştur
  const createPayment = async (payload: PaymentPayload) => {
    loading.value = true
    error.value = null
    try {
      const res = await axios.post<PaymentResponse>('/api/payments', payload)
      response.value = res.data
    } catch (err: any) {
      error.value = err.response?.data || err.message
    } finally {
      loading.value = false
    }
  }

  // 🔄 Durum güncelle
  const updateStatus = async (
    id: number,
    status: PaymentStatus,
    paid_at?: string
  ) => {
    loading.value = true
    error.value = null
    try {
      await axios.put(`/api/payments/${id}/status`, { status, paid_at })
    } catch (err: any) {
      error.value = err.response?.data || err.message
    } finally {
      loading.value = false
    }
  }

  // 📜 Ödeme geçmişi
  const fetchHistory = async () => {
    loading.value = true
    error.value = null
    try {
      const res = await axios.get<Payment[]>('/api/payments/history')
      payments.value = res.data
    } catch (err: any) {
      error.value = err.response?.data || err.message
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    payments,
    response,
    createPayment,
    updateStatus,
    fetchHistory
  }
}