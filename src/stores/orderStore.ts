import { defineStore } from 'pinia'
import { ref } from 'vue'
import { useToast } from 'vue-toastification'

export const useOrderStore = defineStore('order', () => {
  const orders = ref<any[]>([
    {
      id: 1001,
      created_at: '2025-11-15T10:30:00',
      status: 'delivered',
      total: 4199.40,
      items: [
        {
          id: 1,
          quantity: 1,
          price: 3299.90,
          product: {
            id: 1,
            name: 'Nike Air Zoom Pegasus',
            image_url: 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/ca79d356-4213-44a7-be92-ae850d668242/air-zoom-pegasus-39-road-running-shoes-d4dvtm.png'
          }
        },
        {
          id: 2,
          quantity: 1,
          price: 899.50,
          product: {
            id: 2,
            name: 'Adidas Dri-Fit T-Shirt',
            image_url: 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c35214f6104c4a288bfed097586310d2_9366/Own_the_Run_Tee_Blue_H58593_21_model.jpg'
          }
        }
      ]
    }
  ])
  
  const favorites = ref<any[]>([])
  const toast = useToast()

  function createOrder(items: any[], total: number, shippingDetails: any) {
    const newOrder = {
      id: Math.floor(Math.random() * 10000) + 1000,
      created_at: new Date().toISOString(),
      status: 'processing',
      total: total,
      items: items.map(item => ({
        id: Math.random(),
        quantity: item.quantity,
        price: item.price,
        product: {
          id: item.id,
          name: item.name,
          image_url: item.image_url || item.image
        }
      })),
      shippingDetails
    }
    
    orders.value.unshift(newOrder)
    return newOrder
  }

  function toggleFavorite(product: any) {
    const index = favorites.value.findIndex(p => p.id === product.id)
    if (index !== -1) {
      favorites.value.splice(index, 1)
      toast.info('Favorilerden kaldırıldı')
    } else {
      favorites.value.push(product)
      toast.success('Favorilere eklendi')
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
    isFavorite
  }
})
