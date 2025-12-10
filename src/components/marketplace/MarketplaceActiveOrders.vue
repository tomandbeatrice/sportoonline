<template>
  <section v-if="activeOrders.length > 0" class="active-orders">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
        <span class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
          📦
        </span>
        {{ t('order.activeOrders') }}
      </h2>
      <router-link 
        to="/orders" 
        class="text-sm text-indigo-600 hover:text-indigo-700 font-medium"
      >
        {{ t('common.viewAll') }} →
      </router-link>
    </div>

    <!-- Mobile: Swipe Tab / Desktop: Grid -->
    <div 
      class="orders-container overflow-x-auto pb-2 -mx-4 px-4 md:mx-0 md:px-0"
      ref="ordersContainer"
      @scroll="handleOrdersScroll"
    >
      <div class="flex gap-4 md:grid md:grid-cols-3 min-w-max md:min-w-0">
        <div 
          v-for="order in activeOrders.slice(0, 6)"
          :key="order.id"
          class="order-card flex-shrink-0 w-72 md:w-auto bg-white rounded-xl shadow-sm border border-slate-100 p-4 hover:shadow-md transition-shadow"
        >
          <div class="flex gap-3">
            <!-- Product Image -->
            <div class="relative">
              <img 
                :src="order.product?.image || '/placeholder.jpg'" 
                :alt="order.product?.title"
                class="w-16 h-16 rounded-lg object-cover"
                loading="lazy"
              />
              <span 
                v-if="order.itemCount > 1"
                class="absolute -top-1 -right-1 w-5 h-5 bg-indigo-600 text-white text-xs rounded-full flex items-center justify-center"
              >
                +{{ order.itemCount - 1 }}
              </span>
            </div>

            <!-- Order Info -->
            <div class="flex-1 min-w-0">
              <p class="font-medium text-slate-900 truncate">
                {{ order.product?.title || `Sipariş #${order.id}` }}
              </p>
              <p class="text-sm text-slate-500">
                {{ formatDate(order.createdAt) }}
              </p>
              
              <!-- Status Badge -->
              <div class="mt-2 flex items-center gap-2">
                <span 
                  class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium"
                  :class="getOrderStatusClass(order.status)"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="getOrderStatusDotClass(order.status)"></span>
                  {{ getOrderStatusText(order.status) }}
                </span>
              </div>
            </div>

            <!-- Track Button -->
            <button 
              @click="trackOrder(order)"
              class="self-start p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
              :aria-label="t('order.track')"
            >
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </button>
          </div>

          <!-- Delivery Progress -->
          <div v-if="order.deliveryProgress" class="mt-3">
            <div class="flex justify-between text-xs text-slate-500 mb-1">
              <span>{{ t('order.delivery') }}</span>
              <span>{{ order.deliveryProgress }}%</span>
            </div>
            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
              <div 
                class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all"
                :style="{ width: order.deliveryProgress + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Scroll Indicator (Mobile) -->
    <div class="flex justify-center gap-1.5 mt-3 md:hidden">
      <span 
        v-for="i in Math.ceil(activeOrders.length / 2)"
        :key="i"
        class="w-1.5 h-1.5 rounded-full transition-colors"
        :class="currentOrderPage === i - 1 ? 'bg-indigo-600' : 'bg-slate-300'"
      />
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useOrderStore } from '@/stores/orderStore'

const router = useRouter()
const { t } = useI18n()
const orderStore = useOrderStore()

// State
const activeOrders = computed(() => orderStore.activeOrders)
const currentOrderPage = ref(0)
const ordersContainer = ref<HTMLElement | null>(null)

const formatDate = (date: string) => new Date(date).toLocaleDateString('tr-TR')

// Methods
const loadActiveOrders = async () => {
  if (activeOrders.value.length === 0) {
    await orderStore.fetchActiveOrders()
  }
}

const handleOrdersScroll = () => {
  if (!ordersContainer.value) return
  const scrollLeft = ordersContainer.value.scrollLeft
  const cardWidth = 288 + 16 // card width + gap
  currentOrderPage.value = Math.round(scrollLeft / (cardWidth * 2))
}

const getOrderStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-700',
    confirmed: 'bg-blue-100 text-blue-700',
    processing: 'bg-purple-100 text-purple-700',
    shipped: 'bg-indigo-100 text-indigo-700',
    delivered: 'bg-green-100 text-green-700'
  }
  return classes[status] || 'bg-slate-100 text-slate-700'
}

const getOrderStatusDotClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-500',
    confirmed: 'bg-blue-500',
    processing: 'bg-purple-500',
    shipped: 'bg-indigo-500',
    delivered: 'bg-green-500'
  }
  return classes[status] || 'bg-slate-500'
}

const getOrderStatusText = (status: string) => {
  return t(`order.status.${status}`)
}

const trackOrder = (order: Order) => {
  router.push(`/orders/${order.id}/track`)
}

onMounted(() => {
  loadActiveOrders()
})
</script>

<style scoped>
/* Smooth scrolling for orders container */
.orders-container {
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
}

.orders-container::-webkit-scrollbar {
  display: none;
}

.order-card {
  scroll-snap-align: start;
}
</style>
