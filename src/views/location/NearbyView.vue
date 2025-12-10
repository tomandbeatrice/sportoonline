<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b sticky top-0 z-30">
      <div class="container mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button @click="router.back()" class="p-2 hover:bg-gray-100 rounded-full">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </button>
          <h1 class="text-xl font-bold text-gray-900">Yakınımdaki İşletmeler</h1>
        </div>
        
        <div v-if="userLocation" class="flex items-center text-sm text-green-600 bg-green-50 px-3 py-1 rounded-full">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          Konum Aktif
        </div>
      </div>
    </div>

    <div class="container mx-auto px-4 py-8">
      <!-- Permission Request -->
      <LocationRequest 
        v-if="!permissionGranted" 
        :loading="loading"
        :error="error"
        @request="requestLocation"
      />

      <!-- Content -->
      <div v-else>
        <div class="mb-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-2">Sizin İçin Önerilenler</h2>
          <p class="text-gray-600">Konumunuza en yakın restoran, otel ve mağazalar.</p>
        </div>

        <NearbyBusinessList :businesses="businesses" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useLocationStore } from '@/stores/locationStore'
import LocationRequest from '@/components/location/LocationRequest.vue'
import NearbyBusinessList from '@/components/location/NearbyBusinessList.vue'

const router = useRouter()
const store = useLocationStore()

const loading = computed(() => store.loading)
const error = computed(() => store.error)
const permissionGranted = computed(() => store.permissionGranted)
const userLocation = computed(() => store.userLocation)
const businesses = computed(() => store.businesses)

function requestLocation() {
  store.requestUserLocation()
}

// Eğer daha önce izin verildiyse otomatik yükle
onMounted(() => {
  if (store.permissionGranted && !store.businesses.length) {
    store.fetchNearbyBusinesses()
  }
})
</script>
