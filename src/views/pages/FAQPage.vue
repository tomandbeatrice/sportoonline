<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-amber-500 via-orange-500 to-red-500 text-white py-16">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-black mb-4">Sıkça Sorulan Sorular</h1>
        <p class="text-xl text-amber-100 mb-8">Merak ettiklerinizin cevapları burada</p>
        
        <!-- Search -->
        <div class="max-w-xl mx-auto">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Soru ara..."
              class="w-full px-6 py-4 pl-12 rounded-2xl text-slate-800 focus:ring-4 focus:ring-white/30 focus:outline-none"
            />
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl">🔍</span>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
      <!-- Category Tabs -->
      <div class="flex flex-wrap gap-2 mb-8">
        <button
          v-for="category in categories"
          :key="category.id"
          @click="selectedCategory = category.id"
          :class="[
            'px-4 py-2 rounded-full text-sm font-medium transition-all',
            selectedCategory === category.id
              ? 'bg-orange-600 text-white'
              : 'bg-white text-slate-600 border border-slate-200 hover:border-orange-300'
          ]"
        >
          {{ category.icon }} {{ category.name }}
        </button>
      </div>

      <!-- FAQ Accordion -->
      <div class="space-y-4">
        <div
          v-for="faq in filteredFAQs"
          :key="faq.id"
          :id="faq.anchor"
          class="bg-white rounded-2xl shadow-sm overflow-hidden"
        >
          <button
            @click="toggleFAQ(faq.id)"
            class="w-full px-6 py-5 text-left flex items-center justify-between hover:bg-slate-50 transition-colors"
          >
            <span class="font-semibold text-slate-800 pr-4">{{ faq.question }}</span>
            <span class="text-2xl text-slate-400 flex-shrink-0" :class="{ 'rotate-180': openFAQs.includes(faq.id) }">
              ⌄
            </span>
          </button>
          <div
            v-show="openFAQs.includes(faq.id)"
            class="px-6 pb-5 text-slate-600 leading-relaxed border-t border-slate-100"
          >
            <div class="pt-4" v-html="faq.answer"></div>
          </div>
        </div>
      </div>

      <!-- Still Need Help -->
      <div class="mt-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-3xl p-8 text-white text-center">
        <h2 class="text-2xl font-bold mb-4">Cevabınızı bulamadınız mı?</h2>
        <p class="text-orange-100 mb-6">Müşteri hizmetlerimiz size yardımcı olmaya hazır</p>
        <div class="flex justify-center gap-4 flex-wrap">
          <router-link to="/contact" class="bg-white text-orange-600 px-6 py-3 rounded-xl font-semibold hover:bg-orange-50 transition-colors">
            📧 Bize Yazın
          </router-link>
          <button class="bg-white/20 backdrop-blur text-white px-6 py-3 rounded-xl font-semibold hover:bg-white/30 transition-colors">
            💬 Canlı Destek
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

const searchQuery = ref('')
const selectedCategory = ref('all')
const openFAQs = ref<number[]>([])

const categories = [
  { id: 'all', name: 'Tümü', icon: '📋' },
  { id: 'shipping', name: 'Kargo & Teslimat', icon: '📦' },
  { id: 'return', name: 'İade & Değişim', icon: '🔄' },
  { id: 'payment', name: 'Ödeme', icon: '💳' },
  { id: 'seller', name: 'Satıcı', icon: '🏪' },
  { id: 'account', name: 'Hesap', icon: '👤' },
]

