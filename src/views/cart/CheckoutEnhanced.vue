<template>
  <div class="min-h-screen bg-slate-50 pb-12 pt-6 font-sans">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold text-slate-900 mb-8">Ödeme</h1>

      <!-- Steps Indicator -->
      <div class="mb-10">
        <div class="relative after:absolute after:inset-x-0 after:top-1/2 after:block after:h-0.5 after:-translate-y-1/2 after:rounded-lg after:bg-slate-200">
          <ol class="relative z-10 flex justify-between text-sm font-medium text-slate-500">
            <li 
              v-for="(stepItem, index) in steps" 
              :key="index"
              class="flex items-center gap-2 bg-slate-50 p-2 cursor-pointer"
              :class="{ 'text-blue-600': currentStep >= index + 1 }"
              @click="currentStep > index + 1 && (currentStep = index + 1)"
            >
              <span 
                class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold transition"
                :class="currentStep >= index + 1 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-600'"
              >
                {{ index + 1 }}
              </span>
              <span class="hidden sm:block">{{ stepItem }}</span>
            </li>
          </ol>
        </div>
      </div>

      <div class="lg:grid lg:grid-cols-12 lg:gap-x-8 lg:items-start">
        <!-- Main Form Area -->
        <div class="lg:col-span-7">
          
          <!-- Step 1: Address Selection -->
          <div v-if="currentStep === 1" class="space-y-6">
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200">
              <h2 class="text-lg font-bold text-slate-900 mb-4">Teslimat Adresi</h2>
              
              <!-- Saved Addresses -->
              <div v-if="savedAddresses.length > 0" class="space-y-3 mb-6">
                <label 
                  v-for="address in savedAddresses" 
                  :key="address.id"
                  class="flex items-start p-4 border border-slate-200 rounded-xl cursor-pointer hover:border-blue-500 transition"
                  :class="selectedAddress === address.id ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500' : ''"
                >
                  <input 
                    type="radio" 
                    :value="address.id"
                    v-model="selectedAddress"
                    class="mt-1 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500"
                  >
                  <div class="ml-3 flex-1">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-slate-900">{{ address.title }}</span>
                      <span v-if="address.isDefault" class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">
                        Varsayılan
                      </span>
                    </div>
                    <p class="text-sm text-slate-600 mt-1">{{ address.fullName }}</p>
                    <p class="text-sm text-slate-500 mt-1">{{ address.address }}, {{ address.district }}/{{ address.city }}</p>
                    <p class="text-sm text-slate-500">{{ address.phone }}</p>
                  </div>
                </label>
              </div>

              <!-- Add New Address Button -->
              <button 
                @click="showNewAddressForm = true"
                class="w-full py-3 px-4 border-2 border-dashed border-slate-300 rounded-xl text-slate-600 hover:border-blue-500 hover:text-blue-600 transition flex items-center justify-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Yeni Adres Ekle
              </button>

              <!-- New Address Form (Modal-like) -->
              <div v-if="showNewAddressForm" class="mt-6 p-6 bg-slate-50 rounded-xl border border-slate-200">
                <h3 class="font-bold text-slate-900 mb-4">Yeni Adres</h3>
                <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-4">
                  <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Adres Başlığı *</label>
                    <input v-model="newAddress.title" type="text" required placeholder="Ev, İş, vb." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Ad *</label>
                    <input v-model="newAddress.firstName" type="text" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Soyad *</label>
                    <input v-model="newAddress.lastName" type="text" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Telefon *</label>
                    <input v-model="newAddress.phone" type="tel" required placeholder="05XX XXX XX XX" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Şehir *</label>
                    <select v-model="newAddress.city" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                      <option value="">Seçiniz</option>
                      <option>İstanbul</option>
                      <option>Ankara</option>
                      <option>İzmir</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">İlçe *</label>
                    <input v-model="newAddress.district" type="text" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Posta Kodu</label>
                    <input v-model="newAddress.zipCode" type="text" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                  </div>
                  <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Adres *</label>
                    <textarea v-model="newAddress.address" rows="3" required placeholder="Mahalle, sokak, bina no, daire no" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                  </div>
                </div>
                <div class="mt-4 flex gap-3">
                  <button @click="saveNewAddress" class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700">
                    Kaydet ve Kullan
                  </button>
                  <button @click="showNewAddressForm = false" class="px-6 py-2.5 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50">
                    İptal
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 2: Shipping Method -->
          <div v-if="currentStep === 2" class="space-y-6">
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200">
              <h2 class="text-lg font-bold text-slate-900 mb-4">Kargo Seçimi</h2>
              <div class="space-y-4">
                <label 
                  v-for="option in shippingOptions" 
                  :key="option.id"
                  class="relative flex cursor-pointer rounded-xl border p-4 shadow-sm transition"
                  :class="selectedShipping === option.id ? 'border-blue-500 ring-2 ring-blue-500' : 'border-slate-200 hover:border-slate-300'"
                >
                  <input type="radio" :value="option.id" v-model="selectedShipping" class="sr-only" />
                  <span class="flex flex-1">
                    <span class="flex flex-col">
                      <span class="block text-sm font-medium text-slate-900">{{ option.name }}</span>
                      <span class="mt-1 flex items-center text-sm text-slate-500">
                        <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ option.duration }}
                      </span>
                      <span class="mt-4 text-sm font-medium text-slate-900">
                        {{ option.price === 0 ? 'Ücretsiz' : formatPrice(option.price) + ' TL' }}
                      </span>
                    </span>
                  </span>
                  <svg v-if="selectedShipping === option.id" class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                </label>
              </div>
            </div>
          </div>

          <!-- Step 3: Payment -->
          <div v-if="currentStep === 3" class="space-y-6">
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200">
              <h2 class="text-lg font-bold text-slate-900 mb-4">Ödeme Bilgileri</h2>
              
              <!-- Payment Method Tabs -->
              <div class="mb-6 flex space-x-4">
                <button 
                  @click="paymentMethod = 'card'"
                  :class="paymentMethod === 'card' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
                  class="flex-1 rounded-lg border py-2 text-sm font-medium text-center transition"
                >
                  💳 Kredi/Banka Kartı
                </button>
                <button 
                  @click="paymentMethod = 'bank'"
                  :class="paymentMethod === 'bank' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
                  class="flex-1 rounded-lg border py-2 text-sm font-medium text-center transition"
                >
                  🏦 Havale/EFT
                </button>
              </div>

              <!-- Card Payment Form -->
              <div v-if="paymentMethod === 'card'">
                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                  <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kart Numarası *</label>
                    <input 
                      v-model="paymentForm.cardNumber"
                      type="text" 
                      placeholder="0000 0000 0000 0000"
                      maxlength="19"
                      @input="formatCardNumber"
                      required
                      class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    />
                  </div>
                  <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kart Üzerindeki İsim *</label>
                    <input 
                      v-model="paymentForm.cardName"
                      type="text" 
                      placeholder="Ad Soyad"
                      required
                      class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase" 
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Son Kullanma Tarihi *</label>
                    <input 
                      v-model="paymentForm.cardExpiry"
                      type="text" 
                      placeholder="AA/YY"
                      maxlength="5"
                      @input="formatExpiry"
                      required
                      class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">CVV *</label>
                    <input 
                      v-model="paymentForm.cardCvv"
                      type="text" 
                      placeholder="123"
                      maxlength="4"
                      required
                      class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    />
                  </div>
                  <div class="sm:col-span-2">
                    <label class="flex items-center cursor-pointer">
                      <input 
                        type="checkbox" 
                        v-model="paymentForm.saveCard"
                        class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500"
                      >
                      <span class="ml-2 text-sm text-slate-700">Kartımı güvenle sakla (sonraki alışverişlerimde kullanmak için)</span>
                    </label>
                  </div>
                </div>

                <!-- 3D Secure Badge -->
                <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                  <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                  <div>
                    <div class="font-medium text-green-900 text-sm">3D Secure ile Güvenli Ödeme</div>
                    <div class="text-xs text-green-700">Ödemeniz banka tarafından onaylanacaktır</div>
                  </div>
                </div>
              </div>

              <!-- Bank Transfer Info -->
              <div v-else-if="paymentMethod === 'bank'" class="p-6 bg-slate-50 rounded-xl">
                <h3 class="font-bold text-slate-900 mb-4">Banka Hesap Bilgileri</h3>
                <div class="space-y-3 text-sm">
                  <div class="flex justify-between">
                    <span class="text-slate-600">Banka:</span>
                    <span class="font-medium text-slate-900">Ziraat Bankası</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-slate-600">Hesap Sahibi:</span>
                    <span class="font-medium text-slate-900">SportoOnline A.Ş.</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-slate-600">IBAN:</span>
                    <span class="font-medium text-slate-900 font-mono">TR98 0001 0000 0000 0000 0000 01</span>
                  </div>
                  <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-800">
                    ⚠️ Ödeme açıklamasına sipariş numaranızı yazmayı unutmayın!
                  </div>
                </div>
              </div>
            </div>

            <!-- Agreements -->
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200">
              <div class="space-y-3">
                <label class="flex items-start cursor-pointer">
                  <input 
                    type="checkbox" 
                    v-model="agreements.distanceSales"
                    required
                    class="mt-1 w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500"
                  >
                  <span class="ml-2 text-sm text-slate-700">
                    <a href="#" class="text-blue-600 hover:underline">Mesafeli Satış Sözleşmesi</a>'ni okudum, onaylıyorum.
                  </span>
                </label>
                <label class="flex items-start cursor-pointer">
                  <input 
                    type="checkbox" 
                    v-model="agreements.cancellation"
                    required
                    class="mt-1 w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500"
                  >
                  <span class="ml-2 text-sm text-slate-700">
                    <a href="#" class="text-blue-600 hover:underline">Cayma ve İptal Koşulları</a>'nı kabul ediyorum.
                  </span>
                </label>
                <label class="flex items-start cursor-pointer">
                  <input 
                    type="checkbox" 
                    v-model="agreements.privacy"
                    required
                    class="mt-1 w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500"
                  >
                  <span class="ml-2 text-sm text-slate-700">
                    Kişisel verilerimin işlenmesine dair <a href="#" class="text-blue-600 hover:underline">Aydınlatma Metni</a>'ni okudum.
                  </span>
                </label>
              </div>
            </div>
          </div>

          <!-- Step 4: Order Complete -->
          <div v-if="currentStep === 4" class="space-y-6">
            <div class="rounded-2xl bg-white p-12 shadow-sm border border-slate-200 text-center">
              <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-green-100 mb-6 animate-bounce">
                <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <h2 class="text-3xl font-bold text-slate-900 mb-2">Siparişiniz Alındı!</h2>
              <p class="text-slate-500 mb-2">Sipariş numaranız:</p>
              <p class="text-2xl font-mono font-bold text-blue-600 mb-6">#{{ orderNumber }}</p>
              <div class="max-w-md mx-auto text-sm text-slate-600 space-y-2">
                <p>✉️ Sipariş detayları <strong>{{ userEmail }}</strong> adresine gönderildi.</p>
                <p>📱 SMS ile de bilgilendirileceksiniz.</p>
                <p v-if="paymentMethod === 'bank'">🏦 Havale/EFT yaptıktan sonra siparişiniz onaylanacaktır.</p>
              </div>
              
              <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                <router-link 
                  :to="`/orders/${orderNumber}`"
                  class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-8 py-3 text-base font-bold text-white shadow-lg hover:bg-blue-700"
                >
                  Siparişimi Görüntüle
                </router-link>
                <router-link 
                  to="/"
                  class="inline-flex items-center justify-center rounded-xl border-2 border-slate-200 bg-white px-8 py-3 text-base font-medium text-slate-700 hover:bg-slate-50"
                >
                  Alışverişe Devam Et
                </router-link>
              </div>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="mt-8 flex justify-between" v-if="currentStep < 4">
            <button
              v-if="currentStep > 1"
              @click="currentStep--"
              class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
            >
              ← Geri
            </button>
            <div v-else></div>
            
            <button
              @click="nextStep"
              :disabled="!canProceed"
              class="rounded-xl bg-blue-600 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-blue-300 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ currentStep === 3 ? '🔒 Siparişi Tamamla' : 'Devam Et →' }}
            </button>
          </div>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="lg:col-span-5 mt-10 lg:mt-0" v-if="currentStep < 4">
          <div class="sticky top-24 space-y-6">
            <!-- Cart Items Summary -->
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200">
              <h3 class="font-bold text-slate-900 mb-4">Sipariş Özeti ({{ cart.totalItems }} ürün)</h3>
              
              <ul class="divide-y divide-slate-100 max-h-64 overflow-y-auto mb-4">
                <li v-for="item in cart.items" :key="item.id" class="flex gap-4 py-3">
                  <img 
                    :src="item.image || 'https://via.placeholder.com/60'" 
                    :alt="item.name"
                    class="h-16 w-16 flex-shrink-0 rounded-lg border border-slate-200 object-cover"
                  />
                  <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-medium text-slate-900 line-clamp-2">{{ item.name }}</h4>
                    <p class="text-xs text-slate-500 mt-1">Adet: {{ item.quantity }}</p>
                  </div>
                  <div class="text-sm font-medium text-slate-900">
                    {{ formatPrice(item.price * item.quantity) }} TL
                  </div>
                </li>
              </ul>

              <!-- Price Breakdown -->
              <div class="space-y-3 pt-4 border-t border-slate-200">
                <div class="flex justify-between text-sm">
                  <span class="text-slate-600">Ara Toplam</span>
                  <span class="font-medium text-slate-900">{{ formatPrice(subtotal) }} TL</span>
                </div>
                <div v-if="couponDiscount > 0" class="flex justify-between text-sm">
                  <span class="text-green-600">Kupon İndirimi</span>
                  <span class="font-medium text-green-600">-{{ formatPrice(couponDiscount) }} TL</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-slate-600">Kargo</span>
                  <span class="font-medium text-slate-900">
                    {{ shippingCost === 0 ? 'Ücretsiz' : formatPrice(shippingCost) + ' TL' }}
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-slate-600">KDV (%{{ taxRate }})</span>
                  <span class="font-medium text-slate-900">{{ formatPrice(taxAmount) }} TL</span>
                </div>
                <div class="border-t border-slate-200 pt-3 flex justify-between">
                  <span class="text-lg font-bold text-slate-900">Toplam</span>
                  <span class="text-2xl font-bold text-blue-600">{{ formatPrice(total) }} TL</span>
                </div>
              </div>
            </div>

            <!-- Trust Badges -->
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200">
              <div class="grid grid-cols-2 gap-4 text-center text-xs text-slate-600">
                <div class="flex flex-col items-center gap-2">
                  <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                  <span>Güvenli Ödeme</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                  </svg>
                  <span>Taksit Seçenekleri</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <svg class="w-8 h-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                  </svg>
                  <span>256-bit SSL</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <svg class="w-8 h-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                  </svg>
                  <span>Kolay İade</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cartStore'
