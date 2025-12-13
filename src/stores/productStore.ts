import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useProductStore = defineStore('product', () => {
  const products = ref<any[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchProducts = async (params: any = {}) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.get('/api/products', { params })
      products.value = response.data.data || response.data
      return products.value
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Ürünler yüklenirken bir hata oluştu'
      console.error('Failed to fetch products:', err)
      return []
    } finally {
      loading.value = false
    }
  }

  const getProductById = async (id: number) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.get(`/api/products/${id}`)
      return response.data.data || response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Ürün yüklenirken bir hata oluştu'
      console.error('Failed to fetch product:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  function addProduct(product: any) {
    products.value.push({
      ...product,
      id: Date.now()
    })
  }

  function updateProduct(updatedProduct: any) {
    const index = products.value.findIndex(p => p.id === updatedProduct.id)
    if (index !== -1) {
      products.value[index] = updatedProduct
    }
  }

  function deleteProduct(id: number) {
    products.value = products.value.filter(p => p.id !== id)
  }

  const totalProducts = computed(() => products.value.length)
  const hasProducts = computed(() => products.value.length > 0)

  return {
    products,
    loading,
    error,
    totalProducts,
    hasProducts,
    fetchProducts,
    getProductById,
    addProduct,
    updateProduct,
    deleteProduct
  }
})