const faqs = [
  // Shipping
  { id: 1, category: 'shipping', anchor: 'shipping', question: 'Kargo süreleri ne kadar?', answer: 'Siparişleriniz genellikle <strong>1-3 iş günü</strong> içinde kargoya verilir. Teslimat süresi bulunduğunuz lokasyona göre <strong>2-5 iş günü</strong> arasında değişmektedir. Aynı gün kargo seçeneği olan ürünlerde ise 17:00\'ye kadar verilen siparişler aynı gün kargoya verilir.' },
  { id: 2, category: 'shipping', anchor: 'tracking', question: 'Siparişimi nasıl takip edebilirim?', answer: 'Siparişlerinizi <strong>Hesabım > Siparişlerim</strong> bölümünden takip edebilirsiniz. Kargo takip numaranız e-posta ve SMS ile de gönderilmektedir. Ayrıca sipariş detay sayfasındaki "Kargo Takip" butonunu kullanabilirsiniz.' },
  { id: 3, category: 'shipping', anchor: 'free-shipping', question: 'Ücretsiz kargo şartları nelerdir?', answer: '<strong>150₺</strong> ve üzeri siparişlerde kargo ücretsizdir. Bazı satıcılar özel kampanyalarla daha düşük tutarlarda da ücretsiz kargo sunabilir. Ürün sayfasında kargo bilgisini kontrol edebilirsiniz.' },
  
  // Return
  { id: 4, category: 'return', anchor: 'return', question: 'Nasıl iade yapabilirim?', answer: '<strong>Hesabım > Siparişlerim</strong> bölümünden iade etmek istediğiniz ürünü seçin, iade nedenini belirtin ve onaylayın. Kargo kodu otomatik olarak oluşturulacaktır. Ürünü orijinal ambalajında, etiketleri sökülmemiş şekilde kargoya verin.' },
  { id: 5, category: 'return', anchor: 'return-period', question: 'İade süresi ne kadar?', answer: 'Tüm ürünlerde <strong>14 gün</strong> koşulsuz iade hakkınız bulunmaktadır. Elektronik ürünlerde bu süre <strong>7 gün</strong>dür. Kozmetik, iç giyim ve kişiye özel üretilen ürünler iade kapsamı dışındadır.' },
  { id: 6, category: 'return', anchor: 'refund', question: 'İade sonrası para ne zaman yatar?', answer: 'İade onayından sonra <strong>3-5 iş günü</strong> içinde ödemeniz iade edilir. Kredi kartı ödemelerinde banka süreçlerine bağlı olarak bu süre uzayabilir. Kapıda ödeme iadeleri IBAN\'a yapılır.' },
  
  // Payment
  { id: 7, category: 'payment', anchor: 'payment', question: 'Hangi ödeme yöntemlerini kullanabilirim?', answer: 'Kredi kartı (Mastercard, Visa, Troy), banka kartı, havale/EFT, kapıda ödeme (nakit veya kart) ve dijital cüzdan seçeneklerini kullanabilirsiniz. Taksit seçenekleri ürün ve banka anlaşmalarına göre değişmektedir.' },
  { id: 8, category: 'payment', anchor: 'installment', question: 'Taksit yapabilir miyim?', answer: 'Evet! <strong>100₺</strong> üzeri alışverişlerde 2-12 taksit seçenekleri mevcuttur. Taksit oranları ürün ve banka anlaşmasına göre değişir. Ödeme sayfasında taksit seçeneklerini görebilirsiniz.' },
  { id: 9, category: 'payment', anchor: 'security', question: 'Ödeme güvenliği nasıl sağlanıyor?', answer: 'Tüm ödemeleriniz <strong>256-bit SSL</strong> şifreleme ile korunmaktadır. Kredi kartı bilgileriniz sistemimizde saklanmaz. 3D Secure doğrulama ile ek güvenlik sağlanmaktadır.' },
  
  // Seller
  { id: 10, category: 'seller', anchor: 'seller', question: 'Nasıl satıcı olabilirim?', answer: '<router-link to="/apply-seller" class="text-orange-600 font-semibold">Satıcı Başvuru</router-link> sayfasından başvurunuzu yapabilirsiniz. Başvuru için vergi levhası, imza sirküleri ve banka hesap bilgileriniz gereklidir. Başvurular 2-3 iş günü içinde değerlendirilir.' },
  { id: 11, category: 'seller', anchor: 'commission', question: 'Satıcı komisyon oranları nedir?', answer: 'Komisyon oranları kategoriye göre <strong>%8-15</strong> arasında değişmektedir. Premium üyelik paketleri ile daha düşük komisyon oranlarından faydalanabilirsiniz. Detaylı bilgi için satıcı panelini inceleyebilirsiniz.' },
  { id: 12, category: 'seller', anchor: 'payout', question: 'Satış gelirlerim ne zaman ödenir?', answer: 'Satış gelirleriniz, ürün teslim edildikten <strong>14 gün</strong> sonra hesabınıza aktarılır. Premium satıcılar için bu süre 7 güne düşmektedir. Ödemeler her hafta Cuma günleri yapılır.' },
  
  // Account
  { id: 13, category: 'account', anchor: 'register', question: 'Nasıl üye olabilirim?', answer: 'Sağ üst köşedeki "Giriş Yap" butonuna tıklayarak kayıt olabilirsiniz. E-posta veya telefon numaranız ile kayıt olabilir, Google veya Apple hesabınızla da hızlıca giriş yapabilirsiniz.' },
  { id: 14, category: 'account', anchor: 'password', question: 'Şifremi unuttum, ne yapmalıyım?', answer: 'Giriş sayfasındaki "Şifremi Unuttum" linkine tıklayın. Kayıtlı e-posta adresinize şifre sıfırlama linki gönderilecektir. Link 24 saat geçerlidir.' },
  { id: 15, category: 'account', anchor: 'delete', question: 'Hesabımı nasıl silebilirim?', answer: '<strong>Hesabım > Ayarlar > Hesap Silme</strong> bölümünden hesabınızı silebilirsiniz. Hesap silme işlemi geri alınamaz ve tüm verileriniz kalıcı olarak silinir.' },
]

const filteredFAQs = computed(() => {
  let result = faqs
  
  if (selectedCategory.value !== 'all') {
    result = result.filter(faq => faq.category === selectedCategory.value)
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(faq => 
      faq.question.toLowerCase().includes(query) || 
      faq.answer.toLowerCase().includes(query)
    )
  }
  
  return result
})

const toggleFAQ = (id: number) => {
  const index = openFAQs.value.indexOf(id)
  if (index > -1) {
    openFAQs.value.splice(index, 1)
  } else {
    openFAQs.value.push(id)
  }
}
</script>
