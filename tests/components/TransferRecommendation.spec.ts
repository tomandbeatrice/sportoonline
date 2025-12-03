import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import TransferRecommendation from '@/components/marketplace/TransferRecommendation.vue'

describe('TransferRecommendation', () => {
  it('renders with default props', () => {
    const wrapper = mount(TransferRecommendation)
    expect(wrapper.find('h3').text()).toContain('Need a ride')
  })

  it('displays destination when provided', () => {
    const wrapper = mount(TransferRecommendation, {
      props: {
        destination: 'Grand Plaza Hotel'
      }
    })
    expect(wrapper.find('h3').text()).toContain('Grand Plaza Hotel')
  })

  it('shows discount badge when showDiscount is true', () => {
    const wrapper = mount(TransferRecommendation, {
      props: {
        showDiscount: true
      }
    })
    expect(wrapper.text()).toContain('15% OFF')
  })

  it('emits add-transfer event when Add Transfer button is clicked', async () => {
    const wrapper = mount(TransferRecommendation)
    const addButton = wrapper.find('button:first-of-type')
    await addButton.trigger('click')
    expect(wrapper.emitted('add-transfer')).toBeTruthy()
  })

  it('emits dismiss event when No thanks button is clicked', async () => {
    const wrapper = mount(TransferRecommendation)
    const buttons = wrapper.findAll('button')
    const noThanksButton = buttons[1]
    await noThanksButton.trigger('click')
    expect(wrapper.emitted('dismiss')).toBeTruthy()
  })

  it('displays all benefit items', () => {
    const wrapper = mount(TransferRecommendation)
    const benefits = wrapper.findAll('li')
    expect(benefits.length).toBe(3)
    expect(wrapper.text()).toContain('Professional drivers')
    expect(wrapper.text()).toContain('24/7 customer support')
    expect(wrapper.text()).toContain('Competitive pricing')
  })
})