import { useToast } from 'vue-toastification'

const router = useRouter()
const cart = useCartStore()
const toast = useToast()

// State
const currentStep = ref(1)
const selectedAddress = ref<number | null>(1)
const showNewAddressForm = ref(false)
const selectedShipping = ref('standard')
const paymentMethod = ref('card')
const orderNumber = ref('')
const userEmail = ref('user@example.com')

const steps = computed(() => {
  return ['Adres', 'Kargo', 'Ödeme', 'Onay']
})

const newAddress = ref({
  title: '',
  firstName: '',
  lastName: '',
  phone: '',
  city: '',
  district: '',
  zipCode: '',
  address: ''
})

const savedAddresses = ref([
  {
    id: 1,
    title: 'Evim',
    fullName: 'Ahmet Yılmaz',
    phone: '0532 XXX XX XX',
    city: 'İstanbul',
    district: 'Kadıköy',
    address: 'Caferağa Mah. Moda Cad. No:45 D:12',
    zipCode: '34710',
    isDefault: true
  }
])

const shippingOptions = ref([
  { id: 'standard', name: 'Standart Kargo', duration: '3-5 iş günü', price: 0 },
  { id: 'express', name: 'Hızlı Kargo', duration: '1-2 iş günü', price: 29.90 },
  { id: 'same-day', name: 'Aynı Gün Teslimat', duration: 'Bugün', price: 49.90 }
])

