<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
            🧪 E2E Test Automation Dashboard
          </h1>
          <p class="text-slate-600 mt-1">End-to-End akış senaryoları ve otomatik test yönetimi</p>
        </div>
        <div class="flex gap-3">
          <button
            @click="runAllTests"
            :disabled="isRunningAll"
            class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 shadow-lg"
          >
            <span v-if="isRunningAll">⏳</span>
            <span v-else>▶️</span>
            {{ isRunningAll ? 'Testler Çalışıyor...' : 'Tüm Testleri Çalıştır' }}
          </button>
          <button
            @click="resetAllTests"
            class="px-6 py-3 bg-slate-600 text-white rounded-lg hover:bg-slate-700 flex items-center gap-2 shadow-lg"
          >
            🔄 Sıfırla
          </button>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-7 gap-4 mt-6">
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
          <div class="text-2xl font-bold text-blue-600">{{ stats.total }}</div>
          <div class="text-sm text-slate-600">Toplam Test</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-green-500">
          <div class="text-2xl font-bold text-green-600">{{ stats.passed }}</div>
          <div class="text-sm text-slate-600">✅ Başarılı</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-red-500">
          <div class="text-2xl font-bold text-red-600">{{ stats.failed }}</div>
          <div class="text-sm text-slate-600">❌ Başarısız</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-yellow-500">
          <div class="text-2xl font-bold text-yellow-600">{{ stats.running }}</div>
          <div class="text-sm text-slate-600">⏳ Çalışıyor</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-slate-400">
          <div class="text-2xl font-bold text-slate-600">{{ stats.pending }}</div>
          <div class="text-sm text-slate-600">⏸️ Bekliyor</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-purple-500">
          <div class="text-2xl font-bold text-purple-600">{{ successRate }}%</div>
          <div class="text-sm text-slate-600">Başarı Oranı</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-amber-500">
          <div class="text-2xl font-bold text-amber-600">{{ totalRetries }}</div>
          <div class="text-sm text-slate-600">🔄 Toplam Retry</div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-12 gap-6">
      <!-- Left Panel - Test Scenarios -->
      <div class="col-span-3">
        <div class="bg-white rounded-lg shadow-md p-5">
          <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            📋 Test Senaryoları
          </h2>

          <div class="space-y-3">
            <div
              v-for="scenario in filteredScenarios"
              :key="scenario.id"
              @click="selectScenario(scenario.id)"
              :class="[
                'p-4 rounded-lg cursor-pointer transition-all border-2',
                selectedScenario === scenario.id
                  ? 'bg-orange-50 border-orange-500'
                  : 'bg-slate-50 border-slate-200 hover:border-orange-300'
              ]"
            >
              <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                  <input
                    type="checkbox"
                    v-model="scenario.enabled"
                    @click.stop
                    class="w-4 h-4 text-orange-500 rounded"
                  />
                  <span class="font-semibold text-slate-800 text-sm">{{ scenario.name }}</span>
                </div>
                <span
                  :class="[
                    'text-xs px-2 py-1 rounded-full font-medium',
                    getScenarioStatusClass(scenario)
                  ]"
                >
                  {{ getScenarioStatus(scenario) }}
                </span>
              </div>
              <p class="text-xs text-slate-600 ml-6 mb-2">{{ scenario.description }}</p>
              <div class="ml-6">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                  <span>{{ scenario.steps.filter(s => s.status === 'passed').length }}/{{ scenario.steps.length }} adım</span>
                  <div class="flex-1 bg-slate-200 rounded-full h-1.5">
                    <div
                      class="bg-green-500 h-1.5 rounded-full transition-all"
                      :style="{ width: (scenario.steps.filter(s => s.status === 'passed').length / scenario.steps.length * 100) + '%' }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Scenario Category Filter -->
          <div class="mt-6 pt-4 border-t border-slate-200">
            <h3 class="text-sm font-bold text-slate-700 mb-3">📂 Kategori Filtresi</h3>
            <div class="space-y-2">
              <button
                v-for="category in scenarioCategories"
                :key="category.id"
                @click="filterByCategory(category.id)"
                :class="[
                  'w-full text-left px-3 py-2 rounded-lg text-sm transition-all',
                  selectedCategory === category.id 
                    ? 'bg-orange-500 text-white' 
                    : 'bg-slate-50 text-slate-700 hover:bg-slate-100'
                ]"
              >
                <span>{{ category.icon }}</span>
                <span class="ml-2">{{ category.name }}</span>
                <span class="ml-auto float-right text-xs opacity-70">{{ category.count }}</span>
              </button>
            </div>
          </div>

          <!-- Feature Toggles -->
          <div class="mt-6 pt-4 border-t border-slate-200">
            <h3 class="text-sm font-bold text-slate-700 mb-3">⚙️ Feature Toggles</h3>
            <div class="space-y-2">
              <div
                v-for="toggle in featureToggles"
                :key="toggle.id"
                class="flex items-center justify-between p-2 rounded bg-slate-50"
              >
                <span class="text-sm text-slate-700">{{ toggle.name }}</span>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input
                    type="checkbox"
                    v-model="toggle.enabled"
                    class="sr-only peer"
                  />
                  <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Center Panel - Test Steps -->
      <div class="col-span-5">
        <div class="bg-white rounded-lg shadow-md p-5">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
              📝 Test Adımları
              <span v-if="currentScenario" class="text-sm font-normal text-slate-600">
                - {{ currentScenario.name }}
              </span>
            </h2>
            <button
              v-if="currentScenario"
              @click="runScenario(currentScenario.id)"
              :disabled="!currentScenario.enabled || isRunningScenario(currentScenario.id)"
              class="px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <span v-if="isRunningScenario(currentScenario.id)">⏳</span>
              <span v-else>▶️</span>
              Senaryoyu Çalıştır
            </button>
          </div>

          <div v-if="!currentScenario" class="text-center py-12 text-slate-400">
            ← Soldan bir senaryo seçin
          </div>

          <div v-else class="space-y-3 max-h-[calc(100vh-320px)] overflow-y-auto">
            <div
              v-for="(step, index) in currentScenario.steps"
              :key="step.id"
              :class="[
                'border-2 rounded-lg p-4 transition-all',
                step.status === 'passed' ? 'border-green-500 bg-green-50' :
                step.status === 'failed' ? 'border-red-500 bg-red-50' :
                step.status === 'running' ? 'border-yellow-500 bg-yellow-50 animate-pulse' :
                step.locked ? 'border-slate-300 bg-slate-100 opacity-60' :
                'border-slate-300 bg-white hover:border-orange-300'
              ]"
            >
              <div class="flex items-start justify-between">
                <div class="flex items-start gap-3 flex-1">
                  <div class="flex items-center gap-2 mt-1">
                    <span class="text-slate-400 font-medium text-sm">#{index + 1}</span>
                    <input
                      type="checkbox"
                      :checked="step.status === 'passed'"
                      :disabled="step.locked"
                      class="w-5 h-5 text-green-500 rounded"
                      readonly
                    />
                  </div>
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                      <h3 class="font-semibold text-slate-800">{{ step.action }}</h3>
                      <span
                        v-if="step.locked"
                        class="text-xs px-2 py-0.5 bg-slate-200 text-slate-600 rounded-full"
                      >
                        🔒 Kilitli
                      </span>
                    </div>
                    <p class="text-sm text-slate-600 mb-2">
                      <strong>Beklenen:</strong> {{ step.expected }}
                    </p>
                    <p class="text-xs text-slate-500">
                      <strong>Doğrulama:</strong> {{ step.validation }}
                    </p>

                    <!-- Error Message -->
                    <div v-if="step.status === 'failed' && step.error" class="mt-2 p-2 bg-red-100 border border-red-300 rounded text-xs text-red-700">
                      <strong>❌ Hata:</strong> {{ step.error }}
                    </div>

                    <!-- Latency Info -->
                    <div v-if="step.latency" class="mt-2 flex items-center gap-4 text-xs text-slate-500">
                      <span>⏱️ Süre: {{ step.latency }}ms</span>
                      <span v-if="step.retries">🔄 Retry: {{ step.retries }}</span>
                    </div>
                  </div>
                </div>

                <!-- Status Badge and Actions -->
                <div class="flex items-center gap-2">
                  <span
                    :class="[
                      'text-xs px-3 py-1 rounded-full font-medium',
                      step.status === 'passed' ? 'bg-green-500 text-white' :
                      step.status === 'failed' ? 'bg-red-500 text-white' :
                      step.status === 'running' ? 'bg-yellow-500 text-white' :
                      'bg-slate-300 text-slate-700'
                    ]"
                  >
                    {{ getStatusText(step.status) }}
                  </span>
                  <button
                    @click="runStep(currentScenario.id, step.id)"
                    :disabled="step.locked || step.status === 'running'"
                    class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    {{ step.status === 'running' ? '⏳' : '▶️' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel - Log Monitor -->
      <div class="col-span-4">
        <div class="bg-white rounded-lg shadow-md p-5">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
              📊 Test Logları
            </h2>
            <button
              @click="clearLogs"
              class="text-xs px-3 py-1 bg-slate-200 text-slate-700 rounded hover:bg-slate-300"
            >
              🗑️ Temizle
            </button>
          </div>

          <!-- Log Filters -->
          <div class="flex gap-2 mb-3">
            <button
              v-for="filter in logFilters"
              :key="filter"
              @click="selectedLogFilter = filter"
              :class="[
                'px-3 py-1 text-xs rounded-lg transition-all',
                selectedLogFilter === filter
                  ? 'bg-orange-500 text-white'
                  : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
              ]"
            >
              {{ filter }}
            </button>
          </div>

          <!-- Logs -->
          <div class="bg-slate-900 rounded-lg p-4 h-[calc(100vh-380px)] overflow-y-auto font-mono text-xs">
            <div
              v-for="(log, index) in filteredLogs"
              :key="index"
              :class="[
                'mb-2 pb-2 border-b border-slate-700',
                log.type === 'error' ? 'text-red-400' :
                log.type === 'success' ? 'text-green-400' :
                log.type === 'warning' ? 'text-yellow-400' :
                log.type === 'api' ? 'text-blue-400' :
                'text-slate-300'
              ]"
            >
              <div class="flex items-start gap-2">
                <span class="text-slate-500">{{ log.timestamp }}</span>
                <span
                  :class="[
                    'px-2 py-0.5 rounded text-[10px] font-bold',
                    log.type === 'error' ? 'bg-red-900 text-red-200' :
                    log.type === 'success' ? 'bg-green-900 text-green-200' :
                    log.type === 'warning' ? 'bg-yellow-900 text-yellow-200' :
                    log.type === 'api' ? 'bg-blue-900 text-blue-200' :
                    'bg-slate-700 text-slate-300'
                  ]"
                >
                  {{ log.type.toUpperCase() }}
                </span>
                <span class="flex-1">{{ log.message }}</span>
              </div>

              <!-- API Details -->
              <div v-if="log.details" class="mt-1 ml-24 text-slate-400 text-[11px]">
                <div v-if="log.details.endpoint">📍 {{ log.details.endpoint }}</div>
                <div v-if="log.details.latency">⏱️ Latency: {{ log.details.latency }}ms</div>
                <div v-if="log.details.status">
                  <span :class="log.details.status >= 200 && log.details.status < 300 ? 'text-green-400' : 'text-red-400'">
                    Status: {{ log.details.status }}
                  </span>
                </div>
                <div v-if="log.details.retries">🔄 Retries: {{ log.details.retries }}</div>
                <div v-if="log.details.error" class="text-red-400">❌ {{ log.details.error }}</div>
              </div>
            </div>

            <div v-if="filteredLogs.length === 0" class="text-slate-500 text-center py-8">
              Henüz log kaydı yok
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

