import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import TransferRecommendation from '@/components/marketplace/TransferRecommendation.vue'

describe('TransferRecommendation', () => {
  it('renders the component with correct title', () => {
    const wrapper = mount(TransferRecommendation)
    expect(wrapper.text()).toContain('Otel rezervasyonunuz alındı. Transfer ister misiniz?')
  })

  it('renders both action buttons', () => {
    const wrapper = mount(TransferRecommendation)
    const buttons = wrapper.findAll('button')
    expect(buttons).toHaveLength(2)
    expect(buttons[0].text()).toContain('Evet, Transfer Ekle')
    expect(buttons[1].text()).toContain('Hayır, Teşekkürler')
  })

  it('emits accept event when accept button is clicked', async () => {
    const wrapper = mount(TransferRecommendation)
    const buttons = wrapper.findAll('button')
    await buttons[0].trigger('click')
    expect(wrapper.emitted('accept')).toBeTruthy()
    expect(wrapper.emitted('accept')).toHaveLength(1)
  })

  it('emits decline event when decline button is clicked', async () => {
    const wrapper = mount(TransferRecommendation)
    const buttons = wrapper.findAll('button')
    await buttons[1].trigger('click')
    expect(wrapper.emitted('decline')).toBeTruthy()
    expect(wrapper.emitted('decline')).toHaveLength(1)
  })

  it('displays discount badge with 15% off', () => {
    const wrapper = mount(TransferRecommendation)
    expect(wrapper.text()).toContain('%15')
    expect(wrapper.text()).toContain('Özel İndirim')
  })

  it('displays all feature items', () => {
    const wrapper = mount(TransferRecommendation)
    expect(wrapper.text()).toContain('7/24 Hizmet')
    expect(wrapper.text()).toContain('Güvenli Sürüş')
    expect(wrapper.text()).toContain('Uygun Fiyat')
    expect(wrapper.text()).toContain('Konforlu Araçlar')
  })
})
