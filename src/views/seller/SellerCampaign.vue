<template>
  <div class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-3xl mx-auto">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Kampanya Oluştur</h1>
        <p class="text-slate-500">Başlangıç tarihinde otomatik yayınlanır. Zorunlu alanlar için akıllı varsayılanlar uygulanır.</p>
      </div>

      <form @submit.prevent="submit" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Kampanya Adı</label>
          <input v-model="form.name" type="text" class="w-full border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-green-500" placeholder="Örn: Hafta Sonu Fırsatı" required />
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Başlangıç Tarihi</label>
            <input v-model="form.start_date" type="datetime-local" class="w-full border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-green-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Bitiş Tarihi (opsiyonel)</label>
            <input v-model="form.end_date" type="datetime-local" class="w-full border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-green-500" />
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">İndirim Tipi</label>
            <select v-model="form.discount_type" class="w-full border border-slate-200 rounded-xl px-3 py-2">
              <option value="percentage">Yüzde</option>
              <option value="fixed">Sabit Tutar</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">İndirim Değeri</label>
            <input v-model.number="form.discount_value" type="number" min="1" class="w-full border border-slate-200 rounded-xl px-3 py-2" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Kapsam</label>
          <select v-model="form.scope" class="w-full border border-slate-200 rounded-xl px-3 py-2">
            <option value="products">Ürünler</option>
            <option value="categories">Kategoriler</option>
            <option value="segments">Segmentler</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Açıklama (opsiyonel)</label>
          <textarea v-model="form.description" rows="3" class="w-full border border-slate-200 rounded-xl px-3 py-2" placeholder="Kampanya kural ve hedefini özetleyin"></textarea>
        </div>

        <div class="flex items-center justify-between pt-2">
          <div class="text-sm text-slate-500">Otomatik yayın: {{ autoPublishEnabled ? 'Açık' : 'Kapalı' }}</div>
          <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700">Kaydet</button>
        </div>
      </form>
    </div>
  </div>
  
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { isFeatureEnabled } from '@/utils/featureToggle'

type DiscountType = 'percentage' | 'fixed'
type ScopeType = 'products' | 'categories' | 'segments'

const autoPublishEnabled = isFeatureEnabled('campaign.autoPublish')

const form = ref({
  name: '',
  start_date: '',
  end_date: '',
  discount_type: 'percentage' as DiscountType,
  discount_value: 10,
  scope: 'products' as ScopeType,
  description: '',
})

onMounted(() => {
  // Akıllı varsayılan: bir sonraki saat
  const now = new Date()
  now.setMinutes(0, 0, 0)
  now.setHours(now.getHours() + 1)
  form.value.start_date = toLocalInput(now)
  form.value.name = defaultName()
})

function toLocalInput(d: Date) {
  return new Date(d.getTime() - d.getTimezoneOffset() * 60000).toISOString().slice(0,16)
}

function toIsoString(dateStr: string) {
  return dateStr ? new Date(dateStr).toISOString() : ''
}

function defaultName() {
  const date = new Date().toISOString().slice(0, 10)
  return `Hızlı Kampanya ${date}`
}

function normalizePayload() {
  const payload: any = { ...form.value }
  // Güvenli varsayılanlar
  if (!payload.name) payload.name = defaultName()
  if (!payload.start_date) {
    const d = new Date(); d.setHours(d.getHours() + 1,0,0,0)
    payload.start_date = new Date(d.getTime() - d.getTimezoneOffset() * 60000).toISOString()
  } else {
    payload.start_date = toIsoString(payload.start_date)
  }
  if (!payload.discount_type) payload.discount_type = 'percentage'
  if (!payload.discount_value || payload.discount_value <= 0) payload.discount_value = 10
  if (!payload.scope) payload.scope = 'products'
  if (!payload.end_date) {
    const end = new Date(payload.start_date)
    end.setDate(end.getDate() + 7)
    payload.end_date = end.toISOString()
  } else {
    payload.end_date = toIsoString(payload.end_date)
  }
  if (new Date(payload.end_date) < new Date(payload.start_date)) {
    const adjusted = new Date(payload.start_date)
    adjusted.setDate(adjusted.getDate() + 1)
    payload.end_date = adjusted.toISOString()
  }
  payload.auto_publish = autoPublishEnabled
  payload.status = autoPublishEnabled ? 'scheduled' : 'draft'
  payload.publish_at = payload.start_date
  payload.description = payload.description || 'Otomatik oluşturulan kampanya'
  return payload
}

async function submit() {
  const body = normalizePayload()
  try {
    // Demo: gerçek API bağlanmadıysa konsola yaz
    console.log('Kampanya gönderiliyor:', body)
    alert('Kampanya kaydedildi (mock). Eksik alanlar varsayılanlarla tamamlandı.')
  } catch (e) {
    alert('Kaydetme başarısız. Lütfen alanları kontrol edin.')
  }
}
</script>
<style scoped>
</style>