type TestStatus = 'pending' | 'running' | 'passed' | 'failed'
type LogType = 'info' | 'success' | 'error' | 'warning' | 'api'

interface TestStep {
  id: string
  action: string
  expected: string
  validation: string
  status: TestStatus
  locked: boolean
  error?: string
  latency?: number
  retries?: number
}

interface TestScenario {
  id: string
  name: string
  description: string
  enabled: boolean
  steps: TestStep[]
}

interface LogEntry {
  timestamp: string
  type: LogType
  message: string
  details?: {
    endpoint?: string
    latency?: number
    status?: number
    retries?: number
    error?: string
  }
}

interface FeatureToggle {
  id: string
  name: string
  enabled: boolean
}

interface ScenarioCategory {
  id: string
  name: string
  icon: string
  count: number
}

// Selected Scenario
const selectedScenario = ref<string>('scenario-1')
const selectedCategory = ref<string>('all')

// Feature Toggles
const featureToggles = ref<FeatureToggle[]>([
  { id: 'auth', name: 'Authentication', enabled: true },
  { id: 'payment', name: 'Payment Gateway', enabled: true },
  { id: 'shipping', name: 'Shipping Module', enabled: true },
  { id: 'notifications', name: 'Notifications', enabled: true },
  { id: 'analytics', name: 'Analytics', enabled: false }
])

