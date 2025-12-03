import { mount, flushPromises } from '@vue/test-utils'
import SprintCockpit from '@/views/SprintCockpit.vue'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import axios from 'axios'

// Mock axios
vi.mock('axios')
const mockedAxios = axios as any

describe('SprintCockpit.vue', () => {
  beforeEach(() => {
    mockedAxios.get = vi.fn().mockResolvedValue({
      data: [
        { id: 1, name: 'Krampon', category: 'Futbol', image: '/products/krampon.jpg' },
        { id: 2, name: 'Basketbol Topu', category: 'Basketbol', image: '/products/top.jpg' }
      ]
    })
  })

  it('renders banner, categories and products', async () => {
    const wrapper = mount(SprintCockpit)
    await flushPromises()

    expect(wrapper.text()).toContain('Sprint Cockpit')
  })
})