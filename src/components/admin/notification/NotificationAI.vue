<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
  notificationId: number
  type: string
  subject: string
  content: string
  status: string
}>()

const urgencyScore = ref(0)
const sentiment = ref('neutral')
const smartActions = ref<any[]>([])

const analyzeNotification = () => {
  // Mock AI Logic
  if (props.status === 'failed') {
    urgencyScore.value = 85
    sentiment.value = 'negative'
    smartActions.value = [
      { label: 'Tekrar Dene (Öncelikli)', action: 'retry_priority', icon: '🚀' },
      { label: 'Logları İncele', action: 'check_logs', icon: '📝' }
    ]
  } else if (props.type === 'security') {
    urgencyScore.value = 95
    sentiment.value = 'critical'
    smartActions.value = [
      { label: 'Kullanıcıyı Engelle', action: 'block_user', icon: '🚫' },
      { label: 'Şifre Sıfırlama Gönder', action: 'reset_pass', icon: '🔒' }
    ]
  } else {
    urgencyScore.value = 20
    sentiment.value = 'neutral'
    smartActions.value = [
      { label: 'Arşive Kaldır', action: 'archive', icon: '📦' }
    ]
  }
}

watch(() => props.notificationId, () => {
  analyzeNotification()
}, { immediate: true })

const getScoreColor = (score: number) => {
  if (score >= 80) return 'text-red-600'
  if (score >= 50) return 'text-orange-600'
  return 'text-emerald-600'
}
</script>

<template>
  <div class="space-y-4">
    <!-- Urgency Score -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
      <div>
        <p class="text-[10px] text-slate-500 uppercase font-bold mb-1">Aciliyet Skoru</p>
        <div class="text-3xl font-black" :class="getScoreColor(urgencyScore)">{{ urgencyScore }}/100</div>
      </div>
      <div class="text-right">
        <div class="text-xs font-bold text-slate-700">AI Önceliği</div>
        <div class="text-[10px] font-bold uppercase" :class="getScoreColor(urgencyScore)">
          {{ urgencyScore > 80 ? 'Kritik' : (urgencyScore > 50 ? 'Yüksek' : 'Normal') }}
        </div>
      </div>
    </div>

    <!-- Smart Summary -->
    <div class="bg-slate-800 rounded-xl p-4 text-white shadow-lg">
      <h4 class="font-bold text-sm mb-2 flex items-center gap-2">
        <span>🧠</span> AI Özeti
      </h4>
      <p class="text-xs text-slate-300 leading-relaxed">
        Bu bildirim <span class="text-white font-bold">{{ type }}</span> kategorisinde değerlendirildi. 
        <span v-if="status === 'failed'">Gönderim başarısız olduğu için müdahale gerekiyor.</span>
        <span v-else>Rutin bir bilgilendirme işlemi.</span>
      </p>
    </div>

    <!-- Smart Actions -->
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
      <h4 class="text-indigo-900 font-bold text-sm mb-3 flex items-center gap-2">
        <span>⚡</span> Önerilen Aksiyonlar
      </h4>
      <div class="space-y-2">
        <button 
          v-for="(action, idx) in smartActions" 
          :key="idx"
          class="w-full flex items-center gap-3 bg-white p-2.5 rounded-lg border border-indigo-100 hover:border-indigo-300 hover:shadow-sm transition text-left group"
        >
          <span class="text-lg">{{ action.icon }}</span>
          <span class="text-xs font-bold text-slate-700 group-hover:text-indigo-700">{{ action.label }}</span>
        </button>
      </div>
    </div>
  </div>
</template>