const paymentForm = ref({
  cardNumber: '',
  cardName: '',
  cardExpiry: '',
  cardCvv: '',
  saveCard: false
})

const agreements = ref({
  distanceSales: false,
  cancellation: false,
  privacy: false
})

const taxRate = ref(20)
const couponDiscount = ref(0)

// Computed
const canProceed = computed(() => {
  if (currentStep.value === 1) return selectedAddress.value !== null
  if (currentStep.value === 2) return selectedShipping.value !== null
  if (currentStep.value === 3) {
    if (paymentMethod.value === 'card') {
      return paymentForm.value.cardNumber.length >= 16 &&
             paymentForm.value.cardName.length > 0 &&
             paymentForm.value.cardExpiry.length === 5 &&
             paymentForm.value.cardCvv.length >= 3 &&
             agreements.value.distanceSales &&
             agreements.value.cancellation &&
             agreements.value.privacy
    }
    return agreements.value.distanceSales &&
           agreements.value.cancellation &&
           agreements.value.privacy
  }
  return true
})

const subtotal = computed(() => {
  return cart.items.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const shippingCost = computed(() => {
  const selected = shippingOptions.value.find(o => o.id === selectedShipping.value)
  return selected?.price || 0
})

const taxAmount = computed(() => {
  return (subtotal.value - couponDiscount.value) * (taxRate.value / 100)
})

const total = computed(() => {
  return subtotal.value - couponDiscount.value + shippingCost.value + taxAmount.value
})

// Methods
const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(price)
}

const formatCardNumber = (event: Event) => {
  const input = event.target as HTMLInputElement
  let value = input.value.replace(/\s/g, '')
  value = value.match(/.{1,4}/g)?.join(' ') || value
  paymentForm.value.cardNumber = value
}

const formatExpiry = (event: Event) => {
  const input = event.target as HTMLInputElement
  let value = input.value.replace(/\D/g, '')
  if (value.length >= 2) {
    value = value.substring(0, 2) + '/' + value.substring(2, 4)
  }
  paymentForm.value.cardExpiry = value
}

const saveNewAddress = () => {
  if (!newAddress.value.title || !newAddress.value.firstName || !newAddress.value.lastName ||
      !newAddress.value.phone || !newAddress.value.city || !newAddress.value.address) {
    toast.error('Lütfen tüm zorunlu alanları doldurun')
    return
  }

  const newAddr = {
    id: savedAddresses.value.length + 1,
    title: newAddress.value.title,
    fullName: `${newAddress.value.firstName} ${newAddress.value.lastName}`,
    phone: newAddress.value.phone,
    city: newAddress.value.city,
    district: newAddress.value.district,
    address: newAddress.value.address,
    zipCode: newAddress.value.zipCode,
    isDefault: false
  }

  savedAddresses.value.push(newAddr)
  selectedAddress.value = newAddr.id
  showNewAddressForm.value = false
  
  toast.success('Adres kaydedildi')
  
  // Reset form
  newAddress.value = {
    title: '',
    firstName: '',
    lastName: '',
    phone: '',
    city: '',
    district: '',
    zipCode: '',
    address: ''
  }
}

const nextStep = async () => {
  if (!canProceed.value) {
    toast.error('Lütfen gerekli bilgileri doldurun')
    return
  }

  if (currentStep.value === 3) {
    // Complete order
    try {
      // Simulate payment processing
      toast.info('Ödeme işleniyor...')
      
      await new Promise(resolve => setTimeout(resolve, 2000))
      
      // Generate order number
      orderNumber.value = 'SP' + Date.now().toString().slice(-6)
      
      // Clear cart
      cart.clearCart()
      
      currentStep.value = 4
      toast.success('Siparişiniz başarıyla oluşturuldu!')
    } catch (error) {
      toast.error('Ödeme işlemi başarısız. Lütfen tekrar deneyin.')
    }
  } else {
    currentStep.value++
  }
}
</script>
