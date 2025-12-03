import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import TransferRecommendation from '@/components/marketplace/TransferRecommendation.vue'

describe('TransferRecommendation', () => {
  it('renders when show prop is true', () => {
    const wrapper = mount(TransferRecommendation, {
      props: {
        show: true
      }
    })
    expect(wrapper.find('.bg-white.rounded-2xl').exists()).toBe(true)
    expect(wrapper.text()).toContain('Otel rezervasyonunuz alındı')
  })

  it('does not render when show prop is false', () => {
    const wrapper = mount(TransferRecommendation, {
      props: {
        show: false
      }
    })
    expect(wrapper.find('.bg-white.rounded-2xl').exists()).toBe(false)
  })

  it('displays correct Turkish text', () => {
    const wrapper = mount(TransferRecommendation, {
      props: {
        show: true
      }
    })
    expect(wrapper.text()).toContain('Otel rezervasyonunuz alındı. Havalimanından otele transfer ister misiniz?')
    expect(wrapper.text()).toContain('Evet, Transfer Ekle')
    expect(wrapper.text()).toContain('Hayır, Teşekkürler')
  })

  it('emits close event when "Hayır" button is clicked', async () => {
    const wrapper = mount(TransferRecommendation, {
      props: {
        show: true
      }
    })
    
    const noButton = wrapper.findAll('button').find(btn => btn.text().includes('Hayır'))
    await noButton?.trigger('click')
    
    expect(wrapper.emitted('close')).toBeTruthy()
    expect(wrapper.emitted('close')).toHaveLength(1)
  })

  it('emits addTransfer and close events when "Evet" button is clicked', async () => {
    const consoleSpy = vi.spyOn(console, 'log')
    
    const wrapper = mount(TransferRecommendation, {
      props: {
        show: true
      }
    })
    
    const yesButton = wrapper.findAll('button').find(btn => btn.text().includes('Evet'))
    await yesButton?.trigger('click')
    
    expect(wrapper.emitted('addTransfer')).toBeTruthy()
    expect(wrapper.emitted('addTransfer')).toHaveLength(1)
    expect(wrapper.emitted('close')).toBeTruthy()
    expect(wrapper.emitted('close')).toHaveLength(1)
    expect(consoleSpy).toHaveBeenCalledWith('Transfer added to Global Cart')
    
    consoleSpy.mockRestore()
  })

  it('emits close event when clicking backdrop', async () => {
    const wrapper = mount(TransferRecommendation, {
      props: {
        show: true
      }
    })
    
    const backdrop = wrapper.find('.fixed.inset-0')
    await backdrop.trigger('click')
    
    expect(wrapper.emitted('close')).toBeTruthy()
  })

  it('does not emit close when clicking inside modal', async () => {
    const wrapper = mount(TransferRecommendation, {
      props: {
        show: true
      }
    })
    
    const modal = wrapper.find('.bg-white.rounded-2xl')
    await modal.trigger('click')
    
    // Should not emit close since we're clicking inside the modal
    // Only the backdrop click should close
    expect(wrapper.emitted('close')).toBeFalsy()
  })
})
