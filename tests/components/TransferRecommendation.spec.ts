import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'
import { mount } from '@vue/test-utils'
import TransferRecommendation from '@/components/marketplace/TransferRecommendation.vue'

describe('TransferRecommendation', () => {
  let targetElement: HTMLDivElement

  beforeEach(() => {
    // Create a target element for Teleport
    targetElement = document.createElement('div')
    targetElement.id = 'teleport-target'
    document.body.appendChild(targetElement)
    // Clear body innerHTML for clean tests
    const existingDialogs = document.body.querySelectorAll('[role="dialog"]')
    existingDialogs.forEach(dialog => dialog.remove())
  })

  afterEach(() => {
    // Clean up
    if (document.body.contains(targetElement)) {
      document.body.removeChild(targetElement)
    }
    // Clean up any remaining modals
    const existingDialogs = document.body.querySelectorAll('[role="dialog"]')
    existingDialogs.forEach(dialog => dialog.remove())
  })

  it('renders when modelValue is true', async () => {
    const wrapper = mount(TransferRecommendation, {
      props: {
        modelValue: true
      },
      global: {
        stubs: {
          Teleport: false
        }
      }
    })
    
    // Wait for teleport to render
    await wrapper.vm.$nextTick()
    
    // Check in document.body since it's teleported
    expect(document.body.innerHTML).toContain('Seyahatinizi Tamamlayın')
    expect(document.body.innerHTML).toContain('Otel Rezervasyonunuz Alındı')
    expect(document.body.innerHTML).toContain('transfer (Yolculuk)')
    
    wrapper.unmount()
  })

  it('emits events correctly', async () => {
    const wrapper = mount(TransferRecommendation, {
      props: {
        modelValue: true
      },
      global: {
        stubs: {
          Teleport: false
        }
      }
    })
    
    await wrapper.vm.$nextTick()
    
    // Call the methods directly instead of clicking
    wrapper.vm.accept()
    await wrapper.vm.$nextTick()
    
    expect(wrapper.emitted('accept')).toBeTruthy()
    expect(wrapper.emitted('update:modelValue')).toBeTruthy()
    
    wrapper.unmount()
  })

  it('displays benefits list', async () => {
    const wrapper = mount(TransferRecommendation, {
      props: {
        modelValue: true
      },
      global: {
        stubs: {
          Teleport: false
        }
      }
    })
    
    await wrapper.vm.$nextTick()
    
    expect(document.body.innerHTML).toContain('Konforlu ve güvenli ulaşım')
    expect(document.body.innerHTML).toContain('Havalimanı ve şehir içi transferler')
    expect(document.body.innerHTML).toContain('Özel indirimli fiyatlar')
    
    wrapper.unmount()
  })

  it('displays discount badge', async () => {
    const wrapper = mount(TransferRecommendation, {
      props: {
        modelValue: true
      },
      global: {
        stubs: {
          Teleport: false
        }
      }
    })
    
    await wrapper.vm.$nextTick()
    
    expect(document.body.innerHTML).toContain('%15 indirim')
    
    wrapper.unmount()
  })
})
