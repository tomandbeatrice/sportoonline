import { mount } from '@vue/test-utils'
import SprintCockpit from '@/views/SprintCockpit.vue'
import { describe, it, expect, vi } from 'vitest'

// Mock fetch
global.fetch = vi.fn((url: string) => {
  if (url.includes('kategoriler')) {
    return Promise.resolve({
      json: () => Promise.resolve([{ id: 1, name: 'Futbol' }, { id: 2, name: 'Basketbol' }])
    })
  }
  if (url.includes('products')) {
    return Promise.resolve({
      json: () => Promise.resolve([
        { id: 1, name: 'Krampon', image: '/products/krampon.jpg' },
        { id: 2, name: 'Basketbol Topu', image: '/products/top.jpg' }
      ])
    })
  }
  return Promise.reject('Unknown URL')
}) as any

describe('SprintCockpit.vue', () => {
  it('renders banner, categories and products', async () => {
    const wrapper = mount(SprintCockpit)
    await new Promise(resolve => setTimeout(resolve, 100)) // wait for onMounted

    expect(wrapper.text()).toContain('🏁 Sprint Cockpit')
    expect(wrapper.text()).toContain('Futbol')
    expect(wrapper.text()).toContain('Basketbol')
    expect(wrapper.text()).toContain('Krampon')
    expect(wrapper.findAll('.product-card').length).toBe(2)
  })
})