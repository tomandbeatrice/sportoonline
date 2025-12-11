import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useProductStore = defineStore('product', () => {
  const products = ref<any[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchProducts(params?: {
    category_id?: number | string
    page?: number
    per_page?: number
    sort_by?: string
  }) {
    loading.value = true
    error.value = null

    try {
      const response = await axios.get('/api/v1/products', { params })
      products.value = response.data.data || response.data.products || []
      return products.value
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Ürünler yüklenirken bir hata oluştu.'
      console.error('Error fetching products:', err)
      return []
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

  return {
    products,
    loading,
    error,
    fetchProducts,
    addProduct,
    updateProduct,
    deleteProduct
  }
})
