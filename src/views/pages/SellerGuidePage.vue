<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 text-white py-20">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 text-center">
        <span class="inline-block bg-white/20 backdrop-blur px-4 py-1 rounded-full text-sm font-medium mb-4">
          📚 Satıcı Rehberi
        </span>
        <h1 class="text-4xl md:text-5xl font-black mb-4">Satışa Başlama Rehberi</h1>
        <p class="text-xl text-purple-100 max-w-2xl mx-auto mb-8">
          sportoonline'da başarılı bir satıcı olmak için bilmeniz gereken her şey
        </p>
        <div class="flex justify-center gap-4 flex-wrap">
          <router-link to="/apply-seller" class="bg-white text-purple-600 px-8 py-3 rounded-xl font-bold hover:bg-purple-50 transition-colors">
            🚀 Hemen Başla
          </router-link>
          <a href="#" class="bg-white/20 backdrop-blur text-white px-8 py-3 rounded-xl font-bold hover:bg-white/30 transition-colors">
            📄 PDF İndir
          </a>
        </div>
      </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
      <!-- Quick Stats -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
        <div class="bg-white rounded-2xl p-6 text-center shadow-sm">
          <div class="text-3xl font-black text-purple-600">45K+</div>
          <div class="text-sm text-slate-500">Aktif Satıcı</div>
        </div>
        <div class="bg-white rounded-2xl p-6 text-center shadow-sm">
          <div class="text-3xl font-black text-purple-600">5M+</div>
          <div class="text-sm text-slate-500">Potansiyel Müşteri</div>
        </div>
        <div class="bg-white rounded-2xl p-6 text-center shadow-sm">
          <div class="text-3xl font-black text-purple-600">%8-15</div>
          <div class="text-sm text-slate-500">Komisyon Oranı</div>
        </div>
        <div class="bg-white rounded-2xl p-6 text-center shadow-sm">
          <div class="text-3xl font-black text-purple-600">7/24</div>
          <div class="text-sm text-slate-500">Satıcı Desteği</div>
        </div>
      </div>

      <!-- Steps -->
      <div class="mb-16">
        <h2 class="text-3xl font-bold text-slate-800 text-center mb-10">Adım Adım Satıcı Olun</h2>
        
        <div class="space-y-6">
          <div v-for="(step, index) in steps" :key="index" class="bg-white rounded-2xl p-6 shadow-sm flex items-start gap-6">
            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center text-white font-black text-xl flex-shrink-0">
              {{ index + 1 }}
            </div>
            <div class="flex-1">
              <h3 class="text-xl font-bold text-slate-800 mb-2">{{ step.title }}</h3>
              <p class="text-slate-600 mb-4">{{ step.description }}</p>
              <div class="flex flex-wrap gap-2">
                <span v-for="tag in step.tags" :key="tag" class="bg-purple-100 text-purple-700 text-xs px-3 py-1 rounded-full">
                  {{ tag }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Requirements -->
      <div class="grid md:grid-cols-2 gap-8 mb-16">
        <div class="bg-white rounded-2xl p-8 shadow-sm">
          <h3 class="text-xl font-bold text-slate-800 mb-6">📋 Gerekli Belgeler</h3>
          <ul class="space-y-4">
            <li class="flex items-center gap-3">
              <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-600">✓</span>
              <span class="text-slate-600">Vergi Levhası</span>
            </li>
            <li class="flex items-center gap-3">
              <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-600">✓</span>
              <span class="text-slate-600">İmza Sirküleri</span>
            </li>
            <li class="flex items-center gap-3">
              <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-600">✓</span>
              <span class="text-slate-600">Ticaret Sicil Gazetesi</span>
            </li>
            <li class="flex items-center gap-3">
              <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-600">✓</span>
              <span class="text-slate-600">Banka Hesap Bilgileri (IBAN)</span>
            </li>
            <li class="flex items-center gap-3">
              <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-600">✓</span>
              <span class="text-slate-600">Faaliyet Belgesi</span>
            </li>
          </ul>
        </div>

        <div class="bg-white rounded-2xl p-8 shadow-sm">
          <h3 class="text-xl font-bold text-slate-800 mb-6">💰 Komisyon Oranları</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
              <span class="text-slate-600">Elektronik</span>
              <span class="font-bold text-purple-600">%8</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
              <span class="text-slate-600">Moda & Giyim</span>
              <span class="font-bold text-purple-600">%12</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
              <span class="text-slate-600">Ev & Yaşam</span>
              <span class="font-bold text-purple-600">%10</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
              <span class="text-slate-600">Kozmetik</span>
              <span class="font-bold text-purple-600">%15</span>
            </div>
            <div class="flex items-center justify-between py-3">
              <span class="text-slate-600">Spor & Outdoor</span>
              <span class="font-bold text-purple-600">%10</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Pricing Plans -->
      <div class="mb-16">
        <h2 class="text-3xl font-bold text-slate-800 text-center mb-10">Satıcı Paketleri</h2>
        <div class="grid md:grid-cols-3 gap-6">
          <div class="bg-white rounded-2xl p-6 shadow-sm border-2 border-transparent hover:border-purple-200 transition-colors">
            <div class="text-center mb-6">
              <h3 class="text-xl font-bold text-slate-800">Başlangıç</h3>
              <div class="text-3xl font-black text-purple-600 mt-2">Ücretsiz</div>
              <p class="text-sm text-slate-500 mt-1">Komisyon: Standart</p>
            </div>
            <ul class="space-y-3 mb-6">
              <li class="flex items-center gap-2 text-sm text-slate-600">
                <span class="text-green-500">✓</span> 50 ürün limiti
              </li>
              <li class="flex items-center gap-2 text-sm text-slate-600">
                <span class="text-green-500">✓</span> Temel analitik
              </li>
              <li class="flex items-center gap-2 text-sm text-slate-600">
                <span class="text-green-500">✓</span> E-posta destek
              </li>
            </ul>
            <router-link to="/apply-seller" class="block w-full text-center bg-slate-100 hover:bg-slate-200 text-slate-800 py-3 rounded-xl font-semibold transition-colors">
              Başla
            </router-link>
          </div>

          <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl p-6 shadow-lg text-white relative">
            <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-yellow-400 text-yellow-900 text-xs font-bold px-4 py-1 rounded-full">
              EN POPÜLER
            </div>
            <div class="text-center mb-6">
              <h3 class="text-xl font-bold">Profesyonel</h3>
              <div class="text-3xl font-black mt-2">₺299<span class="text-lg font-normal">/ay</span></div>
              <p class="text-sm text-purple-100 mt-1">Komisyon: %2 indirim</p>
            </div>
            <ul class="space-y-3 mb-6">
              <li class="flex items-center gap-2 text-sm">
                <span>✓</span> 500 ürün limiti
              </li>
              <li class="flex items-center gap-2 text-sm">
                <span>✓</span> Gelişmiş analitik
              </li>
              <li class="flex items-center gap-2 text-sm">
                <span>✓</span> Öncelikli destek
              </li>
              <li class="flex items-center gap-2 text-sm">
                <span>✓</span> Kampanya araçları
              </li>
            </ul>
            <router-link to="/apply-seller" class="block w-full text-center bg-white text-purple-600 py-3 rounded-xl font-semibold hover:bg-purple-50 transition-colors">
              Başla
            </router-link>
          </div>

          <div class="bg-white rounded-2xl p-6 shadow-sm border-2 border-transparent hover:border-purple-200 transition-colors">
            <div class="text-center mb-6">
              <h3 class="text-xl font-bold text-slate-800">Kurumsal</h3>
              <div class="text-3xl font-black text-purple-600 mt-2">₺999<span class="text-lg font-normal text-slate-500">/ay</span></div>
              <p class="text-sm text-slate-500 mt-1">Komisyon: %5 indirim</p>
            </div>
            <ul class="space-y-3 mb-6">
              <li class="flex items-center gap-2 text-sm text-slate-600">
                <span class="text-green-500">✓</span> Sınırsız ürün
              </li>
              <li class="flex items-center gap-2 text-sm text-slate-600">
                <span class="text-green-500">✓</span> AI kampanya önerileri
              </li>
              <li class="flex items-center gap-2 text-sm text-slate-600">
                <span class="text-green-500">✓</span> 7/24 VIP destek
              </li>
              <li class="flex items-center gap-2 text-sm text-slate-600">
                <span class="text-green-500">✓</span> API erişimi
              </li>
            </ul>
            <router-link to="/contact" class="block w-full text-center bg-slate-100 hover:bg-slate-200 text-slate-800 py-3 rounded-xl font-semibold transition-colors">
              İletişime Geç
            </router-link>
          </div>
        </div>
      </div>

      <!-- Success Stories -->
      <div class="bg-purple-50 rounded-3xl p-10 mb-16">
        <h2 class="text-2xl font-bold text-slate-800 text-center mb-8">Başarı Hikayeleri</h2>
        <div class="grid md:grid-cols-3 gap-6">
          <div class="bg-white rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">AK</div>
              <div>
                <div class="font-bold text-slate-800">Ahmet K.</div>
                <div class="text-sm text-slate-500">Elektronik Satıcısı</div>
              </div>
            </div>
            <p class="text-slate-600 text-sm italic">"İlk 6 ayda 500K₺ ciro yaptım. Platform desteği harika!"</p>
            <div class="mt-4 text-yellow-500">⭐⭐⭐⭐⭐</div>
          </div>
          <div class="bg-white rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-pink-400 to-pink-600 rounded-full flex items-center justify-center text-white font-bold">ZD</div>
              <div>
                <div class="font-bold text-slate-800">Zeynep D.</div>
                <div class="text-sm text-slate-500">Moda Butik</div>
              </div>
            </div>
            <p class="text-slate-600 text-sm italic">"Küçük butiğimi online'a taşıdım, şimdi 81 ile satış yapıyorum."</p>
            <div class="mt-4 text-yellow-500">⭐⭐⭐⭐⭐</div>
          </div>
          <div class="bg-white rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-bold">MB</div>
              <div>
                <div class="font-bold text-slate-800">Mehmet B.</div>
                <div class="text-sm text-slate-500">Ev & Yaşam</div>
              </div>
            </div>
            <p class="text-slate-600 text-sm italic">"AI kampanya önerileri sayesinde satışlarım %40 arttı."</p>
            <div class="mt-4 text-yellow-500">⭐⭐⭐⭐⭐</div>
          </div>
        </div>
      </div>

      <!-- CTA -->
      <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-3xl p-10 text-white text-center">
        <h2 class="text-3xl font-bold mb-4">Hemen Satıcı Olun!</h2>
        <p class="text-purple-100 mb-8 max-w-2xl mx-auto">
          5 milyon müşteriye ulaşın, yapay zeka destekli araçlarla satışlarınızı artırın
        </p>
        <router-link to="/apply-seller" class="inline-block bg-white text-purple-600 px-10 py-4 rounded-xl font-bold text-lg hover:bg-purple-50 transition-colors">
          🚀 Ücretsiz Başvur
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const steps = [
  {
    title: 'Başvuru Yapın',
    description: 'Online başvuru formunu doldurun ve gerekli belgeleri yükleyin. Başvuru tamamen ücretsizdir.',
    tags: ['2 dakika', 'Online', 'Ücretsiz']
  },
  {
    title: 'Onay Bekleyin',
    description: 'Ekibimiz başvurunuzu 2-3 iş günü içinde değerlendirir ve size e-posta ile bildirir.',
    tags: ['2-3 iş günü', 'E-posta bildirimi']
  },
  {
    title: 'Mağazanızı Oluşturun',
    description: 'Satıcı panelinize giriş yapın, mağaza bilgilerinizi girin ve logonuzu yükleyin.',
    tags: ['Kolay kurulum', 'Kılavuzlu']
  },
  {
    title: 'Ürünlerinizi Ekleyin',
    description: 'Ürünlerinizi tek tek veya toplu olarak ekleyin. Excel ile toplu yükleme yapabilirsiniz.',
    tags: ['Toplu yükleme', 'Excel desteği']
  },
  {
    title: 'Satışa Başlayın',
    description: 'Ürünleriniz onaylandıktan sonra milyonlarca müşteriye satış yapmaya başlayın!',
    tags: ['Anında yayın', '5M+ müşteri']
  }
]
</script>