// Test Scenarios
const scenarios = ref<TestScenario[]>([
  {
    id: 'scenario-1',
    name: 'Kullanıcı Sipariş Akışı',
    description: 'Kayıt → Arama → Sepet → Checkout → Teslim',
    enabled: true,
    steps: [
      {
        id: 's1-step1',
        action: 'Kullanıcı Kaydı',
        expected: 'Kayıt formu açılır, email/şifre validasyonu yapılır, hesap oluşturulur',
        validation: 'Kullanıcı DB\'ye eklenir, hoşgeldin emaili gönderilir',
        status: 'pending',
        locked: false
      },
      {
        id: 's1-step2',
        action: 'Ürün Arama ve Filtreleme',
        expected: 'Arama sonuçları görünür, filtreler çalışır, sıralama uygulanır',
        validation: 'Elasticsearch sorgusu doğru sonuç döner, cache güncellenir',
        status: 'pending',
        locked: true
      },
      {
        id: 's1-step3',
        action: 'Sepete Ekleme',
        expected: 'Ürün sepete eklenir, miktar güncellenir, toplam hesaplanır',
        validation: 'Sepet DB\'de kaydedilir, stok kontrol edilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's1-step4',
        action: 'Kupon Uygulama',
        expected: 'Kupon kodu girilir, indirim hesaplanır, toplam güncellenir',
        validation: 'Kupon geçerliliği kontrol edilir, kullanım limiti kontrol edilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's1-step5',
        action: 'Checkout ve Ödeme',
        expected: 'Adres seçilir, ödeme yöntemi seçilir, sipariş oluşturulur',
        validation: 'Payment gateway çağrılır, sipariş DB\'ye kaydedilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's1-step6',
        action: 'Satıcı Sipariş Hazırlama',
        expected: 'Satıcı panelinde sipariş görünür, hazırlama başlatılır',
        validation: 'Sipariş durumu "Hazırlanıyor" olur, satıcıya bildirim gider',
        status: 'pending',
        locked: true
      },
      {
        id: 's1-step7',
        action: 'Kargoya Verme',
        expected: 'Kargo kodu girilir, takip numarası oluşturulur',
        validation: 'Müşteriye kargo bildirimi gönderilir, durum "Kargoda" olur',
        status: 'pending',
        locked: true
      },
      {
        id: 's1-step8',
        action: 'Kullanıcı Teslim Onayı',
        expected: 'Teslim alındı butonuna tıklanır, sipariş tamamlanır',
        validation: 'Sipariş durumu "Tamamlandı", satıcıya ödeme transfer edilir',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-2',
    name: 'İade Süreci',
    description: 'İade talebi → Onay → Ödeme iadesi → Stok güncelleme',
    enabled: true,
    steps: [
      {
        id: 's2-step1',
        action: 'Kullanıcı İade Talebi Oluşturma',
        expected: 'İade formu açılır, neden seçilir, fotoğraf yüklenir',
        validation: 'İade talebi DB\'ye kaydedilir, satıcıya bildirim gönderilir',
        status: 'pending',
        locked: false
      },
      {
        id: 's2-step2',
        action: 'Satıcı İade Onayı',
        expected: 'Satıcı panelinde talep görünür, onay/red butonları aktif',
        validation: 'Satıcı onaylar, iade durumu "Onaylandı" olur',
        status: 'pending',
        locked: true
      },
      {
        id: 's2-step3',
        action: 'Ürün İade Kargoya Verilme',
        expected: 'Kullanıcı ürünü kargoya verir, kargo takip kodu girilir',
        validation: 'İade kargo takip numarası kaydedilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's2-step4',
        action: 'Satıcı Ürün Teslim Alır',
        expected: 'Satıcı ürünü kontrol eder, teslim alır',
        validation: 'İade durumu "Ürün Teslim Alındı" olur',
        status: 'pending',
        locked: true
      },
      {
        id: 's2-step5',
        action: 'Ödeme İadesi',
        expected: 'Ödeme otomatik iade edilir, hesaba geri döner',
        validation: 'Payment gateway iade API\'si çağrılır, bakiye güncellenir',
        status: 'pending',
        locked: true
      },
      {
        id: 's2-step6',
        action: 'Stok Geri Ekleme',
        expected: 'İade edilen ürün stoka eklenir, ürün yeniden satışa çıkar',
        validation: 'Stok miktarı +1 artar, ürün durumu "Satışta" olur',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-3',
    name: 'Kampanya Yönetimi ve Etki Analizi',
    description: 'Kampanya oluştur → Kural kontrolü → Yayın → Satış raporu',
    enabled: true,
    steps: [
      {
        id: 's3-step1',
        action: 'Satıcı Kampanya Oluşturma',
        expected: 'Kampanya formu doldurulur, indirim oranı/tutarı girilir',
        validation: 'Kampanya DB\'ye kaydedilir, durum "Beklemede" olur',
        status: 'pending',
        locked: false
      },
      {
        id: 's3-step2',
        action: 'Admin Kural Kontrolü',
        expected: 'Admin panelinde kampanya görünür, kurallar otomatik kontrol edilir',
        validation: 'Tarih çakışması, kapsam çakışması, bütçe limiti kontrol edilir. Token yenileme ile retry yapılır.',
        status: 'pending',
        locked: true
      },
      {
        id: 's3-step3',
        action: 'Admin Onayı',
        expected: 'Admin kampanyayı onaylar veya reddeder',
        validation: 'Durum "Onaylandı" olur, satıcıya bildirim gönderilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's3-step4',
        action: 'Kampanya Yayını',
        expected: 'Kampanya başlangıç tarihinde otomatik yayınlanır',
        validation: 'Ürünlere indirim uygulanır, site genelinde görünür',
        status: 'pending',
        locked: true
      },
      {
        id: 's3-step5',
        action: 'Müşteri Kampanya Kullanımı',
        expected: 'Müşteri kampanyalı ürün satın alır, indirim uygulanır',
        validation: 'Sipariş\'te kampanya kaydedilir, kullanım sayacı artar',
        status: 'pending',
        locked: true
      },
      {
        id: 's3-step6',
        action: 'Satış Raporu ve Analiz',
        expected: 'Kampanya performans raporu oluşturulur',
        validation: 'Satış sayısı, toplam indirim, ROI hesaplanır',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-4',
    name: '🏨 Otel Rezervasyon Modülü',
    description: 'Tarih seçimi → Filtreleme → Oda seçimi → Ödeme → Rezervasyon',
    enabled: true,
    steps: [
      {
        id: 's4-step1',
        action: 'Tarih ve Konum Seçimi',
        expected: 'Date picker açılır, check-in/out tarihleri seçilir, konum girilir',
        validation: 'Tarih validasyonu yapılır (check-out > check-in), konum DB\'de aranır',
        status: 'pending',
        locked: false
      },
      {
        id: 's4-step2',
        action: 'Otel Filtreleme',
        expected: 'Otel listesi görünür, fiyat/yıldız/tesis filtreleri uygulanır',
        validation: 'Elasticsearch sorgusu çalışır, sonuçlar cache\'lenir',
        status: 'pending',
        locked: true
      },
      {
        id: 's4-step3',
        action: 'Otel Detay ve Oda Seçimi',
        expected: 'Otel detayları yüklenir, oda tipleri ve fiyatları görünür',
        validation: 'Müsaitlik kontrolü yapılır, fiyat hesaplanır (gece × fiyat)',
        status: 'pending',
        locked: true
      },
      {
        id: 's4-step4',
        action: 'Oda Varyantı Seçimi',
        expected: 'Kahvaltı dahil/hariç, iptal koşulları seçilir',
        validation: 'Varyant fiyat farkı eklenir, toplam tutar güncellenir',
        status: 'pending',
        locked: true
      },
      {
        id: 's4-step5',
        action: 'Ödeme İşlemi',
        expected: 'Ödeme sayfası açılır, kredi kartı/havale seçilir, ödeme tamamlanır',
        validation: 'Payment gateway API çağrılır, 3D Secure doğrulaması yapılır',
        status: 'pending',
        locked: true
      },
      {
        id: 's4-step6',
        action: 'Rezervasyon Onayı',
        expected: 'Rezervasyon oluşturulur, onay emaili/SMS gönderilir',
        validation: 'Rezervasyon DB\'ye kaydedilir, otele bildirim gider, QR kod üretilir',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-5',
    name: '🎁 Kampanya ve Sadakat Programı',
    description: 'Kampanyaya katılım → Puan kazanma → Kupon → Kullanım',
    enabled: true,
    steps: [
      {
        id: 's5-step1',
        action: 'Kampanyaya Katılım',
        expected: 'Kullanıcı kampanya sayfasında "Katıl" butonuna tıklar',
        validation: 'Katılım kaydedilir, başlangıç puanı verilir (örn: 100 puan)',
        status: 'pending',
        locked: false
      },
      {
        id: 's5-step2',
        action: 'Alışveriş ve Puan Kazanma',
        expected: 'Kullanıcı 500 TL alışveriş yapar, %10 puan kazanır (50 puan)',
        validation: 'Puan hesaplama: sipariş_tutarı × puan_oranı, kullanıcı bakiyesi güncellenir',
        status: 'pending',
        locked: true
      },
      {
        id: 's5-step3',
        action: 'Puan ile Kupon Üretimi',
        expected: 'Kullanıcı 150 puanı 30 TL kupona çevirir',
        validation: 'Puan bakiyesi düşer (150 puan), kupon oluşturulur (SADAKAT-XXX)',
        status: 'pending',
        locked: true
      },
      {
        id: 's5-step4',
        action: 'Kupon Geçerlilik Kontrolü',
        expected: 'Kupon kodu sepette girilir, indirim uygulanır',
        validation: 'Kupon geçerliliği kontrol edilir (süre, kullanım limiti, min tutar)',
        status: 'pending',
        locked: true
      },
      {
        id: 's5-step5',
        action: 'Alışverişte Kupon Kullanımı',
        expected: 'Sipariş tamamlanır, kupon indirimi uygulanır',
        validation: 'Kupon kullanım sayısı artar, toplam indirim kaydedilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's5-step6',
        action: 'Kampanya Süresi Kontrolü',
        expected: 'Kampanya bitiş tarihinde otomatik kapanır',
        validation: 'Cron job kampanya durumunu "Bitti" yapar, aktif kuponlar iptal edilir',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-6',
    name: '📦 Kargo Takip ve İade Süreci',
    description: 'Sipariş → Kargo → Teslim → İade → Ücret iadesi',
    enabled: true,
    steps: [
      {
        id: 's6-step1',
        action: 'Sipariş Kargoya Verilme',
        expected: 'Satıcı sipariş için kargo kodu girir, kargo API çağrılır',
        validation: 'Kargo takip numarası DB\'ye kaydedilir, müşteriye SMS gönderilir',
        status: 'pending',
        locked: false
      },
      {
        id: 's6-step2',
        action: 'Kargo Durumu Takibi',
        expected: 'Webhook ile kargo durumu güncellenir (Dağıtımda, Teslim Edildi)',
        validation: 'Kargo API webhook\'u alır, sipariş durumu real-time güncellenir',
        status: 'pending',
        locked: true
      },
      {
        id: 's6-step3',
        action: 'Teslimat Onayı',
        expected: 'Kullanıcı "Teslim Aldım" butonuna tıklar',
        validation: 'Sipariş durumu "Tamamlandı" olur, satıcıya ödeme aktarılır',
        status: 'pending',
        locked: true
      },
      {
        id: 's6-step4',
        action: 'İade Talebi Oluşturma',
        expected: 'Kullanıcı iade formu doldurur, neden seçer, fotoğraf yükler',
        validation: 'İade talebi kaydedilir, satıcıya email/bildirim gönderilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's6-step5',
        action: 'Satıcı İade Onayı',
        expected: 'Satıcı iade talebini onaylar, iade kargo bilgisi verilir',
        validation: 'İade durumu "Onaylandı" olur, kullanıcıya kargo adresi gönderilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's6-step6',
        action: 'Ücret İadesi İşlemi',
        expected: 'Satıcı ürünü teslim alır, ödeme iadesi tetiklenir',
        validation: 'Payment gateway iade API\'si çağrılır, bakiye güncellenir',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-7',
    name: '🧾 Fatura ve Muhasebe Modülü',
    description: 'Sipariş → Fatura oluşturma → PDF export → Muhasebe entegrasyonu',
    enabled: true,
    steps: [
      {
        id: 's7-step1',
        action: 'Sipariş Tamamlama ve Fatura Tetikleme',
        expected: 'Sipariş tamamlandığında otomatik fatura oluşturma başlar',
        validation: 'Sipariş event listener fatura service\'i tetikler',
        status: 'pending',
        locked: false
      },
      {
        id: 's7-step2',
        action: 'Fatura Şablonu Doldurma',
        expected: 'Fatura bilgileri şablona yerleştirilir (ürünler, tutar, KDV)',
        validation: 'Şablon render edilir, vergi hesaplaması yapılır (%18 KDV)',
        status: 'pending',
        locked: true
      },
      {
        id: 's7-step3',
        action: 'Vergi Hesaplama',
        expected: 'KDV, stopaj, ÖTV gibi vergiler otomatik hesaplanır',
        validation: 'Vergi kuralları uygulanır, kategori bazlı vergi oranları kullanılır',
        status: 'pending',
        locked: true
      },
      {
        id: 's7-step4',
        action: 'PDF Fatura Oluşturma',
        expected: 'Fatura PDF formatında dışa aktarılır',
        validation: 'PDF generator (wkhtmltopdf/Puppeteer) çalışır, dosya S3\'e yüklenir',
        status: 'pending',
        locked: true
      },
      {
        id: 's7-step5',
        action: 'E-Fatura/E-Arşiv Gönderimi',
        expected: 'Fatura GIB sistemine gönderilir (e-fatura entegrasyonu)',
        validation: 'E-fatura API çağrılır, UUID ve imza alınır',
        status: 'pending',
        locked: true
      },
      {
        id: 's7-step6',
        action: 'Muhasebe Yazılımı Entegrasyonu',
        expected: 'Fatura muhasebe sistemine (Logo, Netsis) aktarılır',
        validation: 'REST API ile fatura verisi gönderilir, cevap kaydedilir',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-8',
    name: '🧠 AI Öneri Motoru ve Monitoring',
    description: 'Kullanıcı davranışı → AI öneri → Tıklama → Dönüşüm → Dashboard',
    enabled: true,
    steps: [
      {
        id: 's8-step1',
        action: 'Kullanıcı Davranış Tracking',
        expected: 'Kullanıcı ürün görüntüler, event tracker kaydeder',
        validation: 'Analytics event gönderilir (product_view, category, timestamp)',
        status: 'pending',
        locked: false
      },
      {
        id: 's8-step2',
        action: 'AI Öneri Modeli Çalıştırma',
        expected: 'ML model kullanıcı geçmişine göre öneri ürünler üretir',
        validation: 'Collaborative filtering/Content-based model çalışır, 10 ürün önerilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's8-step3',
        action: 'Öneri Doğruluğu Testi',
        expected: 'Önerilen ürünler kullanıcı ilgi alanlarıyla uyumlu mu kontrol edilir',
        validation: 'Precision/Recall metrikleri hesaplanır, threshold: >%70',
        status: 'pending',
        locked: true
      },
      {
        id: 's8-step4',
        action: 'Kullanıcı Öneriye Tıklama (CTR)',
        expected: 'Kullanıcı önerilen ürüne tıklar, click-through rate hesaplanır',
        validation: 'CTR = (tıklama / gösterim) × 100, log\'a kaydedilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's8-step5',
        action: 'Dönüşüm Takibi',
        expected: 'Kullanıcı önerilen ürünü satın alır, conversion rate ölçülür',
        validation: 'Conversion = (satın alma / tıklama) × 100, dashboard\'a gönderilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's8-step6',
        action: 'Monitoring Dashboard Güncelleme',
        expected: 'Real-time dashboard metrikleri güncellenir, anomali tespiti yapılır',
        validation: 'Grafana/Kibana panel güncellenir, CTR < %5 ise alert gönderilir',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-9',
    name: '📋 Admin Moderasyon ve İçerik Yönetimi',
    description: 'Yorum → Admin onay → İhlal kontrolü → Bildirim',
    enabled: true,
    steps: [
      {
        id: 's9-step1',
        action: 'Kullanıcı Yorum/Soru Gönderimi',
        expected: 'Kullanıcı ürün yorumu yazar, gönderir',
        validation: 'Yorum DB\'ye "Beklemede" durumunda kaydedilir',
        status: 'pending',
        locked: false
      },
      {
        id: 's9-step2',
        action: 'Otomatik İçerik Filtreleme',
        expected: 'AI/regex ile küfür, spam, link kontrolü yapılır',
        validation: 'Moderation engine çalışır, ihlal varsa "Bayraklanan" durumuna alır',
        status: 'pending',
        locked: true
      },
      {
        id: 's9-step3',
        action: 'Admin Manuel İnceleme',
        expected: 'Admin moderasyon panelinde yorumu görür, onaylar/reddeder',
        validation: 'Admin aksiyonu kaydedilir, durum "Onaylandı" veya "Reddedildi" olur',
        status: 'pending',
        locked: true
      },
      {
        id: 's9-step4',
        action: 'İhbar Akışı Testi',
        expected: 'Kullanıcılar yorumu ihbar eder (3+ ihbar), admin bilgilendirilir',
        validation: 'İhbar sayacı artar, threshold aşılınca admin email/bildirim alır',
        status: 'pending',
        locked: true
      },
      {
        id: 's9-step5',
        action: 'İçerik Politikası Uygulanması',
        expected: 'İhlal eden içerik silinir, kullanıcıya uyarı verilir',
        validation: 'Yorum soft delete yapılır, kullanıcı uyarı sayacı +1 artar',
        status: 'pending',
        locked: true
      },
      {
        id: 's9-step6',
        action: 'Kullanıcıya Bildirim Gönderimi',
        expected: 'Yorum onay/red bildirimini kullanıcı alır (email/push)',
        validation: 'Notification service tetiklenir, email queue\'ya eklenir, gönderilir',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-10',
    name: '🧩 Feature Toggle ve Modül Aktivasyonu',
    description: 'Admin toggle açar → Erişim kontrolü → Monitoring',
    enabled: true,
    steps: [
      {
        id: 's10-step1',
        action: 'Admin Feature Toggle Açma',
        expected: 'Admin panel\'de "Yeni Ödeme Modülü" toggle\'ını ON yapar',
        validation: 'Feature flag DB\'ye kaydedilir, cache temizlenir',
        status: 'pending',
        locked: false
      },
      {
        id: 's10-step2',
        action: 'Seller Panel Görünürlük Kontrolü',
        expected: 'Satıcı panelinde yeni modül menüde görünür hale gelir',
        validation: 'Frontend feature flag check eder, UI render edilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's10-step3',
        action: 'Kullanıcı Erişim Testi',
        expected: 'Kullanıcı yeni ödeme modülüne erişir, işlem yapar',
        validation: 'Backend middleware feature check eder, erişim loglanır',
        status: 'pending',
        locked: true
      },
      {
        id: 's10-step4',
        action: 'Modül Kullanım Logları',
        expected: 'Her işlem için log kaydedilir (kullanıcı, zaman, aksiyon)',
        validation: 'Structured logging yapılır, ELK stack\'e gönderilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's10-step5',
        action: 'Dashboard Metrik Güncelleme',
        expected: 'Admin dashboard\'da modül kullanım istatistikleri görünür',
        validation: 'Real-time analytics güncellenir, kullanıcı sayısı, işlem sayısı gösterilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's10-step6',
        action: 'Feature Rollback Senaryosu',
        expected: 'Admin toggle\'ı OFF yapar, modül anında kapanır',
        validation: 'Cache invalidate edilir, frontend/backend erişim engellenir, graceful shutdown',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-11',
    name: '🏪 Satıcı Başvuru ve Onay Akışı',
    description: 'Başvuru → Doküman → Onay → Panel → Düzenleme',
    enabled: true,
    steps: [
      {
        id: 's11-step1',
        action: 'Satıcı Başvuru Formu Doldurma',
        expected: 'Satıcı adayı başvuru formunu doldurur (şirket bilgileri, vergi no, IBAN)',
        validation: 'Form validasyonu çalışır (TC/vergi no kontrolü), başvuru DB\'ye kaydedilir',
        status: 'pending',
        locked: false
      },
      {
        id: 's11-step2',
        action: 'Doküman Yükleme',
        expected: 'İmza sirküleri, vergi levhası, faaliyet belgesi PDF olarak yüklenir',
        validation: 'Dosya tipi/boyutu kontrolü yapılır, S3\'e upload edilir, virus scan çalışır',
        status: 'pending',
        locked: true
      },
      {
        id: 's11-step3',
        action: 'Otomatik Ön Kontrol',
        expected: 'Sistem vergi no/TC doğrulaması, black list kontrolü yapar',
        validation: 'GIB API çağrılır, kara liste DB\'de sorgulanır, risk skorlaması yapılır',
        status: 'pending',
        locked: true
      },
      {
        id: 's11-step4',
        action: 'Admin Manuel İnceleme',
        expected: 'Admin başvuruyu inceler, dokümanları kontrol eder, not ekler',
        validation: 'Admin notu kaydedilir, durum "İnceleniyor" olarak güncellenir',
        status: 'pending',
        locked: true
      },
      {
        id: 's11-step5',
        action: 'Onay/Red Kararı',
        expected: 'Admin başvuruyu onaylar veya reddeder, gerekçe yazar',
        validation: 'Durum "Onaylandı"/"Reddedildi" olur, satıcıya email/SMS gönderilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's11-step6',
        action: 'Satıcı Paneli Aktivasyonu',
        expected: 'Onaylanan satıcı için panel erişimi açılır, ilk giriş linki gönderilir',
        validation: 'User role "seller" olur, permissions atanır, welcome email gönderilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's11-step7',
        action: 'Satıcı Bilgilerini Düzenleme',
        expected: 'Satıcı panel\'den şirket bilgilerini, IBAN\'ı günceller',
        validation: 'Update işlemi loglanır, değişiklik onay sürecine girebilir (kritik alanlar için)',
        status: 'pending',
        locked: true
      },
      {
        id: 's11-step8',
        action: 'Satıcı Skorlama ve Sınıflandırma',
        expected: 'Sistem satıcıyı performansa göre skorlar (A/B/C sınıfı)',
        validation: 'Satış performansı, iade oranı, müşteri puanı hesaplanır, badge atanır',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-12',
    name: '📦 Ürün Yönetimi ve Stok Kontrolü',
    description: 'Ürün ekleme → Varyant → Stok → Güncelleme → Toplu işlem',
    enabled: true,
    steps: [
      {
        id: 's12-step1',
        action: 'Ürün Ekleme ve Kategori Seçimi',
        expected: 'Satıcı yeni ürün formu doldurur, kategori seçer, açıklama yazar',
        validation: 'Kategori ağacında doğru seçim yapıldı mı kontrol edilir, slug oluşturulur',
        status: 'pending',
        locked: false
      },
      {
        id: 's12-step2',
        action: 'Ürün Varyantları Tanımlama',
        expected: 'Renk/Beden varyantları eklenir (örn: Kırmızı-S, Mavi-M, Yeşil-L)',
        validation: 'Her varyant için SKU otomatik oluşturulur, fiyat/stok bilgisi girilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's12-step3',
        action: 'Stok Girişi ve Depo Ataması',
        expected: 'Her varyant için stok miktarı girilir, depo seçilir',
        validation: 'Stok inventory DB\'ye kaydedilir, low-stock threshold ayarlanır (min: 5)',
        status: 'pending',
        locked: true
      },
      {
        id: 's12-step4',
        action: 'Ürün Görseli Yükleme',
        expected: 'Ana görsel + 5 ek görsel yüklenir, crop/resize yapılır',
        validation: 'Görsel optimize edilir (WebP, 800×800), CDN\'e upload edilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's12-step5',
        action: 'Ürün Onay Süreci',
        expected: 'Admin ürünü onaylar, yasaklı kelime/kategori kontrolü yapar',
        validation: 'Moderation engine çalışır, onaylanan ürün "Yayında" durumuna geçer',
        status: 'pending',
        locked: true
      },
      {
        id: 's12-step6',
        action: 'Satış Sonrası Stok Güncelleme',
        expected: 'Ürün satıldığında otomatik stok düşer, stok=0 ise "Tükendi" etiketi',
        validation: 'Transaction içinde stok düşer, race condition engellenr (pessimistic lock)',
        status: 'pending',
        locked: true
      },
      {
        id: 's12-step7',
        action: 'Toplu Stok Güncelleme',
        expected: 'Satıcı Excel ile toplu stok güncellemesi yapar (500 ürün)',
        validation: 'CSV parse edilir, batch update çalışır, hata raporu oluşturulur',
        status: 'pending',
        locked: true
      },
      {
        id: 's12-step8',
        action: 'Ürün Arşivleme/Silme',
        expected: 'Satıcı ürünü pasife alır veya siler (soft delete)',
        validation: 'Ürün "Arşivlendi" olur, SEO için 301 redirect ayarlanır',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-13',
    name: '💬 Müşteri Destek Ticket Sistemi',
    description: 'Ticket → Atama → Yanıt → Çözüm → SLA',
    enabled: true,
    steps: [
      {
        id: 's13-step1',
        action: 'Müşteri Destek Talebi Oluşturma',
        expected: 'Müşteri destek formunu doldurur (konu, açıklama, öncelik)',
        validation: 'Ticket DB\'ye kaydedilir, unique ticket ID oluşturulur (TIC-XXXXXX)',
        status: 'pending',
        locked: false
      },
      {
        id: 's13-step2',
        action: 'Otomatik Kategorizasyon ve Önceliklendirme',
        expected: 'AI/NLP ile ticket konusu kategorize edilir (İade, Kargo, Ödeme)',
        validation: 'ML model kategori tahmin eder, SLA süresi atanır (Yüksek: 2h, Normal: 24h)',
        status: 'pending',
        locked: true
      },
      {
        id: 's13-step3',
        action: 'Destek Ekibine Otomatik Atama',
        expected: 'Round-robin algoritması ile müsait temsilciye ticket atanır',
        validation: 'Agent workload kontrol edilir, en az ticket\'a sahip agent seçilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's13-step4',
        action: 'Destek Temsilcisi İlk Yanıt',
        expected: 'Agent ticket\'ı görür, ilk yanıt verir, durum "İşlemde" olur',
        validation: 'First response time kaydedilir, SLA başarı ölçülür',
        status: 'pending',
        locked: true
      },
      {
        id: 's13-step5',
        action: 'Müşteri-Agent Mesajlaşma',
        expected: 'Müşteri ve agent gerçek zamanlı mesajlaşır (WebSocket)',
        validation: 'Her mesaj DB\'ye kaydedilir, push notification gönderilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's13-step6',
        action: 'Ticket Çözüm ve Kapanış',
        expected: 'Agent problemi çözer, ticket\'ı "Çözüldü" olarak kapatır',
        validation: 'Resolution time kaydedilir, müşteriye memnuniyet anketi gönderilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's13-step7',
        action: 'SLA ve Performans Raporu',
        expected: 'Günlük/haftalık SLA raporu oluşturulur (yanıt süresi, çözüm süresi)',
        validation: 'Dashboard\'da SLA başarı oranı görüntülenir, SLA ihlalleri flaglenir',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-14',
    name: '📊 Toplu Veri İçe/Dışa Aktarım',
    description: 'Excel import → Validasyon → Batch insert → Export → Report',
    enabled: true,
    steps: [
      {
        id: 's14-step1',
        action: 'Excel Dosyası Yükleme',
        expected: 'Satıcı Excel dosyasını upload eder (5000 satır ürün verisi)',
        validation: 'Dosya boyutu/formatı kontrol edilir (max: 10MB, .xlsx/.csv)',
        status: 'pending',
        locked: false
      },
      {
        id: 's14-step2',
        action: 'Veri Parsing ve Validasyon',
        expected: 'Excel parse edilir, her satır validasyondan geçer',
        validation: 'Zorunlu alanlar, veri tipleri, format kontrolleri yapılır',
        status: 'pending',
        locked: true
      },
      {
        id: 's14-step3',
        action: 'Hata Raporu Oluşturma',
        expected: 'Geçersiz satırlar için detaylı hata raporu oluşturulur',
        validation: 'Hata mesajları Excel\'de işaretlenir, download linki sunulur',
        status: 'pending',
        locked: true
      },
      {
        id: 's14-step4',
        action: 'Batch Insert İşlemi',
        expected: 'Geçerli veriler chunk\'lara bölünüp DB\'ye insert edilir (500/batch)',
        validation: 'Transaction içinde batch insert, rollback mekanizması çalışır',
        status: 'pending',
        locked: true
      },
      {
        id: 's14-step5',
        action: 'Import Progress Tracking',
        expected: 'Real-time progress bar gösterilir (%0 → %100)',
        validation: 'WebSocket ile progress update\'leri gönderilir, ETA hesaplanır',
        status: 'pending',
        locked: true
      },
      {
        id: 's14-step6',
        action: 'Veri Export (Raporlama)',
        expected: 'Satıcı tüm ürün/sipariş verisini Excel\'e export eder',
        validation: 'Background job oluşturulur, Excel generate edilir, S3\'e upload',
        status: 'pending',
        locked: true
      },
      {
        id: 's14-step7',
        action: 'Zamanlanmış Export (Cron)',
        expected: 'Her hafta pazartesi 09:00\'da otomatik rapor oluşturulur',
        validation: 'Cron job tetiklenir, rapor email ile gönderilir',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-15',
    name: '🔔 Bildirim Sistemi ve Email Queue',
    description: 'Event → Queue → Email/SMS → Delivery → Tracking',
    enabled: true,
    steps: [
      {
        id: 's15-step1',
        action: 'Event Tetikleme (Sipariş Oluşturuldu)',
        expected: 'Sipariş oluşturulduğunda OrderCreated event fırlatılır',
        validation: 'Event listener kaydedilir, notification service tetiklenir',
        status: 'pending',
        locked: false
      },
      {
        id: 's15-step2',
        action: 'Bildirim Template Seçimi',
        expected: 'Event tipine göre doğru email/SMS template seçilir',
        validation: 'Template engine çalışır, değişkenler doldurulur ({{name}}, {{order_no}})',
        status: 'pending',
        locked: true
      },
      {
        id: 's15-step3',
        action: 'Kanalların Belirlenmesi',
        expected: 'Kullanıcı tercihleri kontrol edilir (Email: Evet, SMS: Hayır, Push: Evet)',
        validation: 'User preferences sorgulanır, aktif kanallar belirlenir',
        status: 'pending',
        locked: true
      },
      {
        id: 's15-step4',
        action: 'Queue\'ya Ekleme',
        expected: 'Bildirimler ilgili queue\'lara eklenir (email_queue, sms_queue)',
        validation: 'Redis/RabbitMQ\'ya job push edilir, priority ayarlanır',
        status: 'pending',
        locked: true
      },
      {
        id: 's15-step5',
        action: 'Email/SMS Gönderimi',
        expected: 'Background worker queue\'dan job alır, email/SMS gönderir',
        validation: 'SMTP/Twilio API çağrılır, response kaydedilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's15-step6',
        action: 'Delivery Status Tracking',
        expected: 'Email açılma (open rate), link tıklama (click rate) izlenir',
        validation: 'Webhook\'lar dinlenir, analytics DB\'ye kaydedilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's15-step7',
        action: 'Retry Mekanizması',
        expected: 'Başarısız bildirimler otomatik retry edilir (max: 3 deneme)',
        validation: 'Failed job yeniden queue\'ya eklenir, exponential backoff uygulanır',
        status: 'pending',
        locked: true
      },
      {
        id: 's15-step8',
        action: 'Bildirim Logları ve Dashboard',
        expected: 'Gönderim başarı/hata logları dashboard\'da görüntülenir',
        validation: 'Metrikler hesaplanır (delivery rate, bounce rate), grafik gösterilir',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-16',
    name: '🎁 Hediye Kartı ve Kupon Sistemi',
    description: 'Hediye kartı oluştur → Kodu paylaş → Kullanım → Bakiye kontrolü',
    enabled: true,
    steps: [
      {
        id: 's16-step1',
        action: 'Hediye Kartı Satın Alma',
        expected: 'Müşteri hediye kartı tutarı seçer (50₺/100₺/200₺/Custom)',
        validation: 'Tutar validasyonu yapılır (min: 10₺, max: 5000₺)',
        status: 'pending',
        locked: false
      },
      {
        id: 's16-step2',
        action: 'Ödeme ve Kod Oluşturma',
        expected: 'Ödeme yapılır, unique hediye kodu generate edilir (GIFT-XXXXX)',
        validation: 'Kod DB\'ye kaydedilir, durum "Aktif", bakiye set edilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's16-step3',
        action: 'Email ile Kod Gönderimi',
        expected: 'Hediye kartı alıcı emailine gönderilir (QR kod + kod)',
        validation: 'Queue job oluşturulur, template render edilir, email gönderilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's16-step4',
        action: 'Hediye Kartı Kullanımı',
        expected: 'Alıcı sepette hediye kodunu girer, bakiye kontrol edilir',
        validation: 'Kod geçerliliği kontrol edilir, bakiye sipariş tutarından düşülür',
        status: 'pending',
        locked: true
      },
      {
        id: 's16-step5',
        action: 'Kısmi Kullanım',
        expected: 'Sipariş tutarı hediye kartından az ise kalan bakiye saklanır',
        validation: 'Transaction log oluşturulur, yeni bakiye hesaplanır',
        status: 'pending',
        locked: true
      },
      {
        id: 's16-step6',
        action: 'Bakiye Sorgulama',
        expected: 'Müşteri kalan bakiyeyi sorgulayabilir',
        validation: 'Kod girilir, geçmiş işlemler ve bakiye gösterilir',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-17',
    name: '🔐 İki Faktörlü Kimlik Doğrulama (2FA)',
    description: '2FA Aktivasyon → QR Kod → TOTP Doğrulama → Backup Kodlar',
    enabled: true,
    steps: [
      {
        id: 's17-step1',
        action: '2FA Aktivasyon Başlatma',
        expected: 'Kullanıcı güvenlik ayarlarından 2FA\'yı etkinleştirir',
        validation: 'Mevcut şifre doğrulaması yapılır, session kontrol edilir',
        status: 'pending',
        locked: false
      },
      {
        id: 's17-step2',
        action: 'QR Kod ve Secret Key Üretimi',
        expected: 'TOTP secret key generate edilir, QR kod oluşturulur',
        validation: 'Google Authenticator format, Base32 encoding kontrol edilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's17-step3',
        action: 'Authenticator App Eşleştirme',
        expected: 'Kullanıcı QR kodu Google/Microsoft Authenticator\'a taratır',
        validation: '30 saniyelik TOTP kodu generate edilir ve gösterilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's17-step4',
        action: 'İlk Kod Doğrulaması',
        expected: 'Kullanıcı app\'ten aldığı 6 haneli kodu girer',
        validation: 'TOTP algoritması ile kod doğrulanır (±30s tolerance)',
        status: 'pending',
        locked: true
      },
      {
        id: 's17-step5',
        action: 'Backup Kodları Oluşturma',
        expected: '10 adet tek kullanımlık backup kodu generate edilir',
        validation: 'Kodlar hash\'lenerek DB\'ye kaydedilir, kullanıcıya gösterilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's17-step6',
        action: 'Login ile 2FA Testi',
        expected: 'Kullanıcı giriş yapar, 2FA kodu istenir',
        validation: 'TOTP veya backup kod ile doğrulama yapılır, kullanılan backup işaretlenir',
        status: 'pending',
        locked: true
      },
      {
        id: 's17-step7',
        action: 'Güvenilir Cihaz Kaydı',
        expected: 'Kullanıcı cihazı "30 gün hatırla" olarak işaretler',
        validation: 'Device fingerprint oluşturulur, cookie/token kaydedilir',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-18',
    name: '💬 Canlı Destek ve Chat Sistemi',
    description: 'Chat başlat → Otomatik route → Agent assign → Mesajlaşma → Ticket close',
    enabled: true,
    steps: [
      {
        id: 's18-step1',
        action: 'Canlı Destek Chat Başlatma',
        expected: 'Müşteri site üzerinden chat widget\'ını açar',
        validation: 'WebSocket bağlantısı kurulur, session oluşturulur',
        status: 'pending',
        locked: false
      },
      {
        id: 's18-step2',
        action: 'Otomatik Kategori Seçimi',
        expected: 'Chatbot kategorileri sunar (Sipariş/Ürün/Teknik/Diğer)',
        validation: 'NLP ile kategori tahmin edilir, önerilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's18-step3',
        action: 'Yapay Zeka Ön Filtre',
        expected: 'Chatbot basit soruları otomatik yanıtlar (SSS)',
        validation: 'Intent detection yapılır, %80+ confidence varsa otomatik yanıt',
        status: 'pending',
        locked: true
      },
      {
        id: 's18-step4',
        action: 'Canlı Agent\'a Yönlendirme',
        expected: 'Chatbot çözemezse müsait agent\'a yönlendirir',
        validation: 'Agent pool\'dan availability kontrol edilir, queue oluşturulur',
        status: 'pending',
        locked: true
      },
      {
        id: 's18-step5',
        action: 'Real-time Mesajlaşma',
        expected: 'Müşteri ve agent real-time mesajlaşır',
        validation: 'WebSocket üzerinden mesajlar iletilir, "typing..." gösterilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's18-step6',
        action: 'Dosya/Görsel Paylaşımı',
        expected: 'Müşteri ekran görüntüsü/fatura yükleyebilir',
        validation: 'Dosya tipi/boyut kontrol edilir (max: 5MB), S3\'e upload',
        status: 'pending',
        locked: true
      },
      {
        id: 's18-step7',
        action: 'Chat Transcript Kaydı',
        expected: 'Konuşma bittiğinde transcript email\'e gönderilir',
        validation: 'Mesaj geçmişi DB\'ye kaydedilir, PDF generate edilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's18-step8',
        action: 'Memnuniyet Anketi',
        expected: 'Chat sonrası agent performansı için rating istenir (1-5 yıldız)',
        validation: 'CSAT skoru hesaplanır, agent dashboard\'ına eklenir',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-19',
    name: '🌐 Çoklu Dil ve Para Birimi',
    description: 'Dil seçimi → İçerik çevirisi → Para birimi değişimi → Fiyat hesaplama',
    enabled: true,
    steps: [
      {
        id: 's19-step1',
        action: 'Dil Değiştirme',
        expected: 'Kullanıcı header\'dan dil seçer (TR/EN/DE/AR)',
        validation: 'i18n locale değişir, tüm statik metinler güncellenir',
        status: 'pending',
        locked: false
      },
      {
        id: 's19-step2',
        action: 'Dinamik İçerik Çevirisi',
        expected: 'Ürün adları, açıklamaları seçilen dilde gösterilir',
        validation: 'DB\'den locale\'e göre translation query\'si çalışır',
        status: 'pending',
        locked: true
      },
      {
        id: 's19-step3',
        action: 'Para Birimi Seçimi',
        expected: 'Kullanıcı para birimini değiştirir (TRY/USD/EUR)',
        validation: 'Cookie\'de currency preference kaydedilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's19-step4',
        action: 'Real-time Kur Dönüşümü',
        expected: 'Tüm fiyatlar seçilen para birimine çevrilir',
        validation: 'Exchange rate API çağrılır (cache: 1 saat), fiyatlar hesaplanır',
        status: 'pending',
        locked: true
      },
      {
        id: 's19-step5',
        action: 'Checkout ve Ödeme Entegrasyonu',
        expected: 'Ödeme seçilen para biriminde işlenir',
        validation: 'Payment gateway\'e doğru currency kodu gönderilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's19-step6',
        action: 'Fatura ve Email Dil Seçimi',
        expected: 'Fatura ve emailler kullanıcının dilinde gönderilir',
        validation: 'Email template locale\'e göre seçilir, render edilir',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-20',
    name: '📊 Analitik Dashboard ve Business Intelligence',
    description: 'Veri toplama → Aggregation → Dashboard → Export → Scheduled Reports',
    enabled: true,
    steps: [
      {
        id: 's20-step1',
        action: 'Event Tracking Kurulumu',
        expected: 'Kullanıcı aksiyonları track edilir (page view, click, purchase)',
        validation: 'Analytics event queue\'ya push edilir, batch insert yapılır',
        status: 'pending',
        locked: false
      },
      {
        id: 's20-step2',
        action: 'Gerçek Zamanlı Metrik Hesaplama',
        expected: 'Dashboard real-time metrikleri gösterir (aktif kullanıcı, GMV)',
        validation: 'Redis\'te counter\'lar güncellenir, WebSocket ile push edilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's20-step3',
        action: 'Günlük Aggregation Job',
        expected: 'Her gün 00:00\'da daily metrics hesaplanır',
        validation: 'Cron job tetiklenir, summary table\'lar güncellenir',
        status: 'pending',
        locked: true
      },
      {
        id: 's20-step4',
        action: 'Custom Rapor Oluşturma',
        expected: 'Admin tarih aralığı ve filtreler seçerek rapor oluşturur',
        validation: 'Query builder ile dinamik SQL oluşturulur, execute edilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's20-step5',
        action: 'Grafik ve Görselleştirme',
        expected: 'Chart.js ile çizgi/bar/pasta grafikleri render edilir',
        validation: 'Veri formatlanır, chart options ayarlanır, render edilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's20-step6',
        action: 'Rapor Export (PDF/Excel)',
        expected: 'Dashboard verileri PDF veya Excel formatında export edilir',
        validation: 'Background job oluşturulur, dosya generate edilir, download link',
        status: 'pending',
        locked: true
      },
      {
        id: 's20-step7',
        action: 'Zamanlanmış Email Raporları',
        expected: 'Her pazartesi 09:00\'da haftalık rapor otomatik gönderilir',
        validation: 'Cron schedule çalışır, rapor generate edilir, stakeholder\'lara email',
        status: 'pending',
        locked: true
      },
      {
        id: 's20-step8',
        action: 'Alert ve Threshold Monitoring',
        expected: 'Metrik eşik değerleri aşınca otomatik alert gönderilir',
        validation: 'Threshold kontrol edilir, Slack/Email notification tetiklenir',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-21',
    name: '🏪 Satıcı Mağaza Özelleştirme',
    description: 'Tema seçimi → Logo upload → Banner tasarımı → SEO ayarları → Yayınla',
    enabled: true,
    steps: [
      {
        id: 's21-step1',
        action: 'Mağaza Tema Seçimi',
        expected: 'Satıcı hazır temalar arasından seçim yapar (Modern/Classic/Minimal)',
        validation: 'Tema DB\'ye kaydedilir, CSS değişkenleri güncellenir',
        status: 'pending',
        locked: false
      },
      {
        id: 's21-step2',
        action: 'Logo ve Banner Upload',
        expected: 'Satıcı logo (max: 2MB) ve banner (max: 5MB) yükler',
        validation: 'Image resize edilir (logo: 200×200, banner: 1920×400), S3\'e upload',
        status: 'pending',
        locked: true
      },
      {
        id: 's21-step3',
        action: 'Renk Paleti Özelleştirme',
        expected: 'Primary/secondary renk seçimi yapılır (color picker)',
        validation: 'CSS custom properties güncellenir, preview render edilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's21-step4',
        action: 'Mağaza Bilgileri',
        expected: 'Mağaza adı, açıklama, iletişim bilgileri girilir',
        validation: 'Form validasyonu yapılır, slug generate edilir (seo-friendly)',
        status: 'pending',
        locked: true
      },
      {
        id: 's21-step5',
        action: 'SEO Meta Tags',
        expected: 'Meta title, description, keywords ayarlanır',
        validation: 'Karakter limitleri kontrol edilir (title: 60, desc: 160)',
        status: 'pending',
        locked: true
      },
      {
        id: 's21-step6',
        action: 'Sosyal Medya Entegrasyonu',
        expected: 'Instagram, Facebook, Twitter linkleri eklenir',
        validation: 'URL formatı validate edilir, social icons gösterilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's21-step7',
        action: 'Mağaza Önizleme',
        expected: 'Yayınlamadan önce mağaza preview modda görüntülenir',
        validation: 'Draft mode aktif edilir, public URL generate edilmez',
        status: 'pending',
        locked: true
      },
      {
        id: 's21-step8',
        action: 'Yayınlama ve Canlıya Alma',
        expected: 'Satıcı mağazayı canlıya alır, public erişime açılır',
        validation: 'Status "Published" olur, sitemap güncellenir, CDN cache temizlenir',
        status: 'pending',
        locked: true
      }
    ]
  },
  {
    id: 'scenario-22',
    name: '🔍 Gelişmiş Arama ve Filtreleme',
    description: 'Arama → Faceted filter → Autocomplete → Search analytics → Öneri',
    enabled: true,
    steps: [
      {
        id: 's22-step1',
        action: 'Temel Arama Sorgusu',
        expected: 'Kullanıcı arama kutusuna kelime girer, sonuçlar gösterilir',
        validation: 'Elasticsearch full-text search query çalışır, scoring yapılır',
        status: 'pending',
        locked: false
      },
      {
        id: 's22-step2',
        action: 'Autocomplete ve Suggestion',
        expected: 'Kullanıcı yazarken otomatik öneri gösterilir',
        validation: 'Prefix query çalışır, popüler aramalar skorlanır, cache kontrol',
        status: 'pending',
        locked: true
      },
      {
        id: 's22-step3',
        action: 'Typo Tolerance ve Fuzzy Search',
        expected: 'Yazım hatası olan aramalar otomatik düzeltilir (akıllı telefon → akıllı telefon)',
        validation: 'Fuzzy matching (edit distance: 2) uygulanır, "Bunu mu demek istediniz?" gösterilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's22-step4',
        action: 'Faceted Filtreleme',
        expected: 'Sol panelde dinamik filtreler gösterilir (marka, fiyat, renk)',
        validation: 'Aggregation query çalışır, facet counts hesaplanır',
        status: 'pending',
        locked: true
      },
      {
        id: 's22-step5',
        action: 'Çoklu Filtre Kombinasyonu',
        expected: 'Kullanıcı birden fazla filtre seçer, sonuçlar daraltılır',
        validation: 'Bool query oluşturulur (must + filter), real-time update',
        status: 'pending',
        locked: true
      },
      {
        id: 's22-step6',
        action: 'Sıralama Seçenekleri',
        expected: 'Kullanıcı sıralama seçer (İlgili/Fiyat Artan/Fiyat Azalan/Yeni)',
        validation: 'Sort parametresi eklenir, query yeniden çalışır',
        status: 'pending',
        locked: true
      },
      {
        id: 's22-step7',
        action: 'Arama Loglaması',
        expected: 'Her arama query\'si analytics için loglanır',
        validation: 'Search term, result count, user ID async queue\'ya kaydedilir',
        status: 'pending',
        locked: true
      },
      {
        id: 's22-step8',
        action: 'Popüler Aramalar ve Trend Analizi',
        expected: 'Dashboard\'da en çok aranan kelimeler gösterilir',
        validation: 'Daily aggregation job çalışır, trending keywords belirlenir',
        status: 'pending',
        locked: true
      }
    ]
  }
])

// Logs
const logs = ref<LogEntry[]>([])
const logFilters = ['Tümü', 'Info', 'Success', 'Error', 'Warning', 'API']
const selectedLogFilter = ref('Tümü')

// Running states
const isRunningAll = ref(false)

// Computed
const currentScenario = computed(() => {
  return scenarios.value.find(s => s.id === selectedScenario.value)
})

const stats = computed(() => {
  const allSteps = scenarios.value.flatMap(s => s.steps)
  return {
    total: allSteps.length,
    passed: allSteps.filter(s => s.status === 'passed').length,
    failed: allSteps.filter(s => s.status === 'failed').length,
    running: allSteps.filter(s => s.status === 'running').length,
    pending: allSteps.filter(s => s.status === 'pending').length
  }
})

const successRate = computed(() => {
  const completedTests = stats.value.passed + stats.value.failed
  if (completedTests === 0) return 0
  return Math.round((stats.value.passed / completedTests) * 100)
})

const totalRetries = computed(() => {
  const allSteps = scenarios.value.flatMap(s => s.steps)
  return allSteps.reduce((sum, step) => sum + (step.retries || 0), 0)
})

const scenarioCategories = computed<ScenarioCategory[]>(() => {
  return [
    { id: 'all', name: 'Tümü', icon: '📊', count: scenarios.value.length },
    { id: 'ecommerce', name: 'E-Ticaret', icon: '🛒', count: scenarios.value.filter(s => ['scenario-1', 'scenario-2', 'scenario-3', 'scenario-12', 'scenario-16'].includes(s.id)).length },
    { id: 'services', name: 'Hizmetler', icon: '🏨', count: scenarios.value.filter(s => ['scenario-4', 'scenario-5', 'scenario-6'].includes(s.id)).length },
    { id: 'finance', name: 'Finans & AI', icon: '🧾', count: scenarios.value.filter(s => ['scenario-7', 'scenario-8'].includes(s.id)).length },
    { id: 'admin', name: 'Admin & Sistem', icon: '📋', count: scenarios.value.filter(s => ['scenario-9', 'scenario-10', 'scenario-11', 'scenario-13', 'scenario-14', 'scenario-15'].includes(s.id)).length },
    { id: 'security', name: 'Güvenlik & Auth', icon: '🔐', count: scenarios.value.filter(s => ['scenario-17', 'scenario-18'].includes(s.id)).length },
    { id: 'customization', name: 'Özelleştirme', icon: '🎨', count: scenarios.value.filter(s => ['scenario-19', 'scenario-20', 'scenario-21', 'scenario-22'].includes(s.id)).length }
  ]
})

const filteredScenarios = computed(() => {
  if (selectedCategory.value === 'all') return scenarios.value
  
  const categoryMap: Record<string, string[]> = {
    ecommerce: ['scenario-1', 'scenario-2', 'scenario-3', 'scenario-12', 'scenario-16'],
    services: ['scenario-4', 'scenario-5', 'scenario-6'],
    finance: ['scenario-7', 'scenario-8'],
    admin: ['scenario-9', 'scenario-10', 'scenario-11', 'scenario-13', 'scenario-14', 'scenario-15'],
    security: ['scenario-17', 'scenario-18'],
    customization: ['scenario-19', 'scenario-20', 'scenario-21', 'scenario-22']
  }
  
  const ids = categoryMap[selectedCategory.value] || []
  return scenarios.value.filter(s => ids.includes(s.id))
})

const filteredLogs = computed(() => {
  if (selectedLogFilter.value === 'Tümü') return logs.value
  return logs.value.filter(log => log.type.toLowerCase() === selectedLogFilter.value.toLowerCase())
})

// Methods
function selectScenario(id: string) {
  selectedScenario.value = id
}

function getScenarioStatus(scenario: TestScenario): string {
  if (!scenario.enabled) return 'KAPALI'
  const hasRunning = scenario.steps.some(s => s.status === 'running')
  if (hasRunning) return 'ÇALIŞIYOR'
  const allPassed = scenario.steps.every(s => s.status === 'passed')
  if (allPassed) return 'BAŞARILI'
  const hasFailed = scenario.steps.some(s => s.status === 'failed')
  if (hasFailed) return 'BAŞARISIZ'
  return 'BEKLEMEDE'
}

function getScenarioStatusClass(scenario: TestScenario): string {
  const status = getScenarioStatus(scenario)
  if (status === 'BAŞARILI') return 'bg-green-100 text-green-700'
  if (status === 'BAŞARISIZ') return 'bg-red-100 text-red-700'
  if (status === 'ÇALIŞIYOR') return 'bg-yellow-100 text-yellow-700'
  if (status === 'KAPALI') return 'bg-slate-200 text-slate-600'
  return 'bg-blue-100 text-blue-700'
}

function getStatusText(status: TestStatus): string {
  switch (status) {
    case 'passed': return 'PASS'
    case 'failed': return 'FAIL'
    case 'running': return 'RUNNING'
    default: return 'PENDING'
  }
}

function isRunningScenario(scenarioId: string): boolean {
  const scenario = scenarios.value.find(s => s.id === scenarioId)
  return scenario?.steps.some(s => s.status === 'running') || false
}

function addLog(type: LogType, message: string, details?: any) {
  const timestamp = new Date().toLocaleTimeString('tr-TR', { hour12: false })
  logs.value.unshift({ timestamp, type, message, details })
  // Keep only last 100 logs
  if (logs.value.length > 100) {
    logs.value = logs.value.slice(0, 100)
  }
}

function clearLogs() {
  logs.value = []
  addLog('info', 'Loglar temizlendi')
}

async function runStep(scenarioId: string, stepId: string, isRetry = false) {
  const scenario = scenarios.value.find(s => s.id === scenarioId)
  if (!scenario) return

  const step = scenario.steps.find(s => s.id === stepId)
  if (!step || step.locked) return

  // Initialize retry counter if not exists
  if (step.retries === undefined) step.retries = 0

  // Start running
  step.status = 'running'
  if (!isRetry) {
    step.error = undefined
    step.latency = undefined
    step.retries = 0
  }

  addLog('info', `Test başlatıldı: ${step.action}${isRetry ? ' (Retry #' + (step.retries + 1) + ')' : ''}`)

  const startTime = Date.now()

  // Simulate API call with realistic delay
  const baseDelay = 300 + Math.random() * 800 // 300-1100ms
  await new Promise(resolve => setTimeout(resolve, baseDelay))

  const latency = Date.now() - startTime

  // Critical steps always succeed (user registration, first order step, campaign creation)
  const criticalSteps = [
    's1-step1', 's2-step1', 's3-step1', 's4-step1', 's5-step1', 
    's6-step1', 's7-step1', 's8-step1', 's9-step1', 's10-step1', 
    's11-step1', 's12-step1', 's13-step1', 's14-step1', 's15-step1',
    's16-step1', 's17-step1', 's18-step1', 's19-step1', 's20-step1',
    's21-step1', 's22-step1'
  ]
  const isCritical = criticalSteps.includes(stepId)

  // Improved success rate: 95% for normal steps, 100% for critical
  const successRate = isCritical ? 1.0 : 0.95
  const success = Math.random() < successRate

  step.latency = latency

  if (success) {
    step.status = 'passed'
    addLog('success', `✅ Test başarılı: ${step.action}${isRetry ? ' (Retry sonrası)' : ''}`, {
      endpoint: '/api/test/' + stepId,
      latency,
      status: 200,
      retries: step.retries > 0 ? step.retries : undefined
    })

    // Unlock next step
    const currentIndex = scenario.steps.findIndex(s => s.id === stepId)
    if (currentIndex < scenario.steps.length - 1) {
      scenario.steps[currentIndex + 1].locked = false
    }
  } else {
    // Weighted error distribution (more realistic)
    const errorTypes = [
      { message: 'Network timeout: Request took too long', weight: 30, status: 504 },
      { message: 'Service temporarily unavailable', weight: 25, status: 503 },
      { message: 'Database connection failed', weight: 20, status: 500 },
      { message: 'Validation error: Required field missing', weight: 15, status: 400 },
      { message: 'Authentication token expired', weight: 7, status: 401 },
      { message: 'Rate limit exceeded: 100 req/min', weight: 3, status: 429 }
    ]

    const totalWeight = errorTypes.reduce((sum, e) => sum + e.weight, 0)
    let random = Math.random() * totalWeight
    let selectedError = errorTypes[0]

    for (const error of errorTypes) {
      random -= error.weight
      if (random <= 0) {
        selectedError = error
        break
      }
    }

    step.error = selectedError.message

    // Auto-retry for transient errors (max 2 retries)
    const transientErrors = [504, 503, 500, 429, 401] // Added 401 Authentication & 429 Rate Limit
    const canRetry = transientErrors.includes(selectedError.status) && step.retries < 2

    if (canRetry) {
      step.retries++
      
      // Special handling for rate limit and auth - longer wait
      const baseDelay = (selectedError.status === 429 || selectedError.status === 401) ? 2000 : 1000 // 2s for rate limit/auth, 1s for others
      const retryDelay = baseDelay * Math.pow(2, step.retries - 1) // Exponential backoff
      
      const retryReason = selectedError.status === 401 ? 'Token yenileniyor' : 
                         selectedError.status === 429 ? 'Rate limit bekleniyor' : 
                         'Geçici hata, yeniden deneniyor'
      
      addLog('warning', `⚠️ ${retryReason}, otomatik retry yapılıyor (${step.retries}/2): ${step.action}`, {
        endpoint: '/api/test/' + stepId,
        latency,
        status: selectedError.status,
        error: step.error,
        retries: step.retries
      })

      // Wait before retry
      await new Promise(resolve => setTimeout(resolve, retryDelay))

      // Recursive retry
      return runStep(scenarioId, stepId, true)
    } else {
      step.status = 'failed'
      addLog('error', `❌ Test başarısız: ${step.action}`, {
        endpoint: '/api/test/' + stepId,
        latency,
        status: selectedError.status,
        error: step.error,
        retries: step.retries > 0 ? step.retries : undefined
      })
    }
  }
}

async function runScenario(scenarioId: string) {
  const scenario = scenarios.value.find(s => s.id === scenarioId)
  if (!scenario || !scenario.enabled) return

  addLog('info', `📋 Senaryo başlatıldı: ${scenario.name}`)

  for (const step of scenario.steps) {
    if (step.locked) {
      addLog('warning', `⏭️ Adım atlandı (kilitli): ${step.action}`)
      continue
    }

    await runStep(scenarioId, step.id)

    // If step failed and gate is enabled, stop scenario
    if (step.status === 'failed') {
      addLog('error', `🛑 Senaryo durduruldu: Adım başarısız`)
      break
    }

    // Small delay between steps
    await new Promise(resolve => setTimeout(resolve, 300))
  }

  const allPassed = scenario.steps.every(s => s.status === 'passed')
  if (allPassed) {
    addLog('success', `🎉 Senaryo tamamlandı: ${scenario.name}`)
  }
}

async function runAllTests() {
  isRunningAll.value = true
  addLog('info', '🚀 Tüm testler başlatıldı')

  for (const scenario of scenarios.value) {
    if (!scenario.enabled) {
      addLog('warning', `⏭️ Senaryo atlandı (kapalı): ${scenario.name}`)
      continue
    }

    await runScenario(scenario.id)
    await new Promise(resolve => setTimeout(resolve, 500))
  }

  isRunningAll.value = false
  addLog('success', '✅ Tüm testler tamamlandı')
}

function resetAllTests() {
  for (const scenario of scenarios.value) {
    scenario.steps.forEach((step, index) => {
      step.status = 'pending'
      step.locked = index > 0 // Lock all except first step
      step.error = undefined
      step.latency = undefined
      step.retries = 0
    })
  }
  logs.value = []
  addLog('info', '🔄 Tüm testler sıfırlandı')
  addLog('info', '📊 Test metrikleri sıfırlandı')
  addLog('info', '✨ Sistem hazır - testler başlatılabilir')
}

function filterByCategory(categoryId: string) {
  selectedCategory.value = categoryId
  addLog('info', `📂 Kategori filtresi: ${categoryId}`)
}

// Initialize
addLog('info', '🧪 E2E Test Automation Dashboard başlatıldı')
addLog('info', `📊 ${stats.value.total} test adımı yüklendi`)
</script>
