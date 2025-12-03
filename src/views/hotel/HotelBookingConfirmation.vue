<template>
  <div class="hotel-booking-confirmation min-h-screen bg-slate-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
      <!-- Success Header -->
      <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 mb-6">
        <div class="text-center mb-6">
          <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h1 class="text-3xl font-bold text-slate-900 mb-2">Rezervasyon Onaylandı! 🎉</h1>
          <p class="text-slate-600">
            Otel rezervasyonunuz başarıyla tamamlandı. Detaylar email adresinize gönderildi.
          </p>
        </div>

        <!-- Booking Details -->
        <div class="bg-slate-50 rounded-xl p-6 space-y-4">
          <h2 class="text-xl font-bold text-slate-900 mb-4">Rezervasyon Detayları</h2>
          
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <span class="text-sm font-medium text-slate-600 block mb-1">Otel</span>
              <span class="text-base font-semibold text-slate-900">{{ mockBooking.hotelName }}</span>
            </div>
            
            <div>
              <span class="text-sm font-medium text-slate-600 block mb-1">Rezervasyon No</span>
              <span class="text-base font-semibold text-slate-900">#{{ mockBooking.id }}</span>
            </div>
            
            <div>
              <span class="text-sm font-medium text-slate-600 block mb-1">Giriş Tarihi</span>
              <span class="text-base font-semibold text-slate-900">{{ formatDate(mockBooking.checkInDate) }}</span>
            </div>
            
            <div>
              <span class="text-sm font-medium text-slate-600 block mb-1">Çıkış Tarihi</span>
              <span class="text-base font-semibold text-slate-900">{{ formatDate(mockBooking.checkOutDate!) }}</span>
            </div>
            
            <div>
              <span class="text-sm font-medium text-slate-600 block mb-1">Misafir Sayısı</span>
              <span class="text-base font-semibold text-slate-900">{{ mockBooking.numberOfGuests }} kişi</span>
            </div>
            
            <div>
              <span class="text-sm font-medium text-slate-600 block mb-1">Oda Tipi</span>
              <span class="text-base font-semibold text-slate-900">{{ mockBooking.roomType }}</span>
            </div>
          </div>

          <div class="pt-4 border-t border-slate-200 mt-4">
            <div class="flex items-center justify-between">
              <span class="text-lg font-medium text-slate-700">Toplam Tutar</span>
              <span class="text-2xl font-bold text-slate-900">₺{{ formatMoney(mockBooking.totalPrice!) }}</span>
            </div>
          </div>
        </div>

        <!-- Address -->
        <div class="mt-4 flex items-start gap-3 p-4 bg-blue-50 rounded-xl">
          <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <div>
            <p class="text-sm font-medium text-blue-900 mb-1">Otel Adresi</p>
            <p class="text-sm text-blue-700">{{ mockBooking.destinationAddress }}</p>
          </div>
        </div>
      </div>

      <!-- Transfer Recommendation Component -->
      <TransferRecommendation 
        :hotelBooking="mockBooking"
        :autoShow="true"
      />

      <!-- Action Buttons -->
      <div class="bg-white rounded-2xl shadow-lg p-6 mt-6">
        <div class="flex flex-col sm:flex-row gap-4">
          <router-link
            to="/orders"
            class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span>Rezervasyonlarım</span>
          </router-link>
          
          <router-link
            to="/"
            class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-slate-200 text-slate-700 font-semibold rounded-xl hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 transition-all"
          >
            <span>Ana Sayfaya Dön</span>
          </router-link>
        </div>
      </div>

      <!-- Additional Info -->
      <div class="mt-6 bg-amber-50 border border-amber-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
          <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <div>
            <p class="text-sm font-medium text-amber-900 mb-1">Önemli Bilgiler</p>
            <ul class="text-sm text-amber-800 space-y-1">
              <li>• Giriş saati: 14:00, Çıkış saati: 12:00</li>
              <li>• Geç çıkış için lütfen otelle iletişime geçin</li>
              <li>• Rezervasyon değişikliği için en az 24 saat önceden bildirimde bulunun</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import TransferRecommendation from '@/components/TransferRecommendation.vue'
import type { HotelBooking } from '@/types/booking'

const route = useRoute()

// Mock booking data (in real app, this would come from the booking confirmation)
const mockBooking = ref<HotelBooking>({
  id: route.query.booking_id || 'HTL' + Math.floor(Math.random() * 100000),
  hotelName: 'Grand Istanbul Hotel & Spa',
  destinationCoordinates: {
    lat: 41.0082,
    lng: 28.9784
  },
  destinationAddress: 'Sultanahmet Meydanı No:1, 34122 Fatih/İstanbul',
  checkInDate: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], // 7 days from now
  checkOutDate: new Date(Date.now() + 10 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], // 10 days from now
  numberOfGuests: 2,
  roomType: 'Deluxe Çift Kişilik Oda',
  totalPrice: 2450,
  status: 'confirmed'
})

// Methods
const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('tr-TR', { 
    day: 'numeric', 
    month: 'long', 
    year: 'numeric' 
  })
}

const formatMoney = (amount: number) => {
  return new Intl.NumberFormat('tr-TR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount)
}

// Lifecycle
onMounted(() => {
  // Trigger HotelBookingCompleted event (for potential event listeners)
  const event = new CustomEvent('HotelBookingCompleted', {
    detail: {
      type: 'HotelBookingCompleted',
      booking: mockBooking.value,
      timestamp: new Date().toISOString()
    }
  })
  window.dispatchEvent(event)
})
</script>

<style scoped>
/* Additional styles if needed */
</style>
