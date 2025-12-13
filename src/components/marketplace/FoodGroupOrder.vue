<!-- 
  FoodGroupOrder.vue - Group Order Component for Food Delivery
  
  Features:
  - State 1 (Setup): Start group order with group name input
  - State 2 (Active): Display shareable link, guest simulation, cart management
  - Per-guest cart management
  - Bill breakdown/summary per person
-->
<template>
  <div class="food-group-order bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 px-6 py-4 text-white">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="text-3xl">👥</span>
          <div>
            <h2 class="text-xl font-bold">Grup Siparişi</h2>
            <p class="text-sm text-white/80">Arkadaşlarınla birlikte sipariş ver</p>
          </div>
        </div>
        <button 
          v-if="isActive"
          @click="closeGroupOrder"
          class="p-2 hover:bg-white/20 rounded-lg transition-colors"
          aria-label="Kapat"
        >
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </div>

    <div class="p-6">
      <!-- State 1: Setup -->
      <div v-if="!isActive" class="space-y-6">
        <div class="text-center py-8">
          <div class="text-6xl mb-4">🍽️</div>
          <h3 class="text-2xl font-bold text-slate-900 mb-2">
            Grup Siparişi Başlat
          </h3>
          <p class="text-slate-600 max-w-md mx-auto">
            Ofis arkadaşlarınla veya ailenle birlikte yemek siparişi vermek için bir grup oluştur.
          </p>
        </div>

        <div class="max-w-md mx-auto space-y-4">
          <!-- Group Name Input -->
          <div>
            <label for="groupName" class="block text-sm font-medium text-slate-700 mb-2">
              Grup Adı
            </label>
            <input
              id="groupName"
              v-model="groupName"
              type="text"
              placeholder="Örn: Ofis Öğle Yemeği"
              class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all"
              @keyup.enter="startGroupOrder"
            />
          </div>

          <!-- Start Button -->
          <button
            @click="startGroupOrder"
            :disabled="!groupName.trim()"
            class="w-full px-6 py-4 bg-orange-500 text-white font-bold rounded-xl hover:bg-orange-600 disabled:bg-slate-300 disabled:cursor-not-allowed transition-all shadow-lg shadow-orange-200 hover:shadow-xl hover:-translate-y-0.5"
          >
            <span class="flex items-center justify-center gap-2">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Grup Siparişi Başlat
            </span>
          </button>
        </div>

        <!-- Features Info -->
        <div class="grid md:grid-cols-3 gap-4 mt-8 pt-8 border-t border-slate-200">
          <div class="text-center p-4">
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
              <span class="text-2xl">🔗</span>
            </div>
            <h4 class="font-semibold text-slate-900 mb-1">Paylaşılabilir Link</h4>
            <p class="text-sm text-slate-600">Arkadaşlarına link gönder</p>
          </div>
          <div class="text-center p-4">
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
              <span class="text-2xl">🛒</span>
            </div>
            <h4 class="font-semibold text-slate-900 mb-1">Kişiye Özel Sepet</h4>
            <p class="text-sm text-slate-600">Herkes kendi ürününü ekler</p>
          </div>
          <div class="text-center p-4">
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
              <span class="text-2xl">💰</span>
            </div>
            <h4 class="font-semibold text-slate-900 mb-1">Adil Ödeme</h4>
            <p class="text-sm text-slate-600">Kişi başı tutar hesaplanır</p>
          </div>
        </div>
      </div>

      <!-- State 2: Active -->
      <div v-else class="space-y-6">
        <!-- Group Info & Shareable Link -->
        <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 border border-orange-200">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="text-xl font-bold text-slate-900 mb-1">{{ groupName }}</h3>
              <p class="text-sm text-slate-600">{{ guests.length }} kişi katıldı</p>
            </div>
            <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">
              Aktif
            </span>
          </div>

          <!-- Shareable Link -->
          <div class="bg-white rounded-lg p-4 border border-orange-200">
            <label class="block text-sm font-medium text-slate-700 mb-2">
              Paylaşılabilir Link
            </label>
            <div class="flex gap-2">
              <input
                :value="shareableLink"
                type="text"
                readonly
                class="flex-1 px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm"
              />
              <button
                @click="copyLink"
                class="px-4 py-2 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors"
              >
                <span v-if="linkCopied" class="flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  Kopyalandı
                </span>
                <span v-else>Kopyala</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Guest Simulation Buttons -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200">
          <h4 class="font-semibold text-slate-900 mb-3">Misafir Ekle (Simülasyon)</h4>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="name in availableGuests"
              :key="name"
              @click="addGuest(name)"
              :disabled="guests.some(g => g.name === name)"
              class="px-4 py-2 bg-white border-2 border-orange-200 text-orange-600 font-medium rounded-lg hover:bg-orange-50 hover:border-orange-300 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
            >
              + {{ name }}
            </button>
          </div>
        </div>

        <!-- Guests & Their Carts -->
        <div class="space-y-4">
          <h4 class="font-semibold text-slate-900 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Katılımcılar ve Siparişleri
          </h4>
          
          <div v-if="guests.length === 0" class="text-center py-8 text-slate-500">
            <span class="text-4xl mb-2 block">👥</span>
            Henüz kimse katılmadı. Yukarıdaki butonlarla misafir ekleyin.
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="guest in guests"
              :key="guest.id"
              class="bg-white rounded-xl p-4 border-2 border-slate-200 hover:border-orange-300 transition-all"
            >
              <!-- Guest Header -->
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-400 rounded-full flex items-center justify-center text-white font-bold">
                    {{ guest.name.charAt(0) }}
                  </div>
                  <div>
                    <h5 class="font-semibold text-slate-900">{{ guest.name }}</h5>
                    <p class="text-sm text-slate-500">
                      {{ guest.items.length }} ürün - ₺{{ calculateGuestTotal(guest).toFixed(2) }}
                    </p>
                  </div>
                </div>
                <button
                  @click="removeGuest(guest.id)"
                  class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                  aria-label="Misafiri çıkar"
                >
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>

              <!-- Guest Items -->
              <div v-if="guest.items.length > 0" class="space-y-2 mb-3">
                <div
                  v-for="item in guest.items"
                  :key="item.id"
                  class="flex items-center justify-between bg-slate-50 rounded-lg p-3"
                >
                  <div class="flex-1">
                    <p class="font-medium text-slate-900">{{ item.name }}</p>
                    <p class="text-sm text-slate-500">{{ item.quantity }}x ₺{{ item.price.toFixed(2) }}</p>
                  </div>
                  <div class="flex items-center gap-3">
                    <span class="font-bold text-slate-900">
                      ₺{{ (item.quantity * item.price).toFixed(2) }}
                    </span>
                    <button
                      @click="removeItem(guest.id, item.id)"
                      class="p-1 text-slate-400 hover:text-red-500 transition-colors"
                      aria-label="Ürünü çıkar"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Add Item Button -->
              <button
                @click="addItemToGuest(guest.id)"
                class="w-full px-4 py-2 border-2 border-dashed border-slate-300 text-slate-600 font-medium rounded-lg hover:border-orange-400 hover:text-orange-600 hover:bg-orange-50 transition-all"
              >
                <span class="flex items-center justify-center gap-2">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                  </svg>
                  Ürün Ekle
                </span>
              </button>
            </div>
          </div>
        </div>

        <!-- Summary / Bill Breakdown -->
        <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-6 border-2 border-slate-200">
          <h4 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Sipariş Özeti
          </h4>

          <div class="space-y-3">
            <!-- Per Person Breakdown -->
            <div
              v-for="guest in guests"
              :key="'summary-' + guest.id"
              class="flex items-center justify-between py-2 border-b border-slate-300 last:border-0"
            >
              <span class="text-slate-700 font-medium">{{ guest.name }}</span>
              <span class="text-slate-900 font-bold">₺{{ calculateGuestTotal(guest).toFixed(2) }}</span>
            </div>

            <!-- Total -->
            <div class="flex items-center justify-between pt-4 border-t-2 border-slate-400">
              <span class="text-lg font-bold text-slate-900">Toplam Tutar</span>
              <span class="text-2xl font-bold text-orange-600">₺{{ calculateTotalAmount().toFixed(2) }}</span>
            </div>

            <!-- Average per Person -->
            <div v-if="guests.length > 0" class="flex items-center justify-between text-sm text-slate-600 pt-2">
              <span>Kişi Başı Ortalama</span>
              <span class="font-semibold">₺{{ (calculateTotalAmount() / guests.length).toFixed(2) }}</span>
            </div>
          </div>

          <!-- Complete Order Button -->
          <button
            @click="completeOrder"
            :disabled="guests.length === 0 || calculateTotalAmount() === 0"
            class="w-full mt-6 px-6 py-4 bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold rounded-xl hover:from-orange-600 hover:to-red-600 disabled:from-slate-300 disabled:to-slate-400 disabled:cursor-not-allowed transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5"
          >
            <span class="flex items-center justify-center gap-2">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              Siparişi Tamamla (₺{{ calculateTotalAmount().toFixed(2) }})
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useModal } from '@/composables/useModal'

