<template>
  <div class="min-h-screen bg-slate-50 py-12 font-sans">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold text-slate-900 mb-8">Sepetim ({{ cartStore.totalItems }} Ürün)</h1>

      <div v-if="cartStore.items.length === 0" class="text-center py-20 bg-white rounded-3xl shadow-sm border border-slate-100">
        <div class="text-6xl mb-4">🛒</div>
        <h2 class="text-xl font-bold text-slate-900 mb-2">Sepetiniz boş</h2>
        <p class="text-slate-500 mb-8">Henüz sepetinize ürün eklemediniz.</p>
        <router-link to="/" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-8 py-3 text-base font-bold text-white shadow-lg shadow-blue-200 transition-all hover:bg-blue-700 hover:shadow-blue-300 hover:-translate-y-1">
          Alışverişe Başla
        </router-link>
      </div>

      <div v-else class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">
        <!-- Cart Items Grouped -->
        <div class="lg:col-span-8 space-y-6">
          
          <!-- Instant Delivery Group -->
          <div v-if="groupedItems.instant.length > 0" class="bg-white rounded-2xl shadow-sm border border-orange-100 overflow-hidden">
            <div class="bg-orange-50 px-6 py-3 border-b border-orange-100 flex items-center gap-2">
              <span class="text-xl">🛵</span>
              <h3 class="font-bold text-orange-800">Hemen Teslimat (30-45 dk)</h3>
            </div>
            <ul role="list" class="divide-y divide-slate-100">
              <li v-for="item in groupedItems.instant" :key="item.id" class="flex p-6">
                <CartItem :item="item" />
              </li>
            </ul>
          </div>

          <!-- Cargo Delivery Group -->
          <div v-if="groupedItems.cargo.length > 0" class="bg-white rounded-2xl shadow-sm border border-blue-100 overflow-hidden">
            <div class="bg-blue-50 px-6 py-3 border-b border-blue-100 flex items-center gap-2">
              <span class="text-xl">📦</span>
              <h3 class="font-bold text-blue-800">Kargo ile Teslimat (1-3 Gün)</h3>
            </div>
            <ul role="list" class="divide-y divide-slate-100">
              <li v-for="item in groupedItems.cargo" :key="item.id" class="flex p-6">
                <CartItem :item="item" />
              </li>
            </ul>
          </div>

          <!-- Digital/Service Group -->
          <div v-if="groupedItems.digital.length > 0" class="bg-white rounded-2xl shadow-sm border border-purple-100 overflow-hidden">
            <div class="bg-purple-50 px-6 py-3 border-b border-purple-100 flex items-center gap-2">
              <span class="text-xl">🎫</span>
              <h3 class="font-bold text-purple-800">Dijital & Rezervasyon</h3>
            </div>
            <ul role="list" class="divide-y divide-slate-100">
              <li v-for="item in groupedItems.digital" :key="item.id" class="flex p-6">
                <CartItem :item="item" />
              </li>
            </ul>
          </div>

        </div>

        <!-- Order Summary -->
        <div class="mt-16 rounded-2xl bg-white p-6 shadow-sm border border-slate-100 lg:col-span-4 lg:mt-0 lg:p-8 sticky top-24">
          <h2 class="text-lg font-bold text-slate-900">Sipariş Özeti</h2>

          <div class="mt-6 space-y-4">
            <div class="flex items-center justify-between text-sm">
              <span class="text-slate-600">Ara Toplam</span>
              <span class="font-medium text-slate-900">{{ formatPrice(cartStore.subtotal) }} ₺</span>
            </div>
            <div v-if="groupedItems.instant.length > 0" class="flex items-center justify-between text-sm">
              <span class="text-slate-600">Kurye Ücreti</span>
              <span class="font-medium text-slate-900">29,90 ₺</span>
            </div>
            <div v-if="groupedItems.cargo.length > 0" class="flex items-center justify-between text-sm">
              <span class="text-slate-600">Kargo Ücreti</span>
              <span class="font-medium text-slate-900">49,90 ₺</span>
            </div>
            <div class="flex items-center justify-between border-t border-slate-100 pt-4">
              <div class="text-lg font-bold text-slate-900">Toplam</div>
              <div class="text-lg font-bold text-blue-600">{{ formatPrice(totalPrice) }} ₺</div>
            </div>
          </div>

          <div class="mt-6">
            <router-link
              to="/checkout"
              class="flex w-full items-center justify-center rounded-xl border border-transparent bg-blue-600 px-6 py-3 text-base font-bold text-white shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-blue-300 transition-all"
            >
              Sepeti Onayla
            </router-link>
          </div>
          <div class="mt-6 flex justify-center text-center text-sm text-slate-500">
            <p>
              veya
              <router-link to="/" class="font-medium text-blue-600 hover:text-blue-500">
                Alışverişe Devam Et
                <span aria-hidden="true"> &rarr;</span>
              </router-link>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useCartStore } from '../../stores/cartStore'
import CartItem from '@/components/cart/CartItem.vue'

const cartStore = useCartStore()

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2 }).format(price)
}

const groupedItems = computed(() => cartStore.groupedItems)

const totalPrice = computed(() => {
  let total = cartStore.subtotal
  if (groupedItems.value.instant.length > 0) total += 29.90
  if (groupedItems.value.cargo.length > 0) total += 49.90
  return total
})
</script>
