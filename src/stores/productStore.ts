import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useProductStore = defineStore('product', () => {
  const products = ref<any[]>([
    { 
      id: 1, 
      name: 'Nike Air Zoom', 
      price: 3299.90, 
      stock: 15, 
      image_url: 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/ca79d356-4213-44a7-be92-ae850d668242/air-zoom-pegasus-39-road-running-shoes-d4dvtm.png',
      description: 'Yüksek performanslı koşu ayakkabısı.',
      category: { id: 1, name: 'Ayakkabı' }
    },
    { 
      id: 2, 
      name: 'Adidas T-Shirt', 
      price: 899.50, 
      stock: 42, 
      image_url: 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c35214f6104c4a288bfed097586310d2_9366/Own_the_Run_Tee_Blue_H58593_21_model.jpg',
      description: 'Nefes alabilen kumaş teknolojisi.',
      category: { id: 2, name: 'Giyim' }
    }
  ])

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
    addProduct,
    updateProduct,
    deleteProduct
  }
})
