import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import { useToast } from 'vue-toastification'

// LocalStorage key for cart persistence
const CART_STORAGE_KEY = 'sportoonline_cart'

export type CartItemType = 'product' | 'service' | 'booking';

export interface BaseCartItem {
  id: number | string;
  name: string;
  price: number;
  quantity: number;
  type: CartItemType;
  image?: string;
  category?: { name: string };
}

export interface ProductCartItem extends BaseCartItem {
  type: 'product';
  variant?: string;
  shippingClass?: 'standard' | 'express' | 'instant';
}

export interface ServiceCartItem extends BaseCartItem {
  type: 'service';
  duration?: number;
  providerId?: number;
}

export interface BookingCartItem extends BaseCartItem {
  type: 'booking';
  startDate: string;
  endDate: string;
  guests: number;
}

export type CartItem = ProductCartItem | ServiceCartItem | BookingCartItem;

export const useCartStore = defineStore('cart', () => {
  // Load persisted cart from localStorage
  const loadPersistedCart = (): CartItem[] => {
    try {
      const saved = localStorage.getItem(CART_STORAGE_KEY)
      return saved ? JSON.parse(saved) : []
    } catch {
      return []
    }
  }

  const items = ref<CartItem[]>(loadPersistedCart())
  const toast = useToast()

  // Persist cart to localStorage on changes
  watch(items, (newItems) => {
    try {
      localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(newItems))
    } catch (e) {
      console.error('Failed to persist cart:', e)
    }
  }, { deep: true })

  const totalItems = computed(() => items.value.reduce((sum, item) => sum + item.quantity, 0))
  
  const subtotal = computed(() => {
    return items.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
  })

  const getItemGroup = (item: CartItem): 'instant' | 'cargo' | 'digital' => {
    if (item.type === 'booking' || item.type === 'service') return 'digital';
    if (item.type === 'product') {
      return item.shippingClass === 'instant' ? 'instant' : 'cargo';
    }
    return 'cargo';
  }

  const groupedItems = computed(() => {
    const groups = {
      instant: [] as CartItem[],
      cargo: [] as CartItem[],
      digital: [] as CartItem[]
    }

    items.value.forEach(item => {
      const group = getItemGroup(item)
      groups[group].push(item)
    })

    return groups
  })

  function addToCart(item: Partial<CartItem> & { id: number | string, name: string, price: number }, quantity = 1) {
    let itemType: CartItemType = item.type || 'product';
    
    if (!item.type && item.category?.name) {
       const cat = item.category.name.toLowerCase();
       if (cat.includes('otel') || cat.includes('hotel')) itemType = 'booking';
       else if (cat.includes('hizmet') || cat.includes('service')) itemType = 'service';
    }

    const itemToAdd = { ...item, type: itemType } as CartItem;

    const existingItem = items.value.find(i => i.id === itemToAdd.id)
    
    if (existingItem) {
      existingItem.quantity += quantity
      toast.info('Sepetteki ürün adedi güncellendi')
    } else {
      items.value.push({
        ...itemToAdd,
        quantity
      })
      toast.success('Ürün sepete eklendi')
    }
  }

  function removeFromCart(itemId: number | string) {
    const index = items.value.findIndex(item => item.id === itemId)
    if (index !== -1) {
      items.value.splice(index, 1)
      toast.info('Ürün sepetten çıkarıldı')
    }
  }

  function updateQuantity(itemId: number | string, quantity: number) {
    const item = items.value.find(item => item.id === itemId)
    if (item) {
      if (quantity > 0) {
        item.quantity = quantity
      } else {
        removeFromCart(itemId)
      }
    }
  }

  function clearCart() {
    items.value = []
    localStorage.removeItem(CART_STORAGE_KEY)
  }

  return {
    items,
    totalItems,
    subtotal,
    groupedItems,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart
  }
})
