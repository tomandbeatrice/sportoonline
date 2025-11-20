import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'

export const useCartStore = defineStore('cart', () => {
  const items = ref<any[]>([])
  const toast = useToast()

  const totalItems = computed(() => items.value.reduce((sum, item) => sum + item.quantity, 0))
  
  const subtotal = computed(() => {
    return items.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
  })

  function addToCart(product: any, quantity = 1) {
    const existingItem = items.value.find(item => item.id === product.id)
    if (existingItem) {
      existingItem.quantity += quantity
      toast.info('Sepetteki ürün adedi güncellendi')
    } else {
      items.value.push({
        ...product,
        quantity
      })
      toast.success('Ürün sepete eklendi')
    }
  }

  function removeFromCart(productId: number) {
    const index = items.value.findIndex(item => item.id === productId)
    if (index !== -1) {
      items.value.splice(index, 1)
      toast.info('Ürün sepetten çıkarıldı')
    }
  }

  function updateQuantity(productId: number, quantity: number) {
    const item = items.value.find(item => item.id === productId)
    if (item) {
      if (quantity > 0) {
        item.quantity = quantity
      } else {
        removeFromCart(productId)
      }
    }
  }

  function clearCart() {
    items.value = []
  }

  return {
    items,
    totalItems,
    subtotal,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart
  }
})
