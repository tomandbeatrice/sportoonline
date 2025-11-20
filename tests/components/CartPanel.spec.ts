import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import CartPanel from '@/components/CartPanel.vue'

describe('CartPanel', () => {
  it('ürün ekler ve toplamı hesaplar', async () => {
    const wrapper = mount(CartPanel)
    await wrapper.vm.addToCart({ name: 'Ürün A', price: 100 })
    expect(wrapper.vm.cart.length).toBe(1)
    expect(wrapper.vm.total).toBe(100)
  })
})