<template>
  <div class="min-h-screen bg-slate-50 pb-12 pt-6 font-sans">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <h1 class="sr-only">Ödeme Sayfası</h1>

      <!-- Steps Indicator -->
      <div class="mb-10">
        <div class="relative after:absolute after:inset-x-0 after:top-1/2 after:block after:h-0.5 after:-translate-y-1/2 after:rounded-lg after:bg-slate-200">
          <ol class="relative z-10 flex justify-between text-sm font-medium text-slate-500">
            <li class="flex items-center gap-2 bg-slate-50 p-2" :class="{ 'text-blue-600': step >= 1 }">
              <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold" :class="step >= 1 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-600'">1</span>
              <span class="hidden sm:block">Adres Bilgileri</span>
            </li>
            <li class="flex items-center gap-2 bg-slate-50 p-2" :class="{ 'text-blue-600': step >= 2 }">
              <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold" :class="step >= 2 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-600'">2</span>
              <span class="hidden sm:block">Kargo Seçimi</span>
            </li>
            <li class="flex items-center gap-2 bg-slate-50 p-2" :class="{ 'text-blue-600': step >= 3 }">
              <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold" :class="step >= 3 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-600'">3</span>
              <span class="hidden sm:block">Ödeme</span>
            </li>
            <li class="flex items-center gap-2 bg-slate-50 p-2" :class="{ 'text-blue-600': step >= 4 }">
              <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold" :class="step >= 4 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-600'">4</span>
              <span class="hidden sm:block">Onay</span>
            </li>
          </ol>
        </div>
      </div>

      <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">
        <!-- Main Form Area -->
        <div class="lg:col-span-7">
          <!-- Step 1: Address -->
          <div v-if="step === 1" class="space-y-6">
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-100">
              <h2 class="text-lg font-bold text-slate-900 mb-4">Teslimat Adresi</h2>
              <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium text-slate-700">Adres Başlığı</label>
                  <input v-model="form.addressTitle" type="text" class="mt-1 block w-full rounded-lg border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Ev, İş vb." />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700">Ad</label>
                  <input v-model="form.firstName" type="text" class="mt-1 block w-full rounded-lg border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700">Soyad</label>
                  <input v-model="form.lastName" type="text" class="mt-1 block w-full rounded-lg border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium text-slate-700">Adres</label>
                  <textarea v-model="form.address" rows="3" class="mt-1 block w-full rounded-lg border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700">Şehir</label>
                  <input v-model="form.city" type="text" class="mt-1 block w-full rounded-lg border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700">Posta Kodu</label>
                  <input v-model="form.zipCode" type="text" class="mt-1 block w-full rounded-lg border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium text-slate-700">Telefon</label>
                  <input v-model="form.phone" type="tel" class="mt-1 block w-full rounded-lg border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                </div>
              </div>
            </div>
          </div>

          <!-- Step 2: Shipping -->
          <div v-if="step === 2" class="space-y-6">
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-100">
              <h2 class="text-lg font-bold text-slate-900 mb-4">Kargo Seçimi</h2>
              <div class="space-y-4">
                <label class="relative flex cursor-pointer rounded-xl border p-4 shadow-sm focus:outline-none" :class="form.shippingMethod === 'standard' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-slate-200'">
                  <input type="radio" name="shipping-method" value="standard" v-model="form.shippingMethod" class="sr-only" />
                  <span class="flex flex-1">
                    <span class="flex flex-col">
                      <span class="block text-sm font-medium text-slate-900">Standart Kargo</span>
                      <span class="mt-1 flex items-center text-sm text-slate-500">3-5 iş günü içinde teslimat</span>
                      <span class="mt-6 text-sm font-medium text-slate-900">Ücretsiz</span>
                    </span>
                  </span>
                  <svg class="h-5 w-5 text-blue-600" v-if="form.shippingMethod === 'standard'" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                  <span class="pointer-events-none absolute -inset-px rounded-xl" aria-hidden="true"></span>
                </label>

                <label class="relative flex cursor-pointer rounded-xl border p-4 shadow-sm focus:outline-none" :class="form.shippingMethod === 'express' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-slate-200'">
                  <input type="radio" name="shipping-method" value="express" v-model="form.shippingMethod" class="sr-only" />
                  <span class="flex flex-1">
                    <span class="flex flex-col">
                      <span class="block text-sm font-medium text-slate-900">Hızlı Teslimat</span>
                      <span class="mt-1 flex items-center text-sm text-slate-500">24 saat içinde teslimat</span>
                      <span class="mt-6 text-sm font-medium text-slate-900">29.90 TL</span>
                    </span>
                  </span>
                  <svg class="h-5 w-5 text-blue-600" v-if="form.shippingMethod === 'express'" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                  <span class="pointer-events-none absolute -inset-px rounded-xl" aria-hidden="true"></span>
                </label>
              </div>
            </div>
          </div>

          <!-- Step 3: Payment -->
          <div v-if="step === 3" class="space-y-6">
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-100">
              <h2 class="text-lg font-bold text-slate-900 mb-4">Ödeme Bilgileri</h2>
              
              <div class="mb-6">
                <div class="flex space-x-4">
                  <button type="button" class="flex-1 rounded-lg border border-blue-600 bg-blue-50 py-2 text-sm font-medium text-blue-700 text-center">Kredi Kartı</button>
                  <button type="button" class="flex-1 rounded-lg border border-slate-200 bg-white py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 text-center">Havale / EFT</button>
                </div>
              </div>

              <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium text-slate-700">Kart Numarası</label>
                  <input v-model="form.cardNumber" type="text" placeholder="0000 0000 0000 0000" class="mt-1 block w-full rounded-lg border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700">Ad Soyad</label>
                  <input v-model="form.cardName" type="text" placeholder="Kart üzerindeki isim" class="mt-1 block w-full rounded-lg border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                </div>
                <div class="flex gap-4">
                  <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-700">SKT</label>
                    <input v-model="form.cardExpiry" type="text" placeholder="AA/YY" class="mt-1 block w-full rounded-lg border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                  </div>
                  <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-700">CVV</label>
                    <input v-model="form.cardCvv" type="text" placeholder="123" class="mt-1 block w-full rounded-lg border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 4: Review -->
          <div v-if="step === 4" class="space-y-6">
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-100 text-center py-12">
              <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 mb-4">
                <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <h2 class="text-2xl font-bold text-slate-900">Siparişiniz Alındı!</h2>
              <p class="mt-2 text-slate-500">Sipariş numaranız: <span class="font-mono font-bold text-slate-900">#SP829304</span></p>
              <p class="mt-1 text-slate-500">Sipariş detayları e-posta adresinize gönderildi.</p>
              
              <div class="mt-8">
                <router-link to="/" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-blue-700">
                  Alışverişe Devam Et
                </router-link>
              </div>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="mt-8 flex justify-between" v-if="step < 4">
            <button
              v-if="step > 1"
              @click="step--"
              class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
            >
              Geri Dön
            </button>
            <div v-else></div> <!-- Spacer -->
            
            <button
              @click="nextStep"
              class="rounded-xl bg-blue-600 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-blue-300"
            >
              {{ step === 3 ? 'Siparişi Tamamla' : 'Devam Et' }}
            </button>
          </div>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="mt-10 lg:col-span-5 lg:mt-0" v-if="step < 4">
          <div class="sticky top-24 rounded-2xl bg-white p-6 shadow-sm border border-slate-100">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Sipariş Özeti</h2>
            
            <ul class="divide-y divide-slate-100">
              <li v-for="item in cartStore.items" :key="item.id" class="flex py-4">
                <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-lg border border-slate-200">
                  <img :src="item.image_url || item.image" :alt="item.name" class="h-full w-full object-cover object-center" />
                </div>
                <div class="ml-4 flex flex-1 flex-col">
                  <div>
                    <div class="flex justify-between text-base font-medium text-slate-900">
                      <h3>{{ item.name }}</h3>
                      <p class="ml-4">{{ formatPrice(item.price * item.quantity) }} TL</p>
                    </div>
                    <p class="mt-1 text-sm text-slate-500">{{ item.category?.name || 'Genel' }}</p>
                  </div>
                  <div class="flex flex-1 items-end justify-between text-sm">
                    <p class="text-slate-500">Adet {{ item.quantity }}</p>
                  </div>
                </div>
              </li>
            </ul>

            <div class="mt-6 space-y-4 border-t border-slate-100 pt-6">
              <div class="flex items-center justify-between">
                <p class="text-sm text-slate-600">Ara Toplam</p>
                <p class="text-sm font-medium text-slate-900">{{ formatPrice(subtotal) }} TL</p>
              </div>
              <div class="flex items-center justify-between">
                <p class="text-sm text-slate-600">Kargo</p>
                <p class="text-sm font-medium text-slate-900">{{ formatPrice(shippingCost) }} TL</p>
              </div>
              <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                <p class="text-base font-bold text-slate-900">Toplam</p>
                <p class="text-xl font-bold text-blue-600">{{ total }} TL</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, reactive } from 'vue'
