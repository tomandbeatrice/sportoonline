<template>
  <div class="theme-management">
    <div class="header">
      <h1>🎨 Tema & Görünüm</h1>
      <div class="header-actions">
        <button @click="previewTheme" class="btn-secondary">
          👁️ Önizleme
        </button>
        <button @click="saveTheme" class="btn-primary">
          💾 Değişiklikleri Kaydet
        </button>
      </div>
    </div>

    <div class="tabs">
      <button 
        v-for="tab in tabs" 
        :key="tab.id"
        :class="['tab', { active: activeTab === tab.id }]"
        @click="activeTab = tab.id"
      >
        {{ tab.icon }} {{ tab.label }}
      </button>
    </div>

    <!-- Colors Tab -->
    <div v-if="activeTab === 'colors'" class="tab-content">
      <div class="color-section">
        <h2>🎨 Renk Paleti</h2>
        <p class="section-desc">Site genelinde kullanılacak renkleri seçin</p>

        <div class="color-grid">
          <div class="color-item">
            <label>Birincil Renk (Primary)</label>
            <div class="color-picker-wrapper">
              <input 
                v-model="theme.colors.primary" 
                type="color"
                class="color-picker"
              >
              <input 
                v-model="theme.colors.primary" 
                type="text"
                class="color-input"
                placeholder="#2563eb"
              >
            </div>
            <div class="color-preview" :style="{ background: theme.colors.primary }">
              <button class="preview-btn">Örnek Buton</button>
            </div>
          </div>

          <div class="color-item">
            <label>İkincil Renk (Secondary)</label>
            <div class="color-picker-wrapper">
              <input 
                v-model="theme.colors.secondary" 
                type="color"
                class="color-picker"
              >
              <input 
                v-model="theme.colors.secondary" 
                type="text"
                class="color-input"
              >
            </div>
            <div class="color-preview" :style="{ background: theme.colors.secondary }">
              <button class="preview-btn">Örnek Buton</button>
            </div>
          </div>

          <div class="color-item">
            <label>Vurgu Rengi (Accent)</label>
            <div class="color-picker-wrapper">
              <input 
                v-model="theme.colors.accent" 
                type="color"
                class="color-picker"
              >
              <input 
                v-model="theme.colors.accent" 
                type="text"
                class="color-input"
              >
            </div>
            <div class="color-preview" :style="{ background: theme.colors.accent }">
              <span class="preview-badge">İndirim</span>
            </div>
          </div>

          <div class="color-item">
            <label>Başarı Rengi (Success)</label>
            <div class="color-picker-wrapper">
              <input 
                v-model="theme.colors.success" 
                type="color"
                class="color-picker"
              >
              <input 
                v-model="theme.colors.success" 
                type="text"
                class="color-input"
              >
            </div>
            <div class="color-preview" :style="{ background: theme.colors.success }">
              <span class="preview-badge">Tamamlandı</span>
            </div>
          </div>

          <div class="color-item">
            <label>Hata Rengi (Danger)</label>
            <div class="color-picker-wrapper">
              <input 
                v-model="theme.colors.danger" 
                type="color"
                class="color-picker"
              >
              <input 
                v-model="theme.colors.danger" 
                type="text"
                class="color-input"
              >
            </div>
            <div class="color-preview" :style="{ background: theme.colors.danger }">
              <span class="preview-badge">İptal</span>
            </div>
          </div>

          <div class="color-item">
            <label>Uyarı Rengi (Warning)</label>
            <div class="color-picker-wrapper">
              <input 
                v-model="theme.colors.warning" 
                type="color"
                class="color-picker"
              >
              <input 
                v-model="theme.colors.warning" 
                type="text"
                class="color-input"
              >
            </div>
            <div class="color-preview" :style="{ background: theme.colors.warning }">
              <span class="preview-badge">Bekliyor</span>
            </div>
          </div>
        </div>

        <div class="presets">
          <h3>Hazır Paletler</h3>
          <div class="preset-grid">
            <button 
              v-for="preset in colorPresets" 
              :key="preset.name"
              @click="applyColorPreset(preset)"
              class="preset-btn"
            >
              <div class="preset-colors">
                <span 
                  v-for="(color, index) in Object.values(preset.colors).slice(0, 3)" 
                  :key="index"
                  :style="{ background: color }"
                  class="preset-color"
                ></span>
              </div>
              <span>{{ preset.name }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Typography Tab -->
    <div v-if="activeTab === 'typography'" class="tab-content">
      <div class="typography-section">
        <h2>✍️ Tipografi</h2>
        <p class="section-desc">Yazı tipi ve boyutlarını yapılandırın</p>

        <div class="form-row">
          <div class="form-group">
            <label>Başlık Font Ailesi</label>
            <select v-model="theme.typography.headingFont">
              <option v-for="font in googleFonts" :key="font" :value="font">
                {{ font }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label>Gövde Font Ailesi</label>
            <select v-model="theme.typography.bodyFont">
              <option v-for="font in googleFonts" :key="font" :value="font">
                {{ font }}
              </option>
            </select>
          </div>
        </div>

        <div class="typography-preview">
          <div :style="{ fontFamily: theme.typography.headingFont }">
            <h1>Başlık 1 - {{ theme.typography.headingFont }}</h1>
            <h2>Başlık 2 - Örnek Yazı</h2>
            <h3>Başlık 3 - Örnek Yazı</h3>
          </div>
          <div :style="{ fontFamily: theme.typography.bodyFont }">
            <p>Bu bir paragraf örneğidir. {{ theme.typography.bodyFont }} yazı tipi kullanılmaktadır. Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
          </div>
        </div>

        <h3>Font Boyutları</h3>
        <div class="font-sizes">
          <div class="form-group">
            <label>H1 Boyutu</label>
            <div class="input-group">
              <input v-model.number="theme.typography.sizes.h1" type="number" min="24" max="72">
              <span>px</span>
            </div>
          </div>

          <div class="form-group">
            <label>H2 Boyutu</label>
            <div class="input-group">
              <input v-model.number="theme.typography.sizes.h2" type="number" min="20" max="60">
              <span>px</span>
            </div>
          </div>

          <div class="form-group">
            <label>H3 Boyutu</label>
            <div class="input-group">
              <input v-model.number="theme.typography.sizes.h3" type="number" min="18" max="48">
              <span>px</span>
            </div>
          </div>

          <div class="form-group">
            <label>Gövde Metni</label>
            <div class="input-group">
              <input v-model.number="theme.typography.sizes.body" type="number" min="12" max="24">
              <span>px</span>
            </div>
          </div>

          <div class="form-group">
            <label>Küçük Metin</label>
            <div class="input-group">
              <input v-model.number="theme.typography.sizes.small" type="number" min="10" max="18">
              <span>px</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Layout Tab -->
    <div v-if="activeTab === 'layout'" class="tab-content">
      <div class="layout-section">
        <h2>📐 Layout Yapılandırması</h2>
        <p class="section-desc">Anasayfa bileşenlerinin sıralamasını düzenleyin</p>

        <div class="layout-builder">
          <draggable 
            v-model="theme.layout.homepage" 
            item-key="id"
            handle=".drag-handle"
            class="components-list"
          >
            <template #item="{ element }">
              <div class="component-item">
                <div class="drag-handle">
                  <span>☰</span>
                </div>
                <div class="component-info">
                  <h4>{{ element.name }}</h4>
                  <p>{{ element.description }}</p>
                </div>
                <label class="toggle-switch">
                  <input v-model="element.enabled" type="checkbox">
                  <span class="slider"></span>
                </label>
              </div>
            </template>
          </draggable>
        </div>

        <div class="layout-settings">
          <h3>Layout Ayarları</h3>
          
          <div class="form-group">
            <label>Header Tipi</label>
            <select v-model="theme.layout.headerType">
              <option value="sticky">Yapışkan (Sticky)</option>
              <option value="static">Sabit (Static)</option>
              <option value="transparent">Şeffaf (Transparent)</option>
            </select>
          </div>

          <div class="form-group">
            <label>Footer Tipi</label>
            <select v-model="theme.layout.footerType">
              <option value="default">Varsayılan</option>
              <option value="minimal">Minimal</option>
              <option value="extended">Genişletilmiş</option>
            </select>
          </div>

          <div class="form-group">
            <label>
              <input v-model="theme.layout.showBreadcrumbs" type="checkbox">
              Breadcrumb Göster
            </label>
          </div>

          <div class="form-group">
            <label>
              <input v-model="theme.layout.showSidebar" type="checkbox">
              Sidebar Göster
            </label>
          </div>

          <div class="form-group">
            <label>Maksimum Genişlik</label>
            <select v-model="theme.layout.maxWidth">
              <option value="1200px">1200px</option>
              <option value="1400px">1400px</option>
              <option value="1600px">1600px</option>
              <option value="100%">Tam Genişlik</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Custom CSS Tab -->
    <div v-if="activeTab === 'css'" class="tab-content">
      <div class="css-section">
        <h2>💻 Özel CSS</h2>
        <p class="section-desc">Gelişmiş özelleştirme için özel CSS kodu ekleyin</p>

        <div class="css-editor">
          <textarea 
            v-model="theme.customCss" 
            rows="20"
            placeholder="/* Özel CSS kodunuzu buraya yazın */&#10;.custom-class {&#10;  color: #2563eb;&#10;}"
          ></textarea>
        </div>

        <div class="css-tips">
          <h3>💡 İpuçları</h3>
          <ul>
            <li>CSS değişkenleri kullanabilirsiniz: <code>var(--primary-color)</code></li>
            <li>Mevcut sınıfları override edebilirsiniz</li>
            <li>Responsive tasarım için media queries kullanın</li>
            <li>Değişiklikler anında uygulanmayacaktır, önizleme yapın</li>
          </ul>
        </div>

        <div class="css-variables">
          <h3>📋 Mevcut CSS Değişkenleri</h3>
          <table>
            <tr>
              <td><code>--primary-color</code></td>
              <td>{{ theme.colors.primary }}</td>
            </tr>
            <tr>
              <td><code>--secondary-color</code></td>
              <td>{{ theme.colors.secondary }}</td>
            </tr>
            <tr>
              <td><code>--accent-color</code></td>
              <td>{{ theme.colors.accent }}</td>
            </tr>
            <tr>
              <td><code>--heading-font</code></td>
              <td>{{ theme.typography.headingFont }}</td>
            </tr>
            <tr>
              <td><code>--body-font</code></td>
              <td>{{ theme.typography.bodyFont }}</td>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <!-- Advanced Tab -->
    <div v-if="activeTab === 'advanced'" class="tab-content">
      <div class="advanced-section">
        <h2>⚙️ Gelişmiş Ayarlar</h2>

        <div class="form-group">
          <label>Border Radius (Köşe Yuvarlaklığı)</label>
          <div class="slider-group">
            <input 
              v-model.number="theme.advanced.borderRadius" 
              type="range" 
              min="0" 
              max="24"
              class="slider"
            >
            <span class="slider-value">{{ theme.advanced.borderRadius }}px</span>
          </div>
          <div class="preview-boxes">
            <div 
              v-for="i in 3" 
              :key="i"
              :style="{ borderRadius: theme.advanced.borderRadius + 'px' }"
              class="preview-box"
            >
              {{ theme.advanced.borderRadius }}px
            </div>
          </div>
        </div>

        <div class="form-group">
          <label>Shadow Intensity (Gölge Yoğunluğu)</label>
          <select v-model="theme.advanced.shadowIntensity">
            <option value="none">Yok</option>
            <option value="sm">Küçük</option>
            <option value="md">Orta</option>
            <option value="lg">Büyük</option>
            <option value="xl">Çok Büyük</option>
          </select>
        </div>

        <div class="form-group">
          <label>Animation Speed (Animasyon Hızı)</label>
          <select v-model="theme.advanced.animationSpeed">
            <option value="slow">Yavaş (500ms)</option>
            <option value="normal">Normal (300ms)</option>
            <option value="fast">Hızlı (150ms)</option>
          </select>
        </div>

        <div class="form-group">
          <label>
            <input v-model="theme.advanced.enableAnimations" type="checkbox">
            Animasyonları Etkinleştir
          </label>
        </div>

        <div class="form-group">
          <label>
            <input v-model="theme.advanced.enableParallax" type="checkbox">
            Parallax Efektleri
          </label>
        </div>

        <div class="form-group">
          <label>
            <input v-model="theme.advanced.darkModeSupport" type="checkbox">
            Karanlık Mod Desteği
          </label>
        </div>

        <div class="theme-actions">
          <button @click="exportTheme" class="btn-secondary">
            📥 Temayı Dışa Aktar
          </button>
          <button @click="importTheme" class="btn-secondary">
            📤 Tema İçe Aktar
          </button>
          <button @click="resetTheme" class="btn-danger">
            🔄 Varsayılana Sıfırla
          </button>
        </div>
      </div>
    </div>

    <!-- Preview Modal -->
    <div v-if="showPreview" class="preview-modal" @click.self="showPreview = false">
      <div class="preview-content">
        <div class="preview-header">
          <h2>🎨 Tema Önizlemesi</h2>
          <button @click="showPreview = false" class="close-btn">×</button>
        </div>
        <div class="preview-body">
          <iframe 
            :src="previewUrl" 
            frameborder="0"
            class="preview-iframe"
          ></iframe>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'
import draggable from 'vuedraggable'

const activeTab = ref('colors')
const showPreview = ref(false)
const previewUrl = ref('')

const tabs = [
  { id: 'colors', label: 'Renkler', icon: '🎨' },
  { id: 'typography', label: 'Tipografi', icon: '✍️' },
  { id: 'layout', label: 'Layout', icon: '📐' },
  { id: 'css', label: 'Özel CSS', icon: '💻' },
  { id: 'advanced', label: 'Gelişmiş', icon: '⚙️' }
]

const theme = reactive({
  colors: {
    primary: '#2563eb',
    secondary: '#64748b',
    accent: '#f59e0b',
    success: '#10b981',
    danger: '#ef4444',
    warning: '#f59e0b'
  },
  typography: {
    headingFont: 'Poppins',
    bodyFont: 'Inter',
    sizes: {
      h1: 48,
      h2: 36,
      h3: 28,
      body: 16,
      small: 14
    }
  },
  layout: {
    homepage: [
      { id: 1, name: 'Hero Banner', description: 'Ana görsel slider', enabled: true },
      { id: 2, name: 'Kampanyalar', description: 'Aktif kampanyalar', enabled: true },
      { id: 3, name: 'Öne Çıkan Ürünler', description: 'Featured products', enabled: true },
      { id: 4, name: 'Kategoriler', description: 'Ürün kategorileri', enabled: true },
      { id: 5, name: 'Yeni Ürünler', description: 'Son eklenen ürünler', enabled: true },
      { id: 6, name: 'En Çok Satanlar', description: 'Best sellers', enabled: true },
      { id: 7, name: 'Blog Yazıları', description: 'Son blog gönderileri', enabled: false },
      { id: 8, name: 'Markalar', description: 'Partner markalar', enabled: true }
    ],
    headerType: 'sticky',
    footerType: 'default',
    showBreadcrumbs: true,
    showSidebar: false,
    maxWidth: '1400px'
  },
  customCss: '',
  advanced: {
    borderRadius: 8,
    shadowIntensity: 'md',
    animationSpeed: 'normal',
    enableAnimations: true,
    enableParallax: false,
    darkModeSupport: false
  }
})

const colorPresets = [
  {
    name: 'Varsayılan',
    colors: {
      primary: '#2563eb',
      secondary: '#64748b',
      accent: '#f59e0b',
      success: '#10b981',
      danger: '#ef4444',
      warning: '#f59e0b'
    }
  },
  {
    name: 'Ocean',
    colors: {
      primary: '#0ea5e9',
      secondary: '#06b6d4',
      accent: '#14b8a6',
      success: '#10b981',
      danger: '#ef4444',
      warning: '#f59e0b'
    }
  },
  {
    name: 'Sunset',
    colors: {
      primary: '#f97316',
      secondary: '#fb923c',
      accent: '#fbbf24',
      success: '#10b981',
      danger: '#ef4444',
      warning: '#f59e0b'
    }
  },
  {
    name: 'Forest',
    colors: {
      primary: '#059669',
      secondary: '#10b981',
      accent: '#34d399',
      success: '#10b981',
      danger: '#ef4444',
      warning: '#f59e0b'
    }
  },
  {
    name: 'Royal',
    colors: {
      primary: '#7c3aed',
      secondary: '#a78bfa',
      accent: '#c084fc',
      success: '#10b981',
      danger: '#ef4444',
      warning: '#f59e0b'
    }
  },
  {
    name: 'Pink',
    colors: {
      primary: '#ec4899',
      secondary: '#f472b6',
      accent: '#f9a8d4',
      success: '#10b981',
      danger: '#ef4444',
      warning: '#f59e0b'
    }
  }
]

const googleFonts = [
  'Inter',
  'Poppins',
  'Roboto',
  'Open Sans',
  'Lato',
  'Montserrat',
  'Raleway',
  'Nunito',
  'Playfair Display',
  'Merriweather'
]

onMounted(() => {
  loadTheme()
})

const loadTheme = async () => {
  try {
    const response = await axios.get('/api/admin/theme')
    Object.assign(theme, response.data)
  } catch (error) {
    console.error('Tema yüklenemedi:', error)
  }
}

const saveTheme = async () => {
  try {
    await axios.post('/api/admin/theme', theme)
    alert('Tema kaydedildi')
    generateCssVariables()
  } catch (error) {
    console.error('Tema kayıt hatası:', error)
  }
}

const generateCssVariables = () => {
  const root = document.documentElement
  root.style.setProperty('--primary-color', theme.colors.primary)
  root.style.setProperty('--secondary-color', theme.colors.secondary)
  root.style.setProperty('--accent-color', theme.colors.accent)
  root.style.setProperty('--success-color', theme.colors.success)
  root.style.setProperty('--danger-color', theme.colors.danger)
  root.style.setProperty('--warning-color', theme.colors.warning)
  root.style.setProperty('--heading-font', theme.typography.headingFont)
  root.style.setProperty('--body-font', theme.typography.bodyFont)
  root.style.setProperty('--border-radius', theme.advanced.borderRadius + 'px')
}

const applyColorPreset = (preset) => {
  Object.assign(theme.colors, preset.colors)
}

const previewTheme = () => {
  previewUrl.value = '/?preview=true'
  showPreview.value = true
}

const exportTheme = () => {
  const json = JSON.stringify(theme, null, 2)
  const blob = new Blob([json], { type: 'application/json' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = 'theme.json'
  a.click()
}

const importTheme = () => {
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = '.json'
  input.onchange = (e) => {
    const file = e.target.files[0]
    const reader = new FileReader()
    reader.onload = (event) => {
      try {
        const imported = JSON.parse(event.target.result)
        Object.assign(theme, imported)
        alert('Tema içe aktarıldı')
      } catch (error) {
        alert('Geçersiz tema dosyası')
      }
    }
    reader.readAsText(file)
  }
  input.click()
}

const resetTheme = () => {
  if (confirm('Tüm tema ayarlarını varsayılana sıfırlamak istediğinizden emin misiniz?')) {
    loadTheme()
  }
}
</script>

<style scoped>
.theme-management {
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.header h1 {
  font-size: 28px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.header-actions {
  display: flex;
  gap: 10px;
}

.tabs {
  display: flex;
  gap: 10px;
  margin-bottom: 30px;
  border-bottom: 2px solid #e5e7eb;
}

.tab {
  padding: 12px 24px;
  background: none;
  border: none;
  border-bottom: 2px solid transparent;
  margin-bottom: -2px;
  cursor: pointer;
  font-size: 15px;
  font-weight: 500;
  color: #6b7280;
  transition: all 0.3s;
}

.tab:hover {
  color: #2563eb;
}

.tab.active {
  color: #2563eb;
  border-bottom-color: #2563eb;
}

.tab-content {
  animation: fadeIn 0.3s;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.color-section, .typography-section, .layout-section, .css-section, .advanced-section {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

h2 {
  font-size: 24px;
  font-weight: 600;
  margin-bottom: 10px;
  color: #1f2937;
}

.section-desc {
  color: #6b7280;
  margin-bottom: 30px;
}

.color-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 25px;
  margin-bottom: 40px;
}

.color-item label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 10px;
}

.color-picker-wrapper {
  display: flex;
  gap: 10px;
  margin-bottom: 15px;
}

.color-picker {
  width: 60px;
  height: 40px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
}

.color-input {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-family: monospace;
  font-size: 14px;
}

.color-preview {
  padding: 20px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.preview-btn {
  padding: 10px 20px;
  background: rgba(255, 255, 255, 0.9);
  border: none;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
}

.preview-badge {
  padding: 6px 16px;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 12px;
  font-size: 13px;
  font-weight: 500;
}

.presets {
  margin-top: 40px;
}

.presets h3 {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 20px;
  color: #1f2937;
}

.preset-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 15px;
}

.preset-btn {
  padding: 15px;
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s;
  text-align: center;
}

.preset-btn:hover {
  border-color: #2563eb;
  transform: translateY(-2px);
}

.preset-colors {
  display: flex;
  gap: 5px;
  margin-bottom: 10px;
  justify-content: center;
}

.preset-color {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 8px;
}

.form-group select,
.form-group input[type="text"],
.form-group input[type="number"] {
  width: 100%;
  padding: 10px 15px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
}

.typography-preview {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 30px;
  margin: 30px 0;
}

.typography-preview h1 {
  margin-bottom: 15px;
}

.typography-preview h2 {
  margin-bottom: 10px;
  font-size: 36px;
}

.typography-preview h3 {
  margin-bottom: 15px;
  font-size: 28px;
}

.font-sizes {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.input-group {
  display: flex;
  align-items: center;
  gap: 10px;
}

.input-group input {
  flex: 1;
}

.input-group span {
  font-size: 14px;
  color: #6b7280;
}

.components-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 30px;
}

.component-item {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 15px;
  display: flex;
  align-items: center;
  gap: 15px;
}

.drag-handle {
  cursor: move;
  color: #9ca3af;
  font-size: 20px;
}

.component-info {
  flex: 1;
}

.component-info h4 {
  font-size: 15px;
  font-weight: 600;
  margin-bottom: 5px;
  color: #1f2937;
}

.component-info p {
  font-size: 13px;
  color: #6b7280;
  margin: 0;
}

.toggle-switch {
  position: relative;
  display: inline-block;
  width: 48px;
  height: 24px;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #d1d5db;
  transition: 0.3s;
  border-radius: 24px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.3s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #2563eb;
}

input:checked + .slider:before {
  transform: translateX(24px);
}

.layout-settings {
  background: #f9fafb;
  border-radius: 8px;
  padding: 20px;
}

.layout-settings h3 {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 20px;
  color: #1f2937;
}

.css-editor textarea {
  width: 100%;
  padding: 15px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-family: 'Courier New', monospace;
  font-size: 14px;
  resize: vertical;
}

.css-tips {
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  border-radius: 8px;
  padding: 20px;
  margin-top: 20px;
}

.css-tips h3 {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 15px;
  color: #1e40af;
}

.css-tips ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.css-tips li {
  padding: 8px 0;
  color: #1e40af;
  font-size: 14px;
}

.css-tips code {
  background: #dbeafe;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 13px;
}

.css-variables {
  margin-top: 30px;
}

.css-variables h3 {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 15px;
  color: #1f2937;
}

.css-variables table {
  width: 100%;
  border-collapse: collapse;
}

.css-variables td {
  padding: 10px;
  border: 1px solid #e5e7eb;
  font-size: 14px;
}

.css-variables code {
  background: #f3f4f6;
  padding: 4px 8px;
  border-radius: 4px;
  font-family: monospace;
  font-size: 13px;
}

.slider-group {
  display: flex;
  align-items: center;
  gap: 15px;
}

.slider-group .slider {
  flex: 1;
  height: 6px;
  background: #d1d5db;
  border-radius: 3px;
  position: relative;
}

.slider-value {
  font-size: 14px;
  font-weight: 600;
  color: #2563eb;
  min-width: 50px;
}

.preview-boxes {
  display: flex;
  gap: 15px;
  margin-top: 15px;
}

.preview-box {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 12px;
  font-weight: 600;
}

.theme-actions {
  display: flex;
  gap: 10px;
  margin-top: 30px;
}

.btn-primary, .btn-secondary, .btn-danger {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-primary {
  background: #2563eb;
  color: white;
}

.btn-primary:hover {
  background: #1d4ed8;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover {
  background: #4b5563;
}

.btn-danger {
  background: #ef4444;
  color: white;
}

.btn-danger:hover {
  background: #dc2626;
}

.preview-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.preview-content {
  background: white;
  border-radius: 12px;
  width: 95%;
  height: 95%;
  display: flex;
  flex-direction: column;
}

.preview-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #e5e7eb;
}

.preview-header h2 {
  font-size: 18px;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 32px;
  color: #6b7280;
  cursor: pointer;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  transition: all 0.3s;
}

.close-btn:hover {
  background: #f3f4f6;
}

.preview-body {
  flex: 1;
  overflow: hidden;
}

.preview-iframe {
  width: 100%;
  height: 100%;
}
</style>
