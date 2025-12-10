import { defineStore } from 'pinia'
import axios from 'axios'
import type { LocationState, Business, GeoLocation } from '@/types/location'

export const useLocationStore = defineStore('location', {
  state: (): LocationState => ({
    userLocation: null,
    businesses: [],
    loading: false,
    error: null,
    permissionGranted: false
  }),

  actions: {
    async requestUserLocation() {
      this.loading = true
      this.error = null
      
      try {
        const position = await new Promise<GeolocationPosition>((resolve, reject) => {
          if (!navigator.geolocation) {
            reject(new Error('Tarayıcınız konum özelliğini desteklemiyor.'))
          } else {
            navigator.geolocation.getCurrentPosition(resolve, reject)
          }
        })

        this.userLocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        }
        this.permissionGranted = true
        
        // Konum alındıktan sonra yakındaki işletmeleri getir
        await this.fetchNearbyBusinesses()
        
      } catch (err: any) {
        this.error = 'Konum erişimi reddedildi veya alınamadı.'
        this.permissionGranted = false
      } finally {
        this.loading = false
      }
    },

    async fetchNearbyBusinesses() {
      this.loading = true
      try {
        const response = await axios.get('/api/nearby-businesses', {
            params: this.userLocation
        })
        this.businesses = response.data
      } catch (err) {
          console.error('Failed to fetch nearby businesses', err)
          this.error = 'İşletmeler yüklenirken bir hata oluştu.'
      } finally {
          this.loading = false
      }
    }
  },

  getters: {
    restaurants: (state): Business[] => state.businesses.filter((b: Business) => b.category === 'restaurant'),
    hotels: (state): Business[] => state.businesses.filter((b: Business) => b.category === 'hotel'),
    nearbyCount: (state): number => state.businesses.length
  }
})
