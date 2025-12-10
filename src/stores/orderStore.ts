import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import { useToast } from 'vue-toastification'
import { marketplaceService } from '@/services/marketplaceService'
import axios from 'axios'
import type { Order } from '@/types/marketplace'
import { useAuthStore } from './auth'

export const useOrderStore = defineStore('order', () => {
  const orders = ref<any[]>([])
  
  const activeOrders = ref<Order[]>([])
  const loadingActiveOrders = ref(false)
  
  // Load favorites from localStorage for guests
  const storedFavorites = localStorage.getItem('favorites')
  const favorites = ref<any[]>(storedFavorites ? JSON.parse(storedFavorites) : [])
  
  const toast = useToast()

  // Persist favorites to localStorage
  watch(favorites, (newVal) => {
    localStorage.setItem('favorites', JSON.stringify(newVal))
  }, { deep: true })

  async function fetchOrders() {
    try {
      const response = await axios.get('/api/orders')
      orders.value = response.data
    } catch (error) {
      console.error('Error fetching orders:', error)
    }
  }

  async function fetchActiveOrders() {
    loadingActiveOrders.value = true
    try {
      const response = await marketplaceService.getActiveOrders()
      activeOrders.value = response.data
    } catch (error) {
      console.error('Error fetching active orders:', error)
    } finally {
      loadingActiveOrders.value = false
    }
  }

  async function createOrder(items: any[], total: number, shippingDetails: any) {
    try {
      // Prepare order data for backend
      const orderData = {
        address: `${shippingDetails.address}, ${shippingDetails.city} ${shippingDetails.zipCode}`,
        phone: shippingDetails.phone,
        notes: shippingDetails.notes || '',
        payment_method: 'stripe', // or from shippingDetails
        shipping_method: shippingDetails.shippingMethod || 'standard',
      }

      const response = await axios.post('/api/checkout', orderData)
      
      if (response.data.success) {
        const newOrder = response.data.order
        orders.value.unshift(newOrder)
        toast.success('Sipariş başarıyla oluşturuldu!')
        return newOrder
      } else {
        throw new Error(response.data.error || 'Sipariş oluşturulamadı')
      }
    } catch (error: any) {
      console.error('Order creation failed:', error)
      
      // Fallback: create local order for offline/demo mode
      const newOrder = {
        id: Math.floor(Math.random() * 10000) + 1000,
        created_at: new Date().toISOString(),
        status: 'processing',
        total: total,
        items: items.map(item => ({
          id: Math.random(),
          quantity: item.quantity,
          price: item.price,
          details: {
            ...item,
            image: item.image || item.image_url
          },
          product: {
            id: item.id,
            name: item.name,
            image_url: item.image || item.image_url
          }
        })),
        shippingDetails
      }
      
      orders.value.unshift(newOrder)
      toast.warning('Sipariş yerel olarak kaydedildi (demo modu)')
      return newOrder
    }
  }

  async function fetchFavorites() {
    const authStore = useAuthStore()
    if (!authStore.isAuthenticated) return

    try {
      const response = await axios.get('/api/favorites')
      favorites.value = response.data.data || response.data || []
    } catch (error) {
      console.error('Error fetching favorites:', error)
    }
  }

  async function toggleFavorite(product: any) {
    const authStore = useAuthStore()
    const index = favorites.value.findIndex(p => p.id === product.id)
    
    if (index !== -1) {
      // Remove from favorites
      favorites.value.splice(index, 1)
      toast.info('Favorilerden kaldırıldı')
      
      // Sync with backend if authenticated
      if (authStore.isAuthenticated) {
        try {
          await axios.delete(`/api/favorites/${product.id}`)
        } catch (error) {
          console.error('Error removing favorite:', error)
        }
      }
    } else {
      // Add to favorites
      favorites.value.push(product)
      toast.success('Favorilere eklendi')
      
      // Sync with backend if authenticated
      if (authStore.isAuthenticated) {
        try {
          await axios.post('/api/favorites', { product_id: product.id })
        } catch (error) {
          console.error('Error adding favorite:', error)
        }
      }
    }
  }

  function isFavorite(productId: number) {
    return favorites.value.some(p => p.id === productId)
  }

  return {
    orders,
    favorites,
    createOrder,
    toggleFavorite,
    isFavorite,
    fetchFavorites,
    activeOrders,
    loadingActiveOrders,
    fetchActiveOrders
  }
})
