<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-600 text-white py-8">
      <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <h1 class="text-2xl font-bold mb-2">👥 Grup Siparişi</h1>
        <p class="text-orange-100">Arkadaşlarınız veya iş arkadaşlarınızla birlikte sipariş verin!</p>
      </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
      <!-- Create or Join Section -->
      <div class="grid md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <div class="text-4xl mb-4">🆕</div>
          <h3 class="text-xl font-bold text-slate-800 mb-2">Yeni Grup Oluştur</h3>
          <p class="text-slate-500 mb-4">Bir sipariş grubu oluştur ve arkadaşlarınla paylaş</p>
          <button
            @click="createGroup"
            class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-xl font-medium transition-colors"
          >
            Grup Oluştur
          </button>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <div class="text-4xl mb-4">🔗</div>
          <h3 class="text-xl font-bold text-slate-800 mb-2">Gruba Katıl</h3>
          <p class="text-slate-500 mb-4">Paylaşılan link veya kod ile mevcut gruba katıl</p>
          <div class="flex gap-2">
            <input
              v-model="joinCode"
              type="text"
              placeholder="Grup kodunu girin..."
              class="flex-1 px-4 py-3 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-orange-500"
            />
            <button
              @click="joinGroup"
              class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-xl font-medium transition-colors"
            >
              Katıl
            </button>
          </div>
        </div>
      </div>

      <!-- Active Group Orders -->
      <div class="mb-8">
        <h2 class="text-xl font-bold text-slate-800 mb-4">Aktif Grup Siparişlerin</h2>
        
        <div class="space-y-4">
          <div
            v-for="group in activeGroups"
            :key="group.id"
            class="bg-white rounded-2xl p-6 shadow-sm"
          >
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <img :src="group.restaurant.image" :alt="group.restaurant.name" class="w-12 h-12 rounded-xl object-cover" />
                <div>
                  <h3 class="font-bold text-slate-800">{{ group.restaurant.name }}</h3>
                  <p class="text-sm text-slate-500">{{ group.createdBy }} tarafından oluşturuldu</p>
                </div>
              </div>
              <div class="text-right">
                <div class="text-sm text-slate-500">Kapanışa</div>
                <div class="font-bold text-orange-600">{{ group.remainingTime }}</div>
              </div>
            </div>
            
            <!-- Participants -->
            <div class="mb-4">
              <div class="text-sm text-slate-500 mb-2">Katılımcılar ({{ group.participants.length }})</div>
              <div class="flex -space-x-2">
                <div
                  v-for="(participant, index) in group.participants"
                  :key="index"
                  class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center text-white font-bold text-sm border-2 border-white"
                  :title="participant.name"
                >
                  {{ participant.name.charAt(0) }}
                </div>
              </div>
            </div>
            
            <!-- Status & Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
              <div>
                <span class="text-slate-500 text-sm">Toplam:</span>
                <span class="font-bold text-lg text-slate-800 ml-2">{{ group.total }}₺</span>
              </div>
              <div class="flex gap-2">
                <button class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2 rounded-lg text-sm transition-colors">
                  📋 Kopyala
                </button>
                <button class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                  Siparişimi Ekle
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- How It Works -->
      <div class="bg-white rounded-2xl p-6 shadow-sm">
        <h2 class="text-xl font-bold text-slate-800 mb-6">Nasıl Çalışır?</h2>
        <div class="grid md:grid-cols-4 gap-6">
          <div class="text-center">
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center text-2xl mx-auto mb-3">1️⃣</div>
            <h4 class="font-medium text-slate-800 mb-1">Grup Oluştur</h4>
            <p class="text-sm text-slate-500">Restoran seç ve grup oluştur</p>
          </div>
          <div class="text-center">
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center text-2xl mx-auto mb-3">2️⃣</div>
            <h4 class="font-medium text-slate-800 mb-1">Davet Et</h4>
            <p class="text-sm text-slate-500">Linki arkadaşlarınla paylaş</p>
          </div>
          <div class="text-center">
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center text-2xl mx-auto mb-3">3️⃣</div>
            <h4 class="font-medium text-slate-800 mb-1">Sipariş Ver</h4>
            <p class="text-sm text-slate-500">Herkes kendi siparişini ekler</p>
          </div>
          <div class="text-center">
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center text-2xl mx-auto mb-3">4️⃣</div>
            <h4 class="font-medium text-slate-800 mb-1">Teslim Al</h4>
            <p class="text-sm text-slate-500">Tek teslimat, kolay ödeme!</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import ServiceNav from '@/components/shared/ServiceNav.vue'

const joinCode = ref('')

const activeGroups = ref([
  {
    id: 1,
    restaurant: {
      name: 'Burger King',
      image: 'https://placehold.co/100x100/F97316/FFFFFF?text=BK'
    },
    createdBy: 'Ahmet Y.',
    remainingTime: '15:30',
    total: 285,
    participants: [
      { name: 'Ahmet Y.', order: 85 },
      { name: 'Mehmet K.', order: 120 },
      { name: 'Ayşe D.', order: 80 },
    ]
  },
  {
    id: 2,
    restaurant: {
      name: 'Domino\'s Pizza',
      image: 'https://placehold.co/100x100/3B82F6/FFFFFF?text=DP'
    },
    createdBy: 'Zeynep A.',
    remainingTime: '22:00',
    total: 420,
    participants: [
      { name: 'Zeynep A.', order: 180 },
      { name: 'Can B.', order: 240 },
    ]
  }
])

const createGroup = () => {
  console.log('Creating new group...')
}

const joinGroup = () => {
  console.log('Joining group with code:', joinCode.value)
}
</script>
