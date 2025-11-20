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
        <!-- Cart Items -->
        <div class="lg:col-span-8">
          <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <ul role="list" class="divide-y divide-slate-100">
              <li v-for="item in cartStore.items" :key="item.id" class="flex p-6">
                <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-xl border border-slate-200">
                  <img :src="item.image_url || item.image" :alt="item.name" class="h-full w-full object-cover object-center" />
                </div>

                <div class="ml-4 flex flex-1 flex-col">
                  <div>
                    <div class="flex justify-between text-base font-medium text-slate-900">
                      <h3>
                        <router-link :to="`/products/${item.id}`" class="hover:text-blue-600">{{ item.name }}</router-link>
                      </h3>
                      <p class="ml-4">{{ formatPrice(item.price * item.quantity) }} ₺</p>
                    </div>
                    <p class="mt-1 text-sm text-slate-500">{{ item.category?.name || 'Genel' }}</p>
                  </div>
                  <div class="flex flex-1 items-end justify-between text-sm">
                    <div class="flex items-center border border-slate-200 rounded-lg">
                      <button @click="cartStore.updateQuantity(item.id, item.quantity - 1)" class="px-3 py-1 hover:bg-slate-50 text-slate-600">-</button>
                      <span class="px-2 py-1 font-medium text-slate-900">{{ item.quantity }}</span>
                      <button @click="cartStore.updateQuantity(item.id, item.quantity + 1)" class="px-3 py-1 hover:bg-slate-50 text-slate-600">+</button>
                    </div>

                    <button type="button" @click="cartStore.removeFromCart(item.id)" class="font-medium text-red-600 hover:text-red-500 flex items-center gap-1">
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                      Sil
                    </button>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="mt-16 rounded-2xl bg-white p-6 shadow-sm border border-slate-100 lg:col-span-4 lg:mt-0 lg:p-8">
          <h2 class="text-lg font-bold text-slate-900">Sipariş Özeti</h2>

          <div class="mt-6 space-y-4">
            <div class="flex items-center justify-between border-t border-slate-100 pt-4">
              <div class="text-base font-medium text-slate-900">Ara Toplam</div>
              <div class="text-base font-medium text-slate-900">{{ formatPrice(cartStore.subtotal) }} ₺</div>
            </div>
            <p class="text-xs text-slate-500 mt-1">Kargo ücreti ödeme adımında hesaplanacaktır.</p>
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
import { useCartStore } from '../../stores/cartStore'

const cartStore = useCartStore()

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2 }).format(price)
}
</script>
