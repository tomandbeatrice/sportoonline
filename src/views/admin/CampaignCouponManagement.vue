<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 px-6 py-4">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
            🎯 Kampanya ve Kupon Yönetimi
            <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 text-xs font-bold">ADMIN</span>
          </h1>
          <p class="text-slate-500 text-sm mt-1">Global kampanyalar, segment bazlı kuponlar ve çakışma çözümü</p>
        </div>
        <div class="flex gap-3">
          <button @click="checkConflicts" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 transition">
            <span>⚠️</span> Çakışma Kontrolü
          </button>
          <button @click="showCampaignModal = true" class="px-4 py-2 bg-orange-600 text-white rounded-lg font-bold hover:bg-orange-700 transition shadow-lg">
            <span>➕</span> Yeni Kampanya
          </button>
        </div>
      </div>
    </header>

    <!-- Tabs -->
    <div class="bg-white border-b border-slate-200">
      <div class="px-6 flex gap-1">
        <button @click="activeTab = 'campaigns'" class="px-6 py-3 font-medium border-b-2 transition" :class="activeTab === 'campaigns' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-600 hover:text-slate-800'">
          🎯 Kampanyalar
        </button>
        <button @click="activeTab = 'coupons'" class="px-6 py-3 font-medium border-b-2 transition" :class="activeTab === 'coupons' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-600 hover:text-slate-800'">
          🎟️ Kuponlar
        </button>
        <button @click="activeTab = 'segments'" class="px-6 py-3 font-medium border-b-2 transition" :class="activeTab === 'segments' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-600 hover:text-slate-800'">
          👥 Segmentler
        </button>
        <button @click="activeTab = 'conflicts'" class="px-6 py-3 font-medium border-b-2 transition relative" :class="activeTab === 'conflicts' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-600 hover:text-slate-800'">
          ⚠️ Çakışmalar
          <span v-if="conflicts.length > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">{{ conflicts.length }}</span>
        </button>
      </div>
    </div>

    <!-- Campaigns Tab -->
    <div v-if="activeTab === 'campaigns'" class="p-6">
      <!-- Campaign Stats -->
      <div class="grid grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">Toplam Kampanya</p>
              <p class="text-2xl font-bold text-slate-800">{{ campaigns.length }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-2xl">🎯</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">Aktif</p>
              <p class="text-2xl font-bold text-green-600">{{ campaigns.filter(c => c.status === 'active').length }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-2xl">✅</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">Zamanlanmış</p>
              <p class="text-2xl font-bold text-blue-600">{{ campaigns.filter(c => c.status === 'scheduled').length }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-2xl">📅</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">Global</p>
              <p class="text-2xl font-bold text-purple-600">{{ campaigns.filter(c => c.scope === 'global').length }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-2xl">🌍</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">Segment Bazlı</p>
              <p class="text-2xl font-bold text-orange-600">{{ campaigns.filter(c => c.scope === 'segment').length }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center text-2xl">👥</div>
          </div>
        </div>
      </div>

      <!-- Campaign Table -->
      <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
        <div class="p-4 border-b border-slate-200 flex justify-between items-center">
          <div class="flex gap-3">
            <input v-model="campaignSearch" type="text" placeholder="Kampanya ara..." class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
            <select v-model="campaignStatusFilter" class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              <option value="">Tüm Durumlar</option>
              <option value="active">Aktif</option>
              <option value="scheduled">Zamanlanmış</option>
              <option value="paused">Duraklatılmış</option>
              <option value="ended">Sona Ermiş</option>
            </select>
            <select v-model="campaignScopeFilter" class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              <option value="">Tüm Kapsamlar</option>
              <option value="global">Global</option>
              <option value="segment">Segment</option>
              <option value="category">Kategori</option>
            </select>
          </div>
        </div>
        <table class="w-full">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">Kampanya</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">Kapsam</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">Tarih Aralığı</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">İndirim</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">Kullanım</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">Durum</th>
              <th class="px-6 py-3 text-right text-xs font-bold text-slate-600 uppercase">İşlemler</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="campaign in filteredCampaigns" :key="campaign.id" class="hover:bg-slate-50">
              <td class="px-6 py-4">
                <div class="font-medium text-slate-900">{{ campaign.name }}</div>
                <div class="text-xs text-slate-500">{{ campaign.description }}</div>
              </td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 rounded text-xs font-medium" :class="getScopeBadge(campaign.scope)">
                  {{ getScopeLabel(campaign.scope) }}
                </span>
                <div v-if="campaign.target_segment" class="text-xs text-slate-500 mt-1">{{ campaign.target_segment }}</div>
              </td>
              <td class="px-6 py-4 text-sm text-slate-600">
                <div>{{ formatDate(campaign.start_date) }}</div>
                <div class="text-xs text-slate-400">{{ formatDate(campaign.end_date) }}</div>
              </td>
              <td class="px-6 py-4 text-sm">
                <div class="font-medium text-orange-600">
                  {{ campaign.discount_type === 'percentage' ? `${campaign.discount_value}%` : `${campaign.discount_value} TL` }}
                </div>
                <div v-if="campaign.min_amount" class="text-xs text-slate-500">Min: {{ formatCurrency(campaign.min_amount) }}</div>
              </td>
              <td class="px-6 py-4 text-sm text-slate-600">
                <div>{{ campaign.usage_count }} / {{ campaign.usage_limit || '∞' }}</div>
                <div class="w-full bg-slate-200 rounded-full h-1 mt-1">
                  <div class="bg-orange-600 h-1 rounded-full" :style="{ width: `${(campaign.usage_count / (campaign.usage_limit || campaign.usage_count)) * 100}%` }"></div>
                </div>
              </td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusBadge(campaign.status)">
                  {{ getStatusLabel(campaign.status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-right space-x-2">
                <button @click="viewCampaignDetails(campaign)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Detay</button>
                <button @click="editCampaign(campaign)" class="text-orange-600 hover:text-orange-800 text-sm font-medium">Düzenle</button>
                <button v-if="campaign.status === 'active'" @click="pauseCampaign(campaign)" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">Duraklat</button>
                <button @click="deleteCampaign(campaign)" class="text-red-600 hover:text-red-800 text-sm font-medium">Sil</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Coupons Tab -->
    <div v-if="activeTab === 'coupons'" class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-slate-800">Kupon Yönetimi</h2>
        <div class="flex gap-3">
          <button @click="bulkGenerateCoupons" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 transition">
            🔄 Toplu Üret
          </button>
          <button @click="showCouponModal = true" class="px-4 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">
            ➕ Yeni Kupon
          </button>
        </div>
      </div>

      <!-- Coupon Stats -->
      <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">Toplam Kupon</p>
              <p class="text-2xl font-bold text-slate-800">{{ coupons.length }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-2xl">🎟️</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">Kullanılmış</p>
              <p class="text-2xl font-bold text-green-600">{{ coupons.reduce((sum, c) => sum + c.usage_count, 0) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-2xl">✅</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">İndirim Tutarı</p>
              <p class="text-2xl font-bold text-orange-600">{{ formatCurrency(coupons.reduce((sum, c) => sum + (c.total_discount || 0), 0)) }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center text-2xl">💰</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">Ortalama İndirim</p>
              <p class="text-2xl font-bold text-purple-600">{{ coupons.length > 0 ? Math.round(coupons.reduce((sum, c) => sum + c.discount_value, 0) / coupons.length) : 0 }}%</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-2xl">📊</div>
          </div>
        </div>
      </div>

      <!-- Coupon Table -->
      <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
        <div class="p-4 border-b border-slate-200">
          <input v-model="couponSearch" type="text" placeholder="Kupon kodu ara..." class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
        </div>
        <table class="w-full">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">Kod</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">İndirim</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">Kurallar</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">Kullanım</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">Geçerlilik</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">Durum</th>
              <th class="px-6 py-3 text-right text-xs font-bold text-slate-600 uppercase">İşlemler</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="coupon in filteredCoupons" :key="coupon.id" class="hover:bg-slate-50">
              <td class="px-6 py-4">
                <div class="font-mono font-bold text-orange-600">{{ coupon.code }}</div>
                <div class="text-xs text-slate-500">{{ coupon.description }}</div>
              </td>
              <td class="px-6 py-4">
                <div class="font-medium text-slate-900">
                  {{ coupon.discount_type === 'percentage' ? `${coupon.discount_value}%` : `${formatCurrency(coupon.discount_value)}` }}
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-slate-600">
                <div v-if="coupon.min_amount">Min: {{ formatCurrency(coupon.min_amount) }}</div>
                <div v-if="coupon.max_discount">Max: {{ formatCurrency(coupon.max_discount) }}</div>
                <div v-if="coupon.user_limit">Kullanıcı başına: {{ coupon.user_limit }}</div>
              </td>
              <td class="px-6 py-4 text-sm">
                <div class="font-medium">{{ coupon.usage_count }} / {{ coupon.usage_limit || '∞' }}</div>
                <div class="w-full bg-slate-200 rounded-full h-1 mt-1">
                  <div class="bg-orange-600 h-1 rounded-full" :style="{ width: `${(coupon.usage_count / (coupon.usage_limit || 100)) * 100}%` }"></div>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-slate-600">
                <div>{{ formatDate(coupon.start_date) }}</div>
                <div class="text-xs text-slate-400">{{ formatDate(coupon.end_date) }}</div>
              </td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="coupon.is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'">
                  {{ coupon.is_active ? 'Aktif' : 'Pasif' }}
                </span>
              </td>
              <td class="px-6 py-4 text-right space-x-2">
                <button @click="copyCouponCode(coupon.code)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Kopyala</button>
                <button @click="editCoupon(coupon)" class="text-orange-600 hover:text-orange-800 text-sm font-medium">Düzenle</button>
                <button @click="deleteCoupon(coupon)" class="text-red-600 hover:text-red-800 text-sm font-medium">Sil</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Segments Tab -->
    <div v-if="activeTab === 'segments'" class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-slate-800">Müşteri Segmentleri</h2>
        <button @click="showSegmentModal = true" class="px-4 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">
          ➕ Yeni Segment
        </button>
      </div>

      <div class="grid grid-cols-3 gap-6">
        <div v-for="segment in segments" :key="segment.id" class="bg-white rounded-lg border border-slate-200 shadow-sm p-6">
          <div class="flex justify-between items-start mb-4">
            <div>
              <h3 class="text-lg font-bold text-slate-800">{{ segment.name }}</h3>
              <p class="text-sm text-slate-500">{{ segment.description }}</p>
            </div>
            <span class="text-2xl">{{ segment.icon }}</span>
          </div>

          <div class="space-y-2 mb-4">
            <div class="flex justify-between text-sm">
              <span class="text-slate-600">Üye Sayısı:</span>
              <span class="font-bold text-slate-800">{{ segment.member_count }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-slate-600">Aktif Kampanya:</span>
              <span class="font-bold text-orange-600">{{ segment.active_campaigns }}</span>
            </div>
            <div v-if="segment.rules" class="text-xs text-slate-500 mt-2">
              <div v-for="(rule, idx) in segment.rules" :key="idx" class="bg-slate-50 px-2 py-1 rounded mb-1">
                {{ rule }}
              </div>
            </div>
          </div>

          <div class="flex gap-2">
            <button @click="editSegment(segment)" class="flex-1 px-3 py-2 bg-orange-50 text-orange-600 rounded-lg text-sm font-medium hover:bg-orange-100 transition">Düzenle</button>
            <button @click="deleteSegment(segment)" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition">Sil</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Conflicts Tab -->
    <div v-if="activeTab === 'conflicts'" class="p-6">
      <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-800">Kampanya Çakışmaları</h2>
        <p class="text-sm text-slate-500">Aynı tarih aralığında çakışan kampanyalar</p>
      </div>

      <div v-if="conflicts.length === 0" class="bg-white rounded-lg border border-slate-200 p-12 text-center">
        <div class="text-6xl mb-4">✅</div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Çakışma Yok</h3>
        <p class="text-slate-500">Tüm kampanyalar uyumlu şekilde çalışıyor.</p>
      </div>

      <div v-else class="space-y-4">
        <div v-for="conflict in conflicts" :key="conflict.id" class="bg-white rounded-lg border border-red-200 shadow-sm p-6">
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center text-2xl flex-shrink-0">⚠️</div>
            <div class="flex-1">
              <h3 class="text-lg font-bold text-red-700 mb-2">{{ conflict.type === 'date' ? 'Tarih Çakışması' : 'Kapsam Çakışması' }}</h3>
              <div class="space-y-2 mb-4">
                <div class="flex items-center gap-3">
                  <span class="px-3 py-1 bg-red-50 text-red-700 rounded font-medium text-sm">{{ conflict.campaign1.name }}</span>
                  <span class="text-red-500">⟷</span>
                  <span class="px-3 py-1 bg-red-50 text-red-700 rounded font-medium text-sm">{{ conflict.campaign2.name }}</span>
                </div>
                <div class="text-sm text-slate-600">
                  <div>Çakışan Tarih: {{ formatDate(conflict.overlap_start) }} - {{ formatDate(conflict.overlap_end) }}</div>
                  <div v-if="conflict.affected_products">Etkilenen Ürün: {{ conflict.affected_products }} adet</div>
                </div>
              </div>
              <div class="flex gap-2">
                <button @click="resolveConflict(conflict, 'keep_first')" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition">1. Kampanyayı Tut</button>
                <button @click="resolveConflict(conflict, 'keep_second')" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition">2. Kampanyayı Tut</button>
                <button @click="resolveConflict(conflict, 'merge')" class="px-4 py-2 bg-green-50 text-green-600 rounded-lg text-sm font-medium hover:bg-green-100 transition">Birleştir</button>
                <button @click="resolveConflict(conflict, 'ignore')" class="px-4 py-2 bg-slate-50 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-100 transition">Yoksay</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Campaign Modal -->
    <Transition name="modal">
      <div v-if="showCampaignModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="showCampaignModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6 border-b border-slate-200 flex justify-between items-center sticky top-0 bg-white">
            <h2 class="text-xl font-bold text-slate-800">{{ editingCampaign ? 'Kampanya Düzenle' : 'Yeni Kampanya Oluştur' }}</h2>
            <button @click="showCampaignModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
          </div>

          <div class="p-6 space-y-6">
            <div class="grid grid-cols-2 gap-4">
              <div class="col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Kampanya Adı *</label>
                <input v-model="campaignForm.name" type="text" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500" placeholder="Yılbaşı İndirimi">
              </div>
              <div class="col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Açıklama</label>
                <textarea v-model="campaignForm.description" rows="2" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500"></textarea>
              </div>
            </div>

            <div class="bg-slate-50 p-4 rounded-lg">
              <h4 class="font-medium text-slate-800 mb-3">Kapsam</h4>
              <div class="grid grid-cols-3 gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input v-model="campaignForm.scope" type="radio" value="global" class="text-orange-600">
                  <span class="text-sm">🌍 Global</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input v-model="campaignForm.scope" type="radio" value="segment" class="text-orange-600">
                  <span class="text-sm">👥 Segment</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input v-model="campaignForm.scope" type="radio" value="category" class="text-orange-600">
                  <span class="text-sm">📁 Kategori</span>
                </label>
              </div>
              <div v-if="campaignForm.scope === 'segment'" class="mt-3">
                <select v-model="campaignForm.target_segment" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
                  <option value="">Segment Seçin</option>
                  <option v-for="seg in segments" :key="seg.id" :value="seg.name">{{ seg.name }}</option>
                </select>
              </div>
            </div>

            <div class="bg-slate-50 p-4 rounded-lg">
              <h4 class="font-medium text-slate-800 mb-3">İndirim</h4>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs text-slate-600 mb-1">İndirim Tipi</label>
                  <select v-model="campaignForm.discount_type" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
                    <option value="percentage">Yüzde (%)</option>
                    <option value="fixed">Sabit Tutar (TL)</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs text-slate-600 mb-1">İndirim Değeri</label>
                  <input v-model.number="campaignForm.discount_value" type="number" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
                </div>
                <div>
                  <label class="block text-xs text-slate-600 mb-1">Min Sepet (TL)</label>
                  <input v-model.number="campaignForm.min_amount" type="number" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
                </div>
                <div>
                  <label class="block text-xs text-slate-600 mb-1">Max İndirim (TL)</label>
                  <input v-model.number="campaignForm.max_discount" type="number" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
                </div>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Başlangıç Tarihi *</label>
                <input v-model="campaignForm.start_date" type="date" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Bitiş Tarihi *</label>
                <input v-model="campaignForm.end_date" type="date" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Kullanım Limiti</label>
              <input v-model.number="campaignForm.usage_limit" type="number" placeholder="Sınırsız için boş bırakın" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
            </div>
          </div>

          <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
            <button @click="showCampaignModal = false" class="px-6 py-2 border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50 transition">İptal</button>
            <button @click="saveCampaign" class="px-6 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">{{ editingCampaign ? 'Güncelle' : 'Oluştur' }}</button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Coupon Modal -->
    <Transition name="modal">
      <div v-if="showCouponModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="showCouponModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6 border-b border-slate-200 flex justify-between items-center sticky top-0 bg-white">
            <h2 class="text-xl font-bold text-slate-800">Yeni Kupon Oluştur</h2>
            <button @click="showCouponModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
          </div>

          <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Kupon Kodu *</label>
                <div class="flex gap-2">
                  <input v-model="couponForm.code" type="text" class="flex-1 px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500 font-mono" placeholder="YILBASI2025">
                  <button @click="generateCouponCode" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition">🔄 Üret</button>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">İndirim Tipi</label>
                <select v-model="couponForm.discount_type" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
                  <option value="percentage">Yüzde (%)</option>
                  <option value="fixed">Sabit Tutar (TL)</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Açıklama</label>
              <textarea v-model="couponForm.description" rows="2" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500"></textarea>
            </div>

            <div class="grid grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">İndirim Değeri</label>
                <input v-model.number="couponForm.discount_value" type="number" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Min Tutar (TL)</label>
                <input v-model.number="couponForm.min_amount" type="number" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Max İndirim (TL)</label>
                <input v-model.number="couponForm.max_discount" type="number" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Toplam Limit</label>
                <input v-model.number="couponForm.usage_limit" type="number" placeholder="Sınırsız" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Kullanıcı Başına</label>
                <input v-model.number="couponForm.user_limit" type="number" placeholder="1" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Durum</label>
                <select v-model="couponForm.is_active" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
                  <option :value="true">Aktif</option>
                  <option :value="false">Pasif</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Başlangıç Tarihi</label>
                <input v-model="couponForm.start_date" type="date" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Bitiş Tarihi</label>
                <input v-model="couponForm.end_date" type="date" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              </div>
            </div>
          </div>

          <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
            <button @click="showCouponModal = false" class="px-6 py-2 border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50 transition">İptal</button>
            <button @click="saveCoupon" class="px-6 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">Kaydet</button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

// Tab State
const activeTab = ref('campaigns')

// Campaigns
const campaigns = ref([
  { id: 1, name: 'Yılbaşı İndirimi', description: 'Tüm ürünlerde geçerli', scope: 'global', status: 'active', discount_type: 'percentage', discount_value: 30, min_amount: 100, max_discount: 500, start_date: '2025-12-20', end_date: '2026-01-05', usage_count: 1245, usage_limit: 5000, target_segment: null },
  { id: 2, name: 'VIP Müşteri Özel', description: 'Sadece VIP müşteriler için', scope: 'segment', status: 'active', discount_type: 'fixed', discount_value: 100, min_amount: 500, max_discount: null, start_date: '2025-12-01', end_date: '2025-12-31', usage_count: 89, usage_limit: 500, target_segment: 'VIP Müşteriler' },
  { id: 3, name: 'Elektronik Fırsatı', description: 'Elektronik kategorisinde', scope: 'category', status: 'scheduled', discount_type: 'percentage', discount_value: 25, min_amount: 200, max_discount: 300, start_date: '2026-01-10', end_date: '2026-01-20', usage_count: 0, usage_limit: 1000, target_segment: null },
  { id: 4, name: 'Kış İndirimi', description: 'Giyim ürünlerinde', scope: 'category', status: 'active', discount_type: 'percentage', discount_value: 40, min_amount: 150, max_discount: 400, start_date: '2025-12-15', end_date: '2026-02-28', usage_count: 567, usage_limit: 3000, target_segment: null },
])

const campaignSearch = ref('')
const campaignStatusFilter = ref('')
const campaignScopeFilter = ref('')

const filteredCampaigns = computed(() => {
  let result = campaigns.value

  if (campaignSearch.value) {
    result = result.filter(c => c.name.toLowerCase().includes(campaignSearch.value.toLowerCase()) || c.description.toLowerCase().includes(campaignSearch.value.toLowerCase()))
  }

  if (campaignStatusFilter.value) {
    result = result.filter(c => c.status === campaignStatusFilter.value)
  }

  if (campaignScopeFilter.value) {
    result = result.filter(c => c.scope === campaignScopeFilter.value)
  }

  return result
})

// Coupons
const coupons = ref([
  { id: 1, code: 'YILBASI2025', description: 'Yılbaşı özel kupon', discount_type: 'percentage', discount_value: 20, min_amount: 100, max_discount: 200, usage_count: 456, usage_limit: 1000, user_limit: 1, start_date: '2025-12-20', end_date: '2026-01-05', is_active: true, total_discount: 45600 },
  { id: 2, code: 'WELCOME50', description: 'Hoşgeldin indirimi', discount_type: 'fixed', discount_value: 50, min_amount: 200, max_discount: null, usage_count: 1234, usage_limit: null, user_limit: 1, start_date: '2025-01-01', end_date: '2025-12-31', is_active: true, total_discount: 61700 },
  { id: 3, code: 'FREESHIP', description: 'Ücretsiz kargo', discount_type: 'fixed', discount_value: 30, min_amount: 150, max_discount: null, usage_count: 789, usage_limit: 5000, user_limit: 2, start_date: '2025-11-01', end_date: '2026-03-31', is_active: true, total_discount: 23670 },
])

const couponSearch = ref('')

const filteredCoupons = computed(() => {
  if (!couponSearch.value) return coupons.value
  return coupons.value.filter(c => c.code.toLowerCase().includes(couponSearch.value.toLowerCase()))
})

// Segments
const segments = ref([
  { id: 1, name: 'VIP Müşteriler', description: 'Toplam 10.000 TL üzeri alışveriş yapan müşteriler', icon: '👑', member_count: 1245, active_campaigns: 3, rules: ['Toplam harcama > 10.000 TL', 'Son 6 ay içinde aktif'] },
  { id: 2, name: 'Yeni Müşteriler', description: 'Son 30 gün içinde kaydolan müşteriler', icon: '🌟', member_count: 3456, active_campaigns: 2, rules: ['Kayıt tarihi < 30 gün', 'İlk alışveriş yapılmamış'] },
  { id: 3, name: 'Aktif Alıcılar', description: 'Son 3 ay içinde en az 3 alışveriş yapan müşteriler', icon: '🔥', member_count: 2789, active_campaigns: 4, rules: ['Son 3 ayda ≥ 3 sipariş', 'Ortalama sepet > 500 TL'] },
])

// Conflicts
const conflicts = ref([
  {
    id: 1,
    type: 'date',
    campaign1: campaigns.value[0],
    campaign2: campaigns.value[3],
    overlap_start: '2025-12-20',
    overlap_end: '2026-01-05',
    affected_products: 45
  }
])

// Modals
const showCampaignModal = ref(false)
const showCouponModal = ref(false)
const showSegmentModal = ref(false)

// Forms
const editingCampaign = ref<any>(null)
const campaignForm = ref({
  name: '',
  description: '',
  scope: 'global',
  target_segment: '',
  discount_type: 'percentage',
  discount_value: 0,
  min_amount: 0,
  max_discount: 0,
  start_date: '',
  end_date: '',
  usage_limit: null
})

const couponForm = ref({
  code: '',
  description: '',
  discount_type: 'percentage',
  discount_value: 0,
  min_amount: 0,
  max_discount: 0,
  usage_limit: null,
  user_limit: 1,
  start_date: '',
  end_date: '',
  is_active: true
})

// Actions
const getScopeBadge = (scope: string) => {
  switch (scope) {
    case 'global': return 'bg-purple-100 text-purple-700'
    case 'segment': return 'bg-orange-100 text-orange-700'
    case 'category': return 'bg-blue-100 text-blue-700'
    default: return 'bg-slate-100 text-slate-700'
  }
}

const getScopeLabel = (scope: string) => {
  switch (scope) {
    case 'global': return 'Global'
    case 'segment': return 'Segment'
    case 'category': return 'Kategori'
    default: return scope
  }
}

const getStatusBadge = (status: string) => {
  switch (status) {
    case 'active': return 'bg-green-100 text-green-700'
    case 'scheduled': return 'bg-blue-100 text-blue-700'
    case 'paused': return 'bg-yellow-100 text-yellow-700'
    case 'ended': return 'bg-slate-100 text-slate-700'
    default: return 'bg-slate-100 text-slate-700'
  }
}

const getStatusLabel = (status: string) => {
  switch (status) {
    case 'active': return 'Aktif'
    case 'scheduled': return 'Zamanlanmış'
    case 'paused': return 'Duraklatılmış'
    case 'ended': return 'Sona Ermiş'
    default: return status
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', { year: 'numeric', month: 'short', day: 'numeric' })
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY', maximumFractionDigits: 0 }).format(value)
}

const checkConflicts = () => {
  toast.info('Çakışma kontrolü yapılıyor...')
  setTimeout(() => {
    toast.success(`${conflicts.value.length} çakışma bulundu`)
    activeTab.value = 'conflicts'
  }, 500)
}

const viewCampaignDetails = (campaign: any) => {
  toast.info(`${campaign.name} detayları`)
}

const editCampaign = (campaign: any) => {
  editingCampaign.value = campaign
  campaignForm.value = { ...campaign }
  showCampaignModal.value = true
}

const pauseCampaign = (campaign: any) => {
  campaign.status = 'paused'
  toast.success('Kampanya duraklatıldı')
}

const deleteCampaign = (campaign: any) => {
  if (confirm(`"${campaign.name}" kampanyasını silmek istediğinizden emin misiniz?`)) {
    const index = campaigns.value.findIndex(c => c.id === campaign.id)
    if (index > -1) {
      campaigns.value.splice(index, 1)
      toast.success('Kampanya silindi')
    }
  }
}

const saveCampaign = () => {
  if (!campaignForm.value.name || !campaignForm.value.start_date || !campaignForm.value.end_date) {
    toast.error('Tüm zorunlu alanları doldurun')
    return
  }

  if (editingCampaign.value) {
    const index = campaigns.value.findIndex(c => c.id === editingCampaign.value.id)
    if (index > -1) {
      campaigns.value[index] = { ...editingCampaign.value, ...campaignForm.value }
      toast.success('Kampanya güncellendi')
    }
  } else {
    const newCampaign = {
      id: campaigns.value.length + 1,
      ...campaignForm.value,
      status: new Date(campaignForm.value.start_date) > new Date() ? 'scheduled' : 'active',
      usage_count: 0
    }
    campaigns.value.push(newCampaign)
    toast.success('Kampanya oluşturuldu')
  }

  showCampaignModal.value = false
  editingCampaign.value = null
  campaignForm.value = { name: '', description: '', scope: 'global', target_segment: '', discount_type: 'percentage', discount_value: 0, min_amount: 0, max_discount: 0, start_date: '', end_date: '', usage_limit: null }
}

const generateCouponCode = () => {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
  let code = ''
  for (let i = 0; i < 8; i++) {
    code += chars.charAt(Math.floor(Math.random() * chars.length))
  }
  couponForm.value.code = code
}

const copyCouponCode = (code: string) => {
  navigator.clipboard.writeText(code)
  toast.success(`Kupon kodu kopyalandı: ${code}`)
}

const editCoupon = (coupon: any) => {
  couponForm.value = { ...coupon }
  showCouponModal.value = true
}

const deleteCoupon = (coupon: any) => {
  if (confirm(`"${coupon.code}" kuponunu silmek istediğinizden emin misiniz?`)) {
    const index = coupons.value.findIndex(c => c.id === coupon.id)
    if (index > -1) {
      coupons.value.splice(index, 1)
      toast.success('Kupon silindi')
    }
  }
}

const saveCoupon = () => {
  if (!couponForm.value.code) {
    toast.error('Kupon kodu gerekli')
    return
  }

  const newCoupon = {
    id: coupons.value.length + 1,
    ...couponForm.value,
    usage_count: 0,
    total_discount: 0
  }
  coupons.value.push(newCoupon)
  toast.success('Kupon oluşturuldu')
  showCouponModal.value = false
  couponForm.value = { code: '', description: '', discount_type: 'percentage', discount_value: 0, min_amount: 0, max_discount: 0, usage_limit: null, user_limit: 1, start_date: '', end_date: '', is_active: true }
}

const bulkGenerateCoupons = () => {
  toast.info('Toplu kupon üretimi özelliği yakında')
}

const editSegment = (segment: any) => {
  toast.info('Segment düzenleme özelliği yakında')
}

const deleteSegment = (segment: any) => {
  toast.warning('Aktif kampanyası olan segment silinemez')
}

const resolveConflict = (conflict: any, action: string) => {
  toast.success(`Çakışma ${action} ile çözüldü`)
  const index = conflicts.value.findIndex(c => c.id === conflict.id)
  if (index > -1) {
    conflicts.value.splice(index, 1)
  }
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
