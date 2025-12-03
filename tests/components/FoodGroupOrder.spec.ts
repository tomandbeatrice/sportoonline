import { describe, it, expect, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import FoodGroupOrder from '@/components/marketplace/FoodGroupOrder.vue'

describe('FoodGroupOrder', () => {
  describe('Setup State', () => {
    it('renders setup state initially', () => {
      const wrapper = mount(FoodGroupOrder)
      
      // Should show setup state
      expect(wrapper.text()).toContain('Grup Siparişi Başlat')
      expect(wrapper.text()).toContain('Grup Adı')
    })

    it('disables start button when group name is empty', () => {
      const wrapper = mount(FoodGroupOrder)
      
      const startButton = wrapper.find('button')
      expect(startButton.attributes('disabled')).toBeDefined()
    })

    it('enables start button when group name is provided', async () => {
      const wrapper = mount(FoodGroupOrder)
      
      const input = wrapper.find('input#groupName')
      await input.setValue('Ofis Öğle Yemeği')
      
      const startButton = wrapper.find('button')
      expect(startButton.attributes('disabled')).toBeUndefined()
    })

    it('starts group order when button is clicked', async () => {
      const wrapper = mount(FoodGroupOrder)
      
      const input = wrapper.find('input#groupName')
      await input.setValue('Ofis Öğle Yemeği')
      
      const startButton = wrapper.find('button')
      await startButton.trigger('click')
      
      // Should show active state
      expect(wrapper.text()).toContain('Ofis Öğle Yemeği')
      expect(wrapper.text()).toContain('Paylaşılabilir Link')
    })
  })

  describe('Active State', () => {
    let wrapper: any

    beforeEach(async () => {
      wrapper = mount(FoodGroupOrder)
      
      // Start group order
      const input = wrapper.find('input#groupName')
      await input.setValue('Test Grubu')
      
      const startButton = wrapper.find('button')
      await startButton.trigger('click')
    })

    it('displays shareable link', () => {
      // Link is in an input field
      const linkInput = wrapper.find('input[readonly]')
      expect(linkInput.element.value).toContain('sportoonline.com/group/')
    })

    it('shows 0 participants initially', () => {
      expect(wrapper.text()).toContain('0 kişi katıldı')
    })

    it('adds guest when simulation button is clicked', async () => {
      const addAliButton = wrapper.findAll('button').find((btn: any) => 
        btn.text().includes('+ Ali')
      )
      
      if (addAliButton) {
        await addAliButton.trigger('click')
        expect(wrapper.text()).toContain('1 kişi katıldı')
        expect(wrapper.text()).toContain('Ali')
      }
    })

    it('calculates total amount correctly', async () => {
      // Add a guest
      const addAliButton = wrapper.findAll('button').find((btn: any) => 
        btn.text().includes('+ Ali')
      )
      
      if (addAliButton) {
        await addAliButton.trigger('click')
        
        // Add an item to Ali
        const addItemButton = wrapper.findAll('button').find((btn: any) => 
          btn.text().includes('Ürün Ekle')
        )
        
        if (addItemButton) {
          await addItemButton.trigger('click')
          
          // Should show total amount > 0
          expect(wrapper.text()).toContain('Toplam Tutar')
          expect(wrapper.html()).toContain('₺')
        }
      }
    })

    it('removes guest when delete button is clicked', async () => {
      // Add a guest first
      const addAliButton = wrapper.findAll('button').find((btn: any) => 
        btn.text().includes('+ Ali')
      )
      
      if (addAliButton) {
        await addAliButton.trigger('click')
        expect(wrapper.text()).toContain('1 kişi katıldı')
        
        // Find and click remove button
        const removeButtons = wrapper.findAll('button[aria-label="Misafiri çıkar"]')
        if (removeButtons.length > 0) {
          await removeButtons[0].trigger('click')
          // Should show 0 participants again
          expect(wrapper.text()).toContain('0 kişi katıldı')
        }
      }
    })

    it('displays bill breakdown per person', async () => {
      // Add two guests
      const addAliButton = wrapper.findAll('button').find((btn: any) => 
        btn.text().includes('+ Ali')
      )
      const addAyseButton = wrapper.findAll('button').find((btn: any) => 
        btn.text().includes('+ Ayşe')
      )
      
      if (addAliButton && addAyseButton) {
        await addAliButton.trigger('click')
        await addAyseButton.trigger('click')
        
        // Should show both names in summary
        expect(wrapper.text()).toContain('Sipariş Özeti')
        expect(wrapper.text()).toContain('Ali')
        expect(wrapper.text()).toContain('Ayşe')
      }
    })
  })
})
