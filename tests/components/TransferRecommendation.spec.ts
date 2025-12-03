import { describe, it, expect, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import TransferRecommendation from '@/components/TransferRecommendation.vue'

describe('TransferRecommendation', () => {
  beforeEach(() => {
    // Create a new pinia instance for each test
    setActivePinia(createPinia())
    // Clear localStorage
    localStorage.clear()
  })

  it('renders transfer recommendation with hotel booking data', async () => {
    const hotelBooking = {
      id: 'HTL123',
      hotelName: 'Test Hotel',
      destinationCoordinates: {
        lat: 41.0082,
        lng: 28.9784
      },
      destinationAddress: 'Test Address, Istanbul',
      checkInDate: '2025-12-10',
      numberOfGuests: 2
    }

    const wrapper = mount(TransferRecommendation, {
      props: {
        hotelBooking,
        autoShow: true
      },
      global: {
        stubs: {
          'router-link': true
        }
      }
    })

    // Wait for component to mount and show
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 600))

    // Check if recommendation is shown
    expect(wrapper.find('.transfer-recommendation-card').exists()).toBe(true)
    
    // Check if message is displayed
    expect(wrapper.text()).toContain('Otel rezervasyonunuz alındı')
    
    // Check if estimated distance is shown
    expect(wrapper.text()).toContain('35 km')
    
    // Check if starting price is shown
    expect(wrapper.text()).toContain('₺350.00')
  })

  it('calculates higher price for more guests', async () => {
    const hotelBooking = {
      id: 'HTL124',
      hotelName: 'Test Hotel',
      destinationAddress: 'Test Address',
      checkInDate: '2025-12-10',
      numberOfGuests: 6 // More than 4 guests
    }

    const wrapper = mount(TransferRecommendation, {
      props: {
        hotelBooking,
        autoShow: false
      }
    })

    // Manually show the component
    wrapper.vm.show()
    await wrapper.vm.$nextTick()

    // Should show higher price for van (6 guests > 4)
    expect(wrapper.text()).toContain('₺450.00')
  })

  it('hides recommendation when dismissed', async () => {
    const hotelBooking = {
      id: 'HTL125',
      hotelName: 'Test Hotel',
      destinationAddress: 'Test Address',
      checkInDate: '2025-12-10',
      numberOfGuests: 2
    }

    const wrapper = mount(TransferRecommendation, {
      props: {
        hotelBooking,
        autoShow: false
      }
    })

    // Manually show the component
    wrapper.vm.show()
    await wrapper.vm.$nextTick()

    expect(wrapper.find('.transfer-recommendation-card').exists()).toBe(true)

    // Find and click dismiss button
    const dismissButton = wrapper.find('button[aria-label="Kapat"]')
    await dismissButton.trigger('click')
    await wrapper.vm.$nextTick()

    // Recommendation should be hidden
    expect(wrapper.find('.transfer-recommendation-card').exists()).toBe(false)

    // Check that dismissal was stored in localStorage
    const dismissed = JSON.parse(localStorage.getItem('dismissedRecommendations') || '[]')
    expect(dismissed.length).toBe(1)
    expect(dismissed[0].type).toBe('hotel_transfer')
  })

  it('does not show if recently dismissed', async () => {
    // Set a recent dismissal in localStorage
    const recentDismissal = {
      type: 'hotel_transfer',
      timestamp: new Date().toISOString() // Just now
    }
    localStorage.setItem('dismissedRecommendations', JSON.stringify([recentDismissal]))

    const hotelBooking = {
      id: 'HTL126',
      hotelName: 'Test Hotel',
      destinationAddress: 'Test Address',
      checkInDate: '2025-12-10',
      numberOfGuests: 2
    }

    const wrapper = mount(TransferRecommendation, {
      props: {
        hotelBooking,
        autoShow: true
      }
    })

    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 600))

    // Should not show because it was recently dismissed
    expect(wrapper.find('.transfer-recommendation-card').exists()).toBe(false)
  })

  it('exposes show and hide methods', () => {
    const wrapper = mount(TransferRecommendation, {
      props: {
        hotelBooking: {
          id: 'HTL127',
          hotelName: 'Test Hotel',
          destinationAddress: 'Test Address',
          checkInDate: '2025-12-10',
          numberOfGuests: 2
        },
        autoShow: false
      }
    })

    // Check that show and hide methods are exposed
    expect(typeof wrapper.vm.show).toBe('function')
    expect(typeof wrapper.vm.hide).toBe('function')
  })
})