import { useToast } from 'vue-toastification'
import { useCartStore } from '../../stores/cartStore'
import { useOrderStore } from '../../stores/orderStore'
import { useRouter } from 'vue-router'
import { useFormValidation } from '@/composables/useFormValidation'

const toast = useToast()
const cartStore = useCartStore()
const orderStore = useOrderStore()
const router = useRouter()
const step = ref(1)

const form = reactive({
  addressTitle: '',
  firstName: '',
  lastName: '',
  address: '',
  city: '',
  zipCode: '',
  phone: '',
  shippingMethod: 'standard',
  cardNumber: '',
  cardName: '',
  cardExpiry: '',
  cardCvv: ''
})

const { validate, errors } = useFormValidation()

const subtotal = computed(() => cartStore.subtotal)

const shippingCost = computed(() => {
  return form.shippingMethod === 'express' ? 29.90 : 0
})

const total = computed(() => {
  return (subtotal.value + shippingCost.value).toFixed(2)
})

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2 }).format(price)
}

const validateStep1 = () => {
  const rules = {
    firstName: [{ rule: 'required', message: 'Ad alanı zorunludur' }],
    lastName: [{ rule: 'required', message: 'Soyad alanı zorunludur' }],
    address: [
      { rule: 'required', message: 'Adres alanı zorunludur' },
      { rule: 'minLength', value: 10, message: 'Adres en az 10 karakter olmalıdır' }
    ],
    city: [{ rule: 'required', message: 'Şehir alanı zorunludur' }],
    phone: [
      { rule: 'required', message: 'Telefon alanı zorunludur' },
      { rule: 'phone', message: 'Geçerli bir telefon numarası girin' }
    ]
  }

  return validate(form, rules)
}