// Types
interface CartItem {
  id: number
  name: string
  price: number
  quantity: number
}

interface Guest {
  id: number
  name: string
  items: CartItem[]
}

// Modal composable
const modal = useModal()

// State
const isActive = ref(false)
const groupName = ref('')
const groupId = ref('')
const linkCopied = ref(false)
const guests = ref<Guest[]>([])
let nextGuestId = 1
let nextItemId = 1

// Available guest names for simulation
const availableGuests = ['Ali', 'Ayşe', 'Mehmet', 'Fatma', 'Ahmet', 'Zeynep']

// Sample menu items for simulation
const sampleMenuItems = [
  { name: 'Izgara Tavuk', price: 85.00 },
  { name: 'Köfte', price: 95.00 },
  { name: 'Pizza Margherita', price: 120.00 },
  { name: 'Hamburger', price: 75.00 },
  { name: 'Salata', price: 45.00 },
  { name: 'Makarna', price: 65.00 },
  { name: 'Lahmacun', price: 55.00 },
  { name: 'Ayran', price: 15.00 },
  { name: 'Kola', price: 25.00 },
  { name: 'Su', price: 10.00 }
]

// Computed
const shareableLink = computed(() => {
  return groupId.value 
    ? `sportoonline.com/group/${groupId.value}` 
    : ''
})

