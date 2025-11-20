<template>
  <div class="subscription-plans">
    <div class="container mx-auto px-4 py-12">
      <!-- Header -->
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Abonelik Planları</h1>
        <p class="text-xl text-gray-600">İşletmenize uygun planı seçin ve satışa başlayın</p>
        
        <!-- Billing Toggle -->
        <div class="flex items-center justify-center gap-4 mt-8">
          <span :class="billingCycle === 'monthly' ? 'font-semibold' : 'text-gray-500'">Aylık</span>
          <button 
            @click="billingCycle = billingCycle === 'monthly' ? 'yearly' : 'monthly'"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            :class="billingCycle === 'yearly' ? 'bg-blue-600' : 'bg-gray-300'"
          >
            <span 
              class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
              :class="billingCycle === 'yearly' ? 'translate-x-6' : 'translate-x-1'"
            ></span>
          </button>
          <span :class="billingCycle === 'yearly' ? 'font-semibold' : 'text-gray-500'">
            Yıllık <span class="text-green-600 text-sm">(2 ay ücretsiz)</span>
          </span>
        </div>
      </div>

      <!-- Plans Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div 
          v-for="plan in plans" 
          :key="plan.id"
          class="bg-white rounded-2xl shadow-lg overflow-hidden transition-transform hover:scale-105"
          :class="{ 'ring-4 ring-blue-500': plan.slug === 'premium' }"
        >
          <!-- Plan Header -->
          <div class="p-8 bg-gradient-to-br from-blue-50 to-white">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-2xl font-bold text-gray-900">{{ plan.name }}</h3>
              <span v-if="plan.slug === 'premium'" class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm">
                Popüler
              </span>
            </div>
            <p class="text-gray-600 mb-6">{{ plan.description }}</p>
            
            <!-- Price -->
            <div class="mb-6">
              <div class="flex items-baseline">
                <span class="text-5xl font-bold text-gray-900">
                  {{ billingCycle === 'yearly' ? plan.yearly_price : plan.price }}₺
                </span>
                <span class="text-gray-500 ml-2">/{{ billingCycle === 'yearly' ? 'yıl' : 'ay' }}</span>
              </div>
              <div v-if="plan.trial_days > 0" class="mt-2 text-sm text-green-600">
                {{ plan.trial_days }} gün ücretsiz deneme
              </div>
            </div>

            <!-- CTA Button -->
            <button 
              @click="selectPlan(plan)"
              class="w-full py-3 px-6 rounded-lg font-semibold transition-colors"
              :class="currentPlan?.id === plan.id 
                ? 'bg-gray-300 text-gray-700 cursor-not-allowed' 
                : 'bg-blue-600 text-white hover:bg-blue-700'"
              :disabled="currentPlan?.id === plan.id"
            >
              {{ currentPlan?.id === plan.id ? 'Mevcut Plan' : 'Planı Seç' }}
            </button>
          </div>

          <!-- Features List -->
          <div class="p-8">
            <h4 class="font-semibold text-gray-900 mb-4">Özellikler:</h4>
            <ul class="space-y-3">
              <li v-for="(feature, index) in plan.features" :key="index" class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-gray-600 text-sm">{{ feature }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Current Subscription Status -->
      <div v-if="subscription" class="mt-12 bg-blue-50 border border-blue-200 rounded-xl p-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Mevcut Aboneliğiniz</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <p class="text-gray-600 text-sm mb-1">Plan</p>
            <p class="text-xl font-semibold">{{ subscription.plan.name }}</p>
          </div>
          <div>
            <p class="text-gray-600 text-sm mb-1">Durum</p>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
              :class="{
                'bg-green-100 text-green-800': subscription.status === 'active',
                'bg-yellow-100 text-yellow-800': subscription.status === 'trial',
                'bg-red-100 text-red-800': subscription.status === 'cancelled'
              }">
              {{ subscription.status === 'active' ? 'Aktif' : subscription.status === 'trial' ? 'Deneme' : 'İptal Edildi' }}
            </span>
          </div>
          <div>
            <p class="text-gray-600 text-sm mb-1">Kalan Gün</p>
            <p class="text-xl font-semibold">{{ subscription.days_remaining }} gün</p>
          </div>
          <div>
            <p class="text-gray-600 text-sm mb-1">Ürün Kullanımı</p>
            <p class="text-xl font-semibold">{{ subscription.product_count }} / {{ subscription.product_limit }}</p>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
              <div class="bg-blue-600 h-2 rounded-full" 
                :style="{ width: (subscription.product_count / subscription.product_limit * 100) + '%' }">
              </div>
            </div>
          </div>
        </div>
        
        <div class="mt-6 flex gap-4">
          <button 
            v-if="subscription.status === 'active'"
            @click="cancelSubscription"
            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
          >
            Aboneliği İptal Et
          </button>
          <button 
            v-if="subscription.status === 'cancelled'"
            @click="renewSubscription"
            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
          >
            Aboneliği Yenile
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const billingCycle = ref('monthly')
const plans = ref([])
const subscription = ref(null)
const currentPlan = ref(null)

onMounted(async () => {
  await loadPlans()
  await loadCurrentSubscription()
})

const loadPlans = async () => {
  try {
    const response = await axios.get('/api/subscriptions/plans')
    plans.value = response.data
  } catch (error) {
    console.error('Plans yüklenemedi:', error)
  }
}

const loadCurrentSubscription = async () => {
  try {
    const response = await axios.get('/api/subscriptions/my-subscription')
    if (response.data.subscription) {
      subscription.value = response.data
      currentPlan.value = response.data.plan
    }
  } catch (error) {
    console.error('Abonelik bilgisi yüklenemedi:', error)
  }
}

const selectPlan = async (plan) => {
  if (currentPlan.value?.id === plan.id) return

  if (!confirm(`${plan.name} planına abone olmak istediğinizden emin misiniz?`)) return

  try {
    const response = await axios.post('/api/subscriptions/subscribe', {
      plan_id: plan.id,
      billing_cycle: billingCycle.value,
      payment_method: 'credit_card' // This would be selected in a real payment flow
    })

    alert(response.data.message)
    await loadCurrentSubscription()
  } catch (error) {
    alert(error.response?.data?.error || 'Bir hata oluştu')
  }
}

const cancelSubscription = async () => {
  if (!confirm('Aboneliğinizi iptal etmek istediğinizden emin misiniz? Mevcut dönem sonuna kadar kullanmaya devam edebilirsiniz.')) return

  try {
    const response = await axios.post('/api/subscriptions/cancel')
    alert(response.data.message)
    await loadCurrentSubscription()
  } catch (error) {
    alert(error.response?.data?.error || 'Bir hata oluştu')
  }
}

const renewSubscription = async () => {
  try {
    const response = await axios.post('/api/subscriptions/renew')
    alert(response.data.message)
    await loadCurrentSubscription()
  } catch (error) {
    alert(error.response?.data?.error || 'Bir hata oluştu')
  }
}
</script>