const validateStep3 = () => {
  const rules = {
    cardNumber: [
      { rule: 'required', message: 'Kart numarası zorunludur' },
      { rule: 'minLength', value: 16, message: 'Kart numarası 16 haneli olmalıdır' }
    ],
    cardName: [{ rule: 'required', message: 'Kart üzerindeki isim zorunludur' }],
    cardExpiry: [
      { rule: 'required', message: 'Son kullanma tarihi zorunludur' },
      { rule: 'pattern', value: /^\d{2}\/\d{2}$/, message: 'Format: MM/YY' }
    ],
    cardCvv: [
      { rule: 'required', message: 'CVV zorunludur' },
      { rule: 'minLength', value: 3, message: 'CVV 3 haneli olmalıdır' }
    ]
  }

  return validate(form, rules)
}

const nextStep = () => {
  if (step.value === 1) {
    if (!validateStep1()) {
      toast.warning('Lütfen formu eksiksiz doldurun')
      return
    }
  }
  
  if (step.value === 3) {
    if (!validateStep3()) {
      toast.warning('Lütfen ödeme bilgilerini kontrol edin')
      return
    }

    toast.info('Ödeme işleniyor...', { timeout: 2000 })
    setTimeout(() => {
      orderStore.createOrder(cartStore.items, Number(total.value), form)
      step.value = 4
      cartStore.clearCart()
      toast.success('Siparişiniz başarıyla oluşturuldu!')
    }, 2000)
    return
  }

  step.value++
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>