// Methods
const startGroupOrder = () => {
  if (!groupName.value.trim()) return
  
  // Generate a cryptographically secure random group ID
  if (typeof crypto !== 'undefined' && crypto.randomUUID) {
    groupId.value = crypto.randomUUID().split('-')[0]
  } else {
    // Fallback for older browsers
    groupId.value = Array.from(crypto.getRandomValues(new Uint8Array(4)))
      .map(b => b.toString(16).padStart(2, '0'))
      .join('')
  }
  isActive.value = true
}

const closeGroupOrder = async () => {
  const confirmed = await modal.confirm({
    title: 'Grup Siparişini İptal Et',
    message: 'Grup siparişini iptal etmek istediğinizden emin misiniz?',
    type: 'warning',
    confirmText: 'Evet, İptal Et',
    cancelText: 'Vazgeç'
  })
  
  if (confirmed) {
    isActive.value = false
    groupName.value = ''
    groupId.value = ''
    guests.value = []
    nextGuestId = 1
    nextItemId = 1
  }
}

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(shareableLink.value)
    linkCopied.value = true
    setTimeout(() => {
      linkCopied.value = false
    }, 2000)
  } catch (error) {
    console.error('Link kopyalanamadı:', error)
  }
}

const addGuest = (name: string) => {
  if (guests.value.some(g => g.name === name)) return
  
  guests.value.push({
    id: nextGuestId++,
    name,
    items: []
  })
}

const removeGuest = (guestId: number) => {
  guests.value = guests.value.filter(g => g.id !== guestId)
}

const addItemToGuest = (guestId: number) => {
  const guest = guests.value.find(g => g.id === guestId)
  if (!guest) return
  
  // Randomly select a menu item
  const randomItem = sampleMenuItems[Math.floor(Math.random() * sampleMenuItems.length)]
  
  guest.items.push({
    id: nextItemId++,
    name: randomItem.name,
    price: randomItem.price,
    quantity: 1
  })
}

const removeItem = (guestId: number, itemId: number) => {
  const guest = guests.value.find(g => g.id === guestId)
  if (!guest) return
  
  guest.items = guest.items.filter(item => item.id !== itemId)
}

const calculateGuestTotal = (guest: Guest): number => {
  return guest.items.reduce((sum, item) => sum + (item.price * item.quantity), 0)
}

const calculateTotalAmount = (): number => {
  return guests.value.reduce((sum, guest) => sum + calculateGuestTotal(guest), 0)
}

const completeOrder = async () => {
  if (guests.value.length === 0 || calculateTotalAmount() === 0) return
  
  const orderSummary = `Toplam: ₺${calculateTotalAmount().toFixed(2)}\n\nKatılımcılar:\n${guests.value.map(g => `${g.name}: ₺${calculateGuestTotal(g).toFixed(2)}`).join('\n')}`
  
  await modal.success(
    `Sipariş başarıyla tamamlandı!\n\n${orderSummary}`,
    'Sipariş Tamamlandı'
  )
  
  console.log('Order completed:', orderSummary)
  
  // Reset after completion
  isActive.value = false
  groupName.value = ''
  groupId.value = ''
  guests.value = []
  nextGuestId = 1
  nextItemId = 1
}
</script>

<style scoped>
/* Custom animations */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.food-group-order {
  animation: slideIn 0.3s ease-out;
}

/* Smooth transitions */
* {
  transition-property: color, background-color, border-color, opacity, transform;
  transition-duration: 150ms;
  transition-timing-function: ease-in-out;
}
</style>
