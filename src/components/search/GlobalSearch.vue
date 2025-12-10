<template>
  <div ref="searchRef" class="relative w-full max-w-2xl">
    <!-- Search Input -->
    <div 
      class="flex items-center gap-3 bg-white border-2 rounded-2xl px-4 py-3 transition-all duration-200"
      :class="[
        isFocused ? 'border-indigo-500 shadow-lg shadow-indigo-100' : 'border-slate-200',
        large ? 'px-5 py-4' : ''
      ]"
    >
      <span class="text-slate-400">🔍</span>
      <input
        ref="inputRef"
        v-model="query"
        type="text"
        :placeholder="placeholder"
        @focus="handleFocus"
        @blur="handleBlur"
        @input="handleInput"
        @keydown="handleKeydown"
        class="flex-1 bg-transparent outline-none text-slate-900 placeholder:text-slate-400"
        :class="large ? 'text-lg' : ''"
      />
      
      <!-- Loading -->
      <div v-if="isSearching" class="animate-spin text-indigo-500">⟳</div>
      
      <!-- Clear Button -->
      <button 
        v-if="query.length > 0"
        @click="clearSearch"
        class="p-1 text-slate-400 hover:text-slate-600 transition"
      >
        ✕
      </button>
      
      <!-- Shortcut hint -->
      <kbd v-if="!query && showShortcut" class="hidden md:flex items-center gap-1 px-2 py-1 bg-slate-100 text-slate-500 text-xs rounded-lg">
        <span>Ctrl</span>
        <span>+</span>
        <span>K</span>
      </kbd>
    </div>

    <!-- Dropdown -->
    <Transition name="dropdown">
      <div 
        v-if="showDropdown"
        class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50"
      >
        <!-- Quick Filters -->
        <div v-if="!query" class="p-4 border-b border-slate-100">
          <div class="text-xs font-medium text-slate-400 uppercase mb-3">Hızlı Filtreler</div>
          <div class="flex flex-wrap gap-2">
            <button 
              v-for="filter in quickFilters"
              :key="filter.id"
              @click="applyQuickFilter(filter)"
              class="px-3 py-1.5 bg-slate-100 hover:bg-indigo-100 hover:text-indigo-700 rounded-full text-sm transition"
            >
              {{ filter.icon }} {{ filter.label }}
            </button>
          </div>
        </div>

        <!-- Recent Searches -->
        <div v-if="!query && recentSearches.length > 0" class="p-4 border-b border-slate-100">
          <div class="flex items-center justify-between mb-3">
            <div class="text-xs font-medium text-slate-400 uppercase">Son Aramalar</div>
            <button @click="clearRecentSearches" class="text-xs text-slate-400 hover:text-red-500 transition">
              Temizle
            </button>
          </div>
          <div class="space-y-1">
            <button
              v-for="(search, i) in recentSearches.slice(0, 5)"
              :key="i"
              @click="selectRecentSearch(search)"
              class="flex items-center gap-3 w-full px-3 py-2 hover:bg-slate-50 rounded-xl transition text-left"
            >
              <span class="text-slate-400">🕐</span>
              <span class="text-slate-700">{{ search }}</span>
            </button>
          </div>
        </div>

        <!-- Trending -->
        <div v-if="!query && trending.length > 0" class="p-4 border-b border-slate-100">
          <div class="text-xs font-medium text-slate-400 uppercase mb-3">🔥 Trend Aramalar</div>
          <div class="flex flex-wrap gap-2">
            <button 
              v-for="item in trending"
              :key="item"
              @click="selectTrending(item)"
              class="px-3 py-1.5 border border-slate-200 hover:border-indigo-500 hover:text-indigo-700 rounded-full text-sm transition"
            >
              {{ item }}
            </button>
          </div>
        </div>

        <!-- Smart Suggestions -->
        <div v-if="smartSearch.suggestions.length > 0" class="p-4 border-b border-slate-100 bg-indigo-50/50">
          <div class="text-xs font-medium text-indigo-400 uppercase mb-2">💡 Akıllı Öneriler</div>
          <div class="flex flex-wrap gap-2">
            <button 
              v-for="(suggestion, i) in smartSearch.suggestions"
              :key="i"
              @click="applySuggestion(suggestion.text)"
              class="flex items-center gap-2 px-3 py-1.5 bg-white border border-indigo-100 hover:border-indigo-300 rounded-lg text-sm text-indigo-700 transition shadow-sm"
            >
              <span v-if="suggestion.type === 'correction'">Bunu mu demek istediniz: <strong>{{ suggestion.text }}</strong>?</span>
              <span v-else-if="suggestion.type === 'related'">İlgili: {{ suggestion.text }}</span>
              <span v-else>{{ suggestion.text }}</span>
            </button>
          </div>
        </div>

        <!-- Search Results -->
        <div v-if="query && results.length > 0" class="max-h-96 overflow-y-auto">
          <template v-for="group in groupedResults" :key="group.type">
            <div class="px-4 py-2 bg-slate-50 text-xs font-medium text-slate-400 uppercase sticky top-0">
              {{ group.label }}
            </div>
            <div 
              v-for="(result, i) in group.items"
              :key="result.id"
              @click="selectResult(result)"
              @mouseenter="highlightedIndex = getGlobalIndex(group.type, i)"
              class="flex items-center gap-4 px-4 py-3 hover:bg-indigo-50 cursor-pointer transition"
              :class="{ 'bg-indigo-50': highlightedIndex === getGlobalIndex(group.type, i) }"
            >
              <img 
                v-if="result.image"
                :src="result.image" 
                :alt="result.title"
                class="w-12 h-12 rounded-xl object-cover"
              />
              <div v-else class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-xl">
                {{ getResultIcon(result.type) }}
              </div>
              
              <div class="flex-1 min-w-0">
                <div class="font-medium text-slate-900" v-html="highlightMatch(result.title, query)"></div>
                <div v-if="result.subtitle" class="text-sm text-slate-500 truncate">{{ result.subtitle }}</div>
              </div>
              
              <div v-if="result.price" class="font-bold text-indigo-600">{{ formatPrice(result.price) }}</div>
              <div v-if="result.badge" class="px-2 py-1 bg-orange-100 text-orange-700 text-xs rounded-full">
                {{ result.badge }}
              </div>
            </div>
          </template>
        </div>

        <!-- No Results -->
        <div v-if="query && results.length === 0 && !isSearching" class="p-8 text-center">
          <div class="text-4xl mb-3">🔍</div>
          <div class="font-medium text-slate-900">Sonuç bulunamadı</div>
          <div class="text-sm text-slate-500 mt-1">"{{ query }}" için arama sonucu yok</div>
          <div class="mt-4">
            <button 
              @click="searchAll"
              class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition"
            >
              Tüm kategorilerde ara
            </button>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
          <div class="flex items-center gap-4">
            <span><kbd class="px-1.5 py-0.5 bg-white border rounded">↑↓</kbd> Gezin</span>
            <span><kbd class="px-1.5 py-0.5 bg-white border rounded">Enter</kbd> Seç</span>
            <span><kbd class="px-1.5 py-0.5 bg-white border rounded">Esc</kbd> Kapat</span>
          </div>
          <router-link 
            to="/search/advanced"
            class="text-indigo-600 hover:underline"
            @click="showDropdown = false"
          >
            Gelişmiş arama →
          </router-link>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useDebounceFn, onClickOutside } from '@vueuse/core'
import { useSmartSearchStore } from '@/stores/smartSearchStore'
import { searchService, type SearchResult } from '@/services/searchService'

interface QuickFilter {
  id: string
  label: string
  icon: string
  params: Record<string, string>
}

const props = withDefaults(defineProps<{
  placeholder?: string
  large?: boolean
  showShortcut?: boolean
  autofocus?: boolean
}>(), {
  placeholder: 'Ürün, marka veya kategori ara...',
  large: false,
  showShortcut: true,
  autofocus: false
})

const emit = defineEmits<{
  search: [query: string]
  result: [result: SearchResult]
}>()

const router = useRouter()
const smartSearch = useSmartSearchStore()
const searchRef = ref<HTMLElement | null>(null)
const inputRef = ref<HTMLInputElement | null>(null)

const query = ref('')
const isFocused = ref(false)
const isSearching = ref(false)
const showDropdown = ref(false)
const highlightedIndex = ref(-1)
const results = ref<SearchResult[]>([])
const recentSearches = ref<string[]>([])
const trending = ref<string[]>([
  'Nike Air Max', 'Adidas Superstar', 'Spor Çanta', 'Koşu Ayakkabısı', 'Forma'
])

const quickFilters: QuickFilter[] = [
  { id: 'sale', label: 'İndirimli', icon: '🏷️', params: { sale: 'true' } },
  { id: 'new', label: 'Yeni', icon: '✨', params: { sort: 'newest' } },
  { id: 'popular', label: 'Popüler', icon: '🔥', params: { sort: 'popular' } },
  { id: 'free-shipping', label: 'Ücretsiz Kargo', icon: '🚚', params: { freeShipping: 'true' } },
]

const groupedResults = computed(() => {
  const groups: { type: string; label: string; items: SearchResult[] }[] = []
  const types = [...new Set(results.value.map(r => r.type))]
  
  const labels: Record<string, string> = {
    product: 'Ürünler',
    category: 'Kategoriler',
    seller: 'Satıcılar',
    brand: 'Markalar',
    page: 'Sayfalar',
    tour: 'Turlar',
    car: 'Araç Kiralama',
    insurance: 'Sigorta',
    activity: 'Aktiviteler'
  }
  
  types.forEach(type => {
    groups.push({
      type,
      label: labels[type] || type,
      items: results.value.filter(r => r.type === type)
    })
  })
  
  return groups
})

const getGlobalIndex = (type: string, localIndex: number): number => {
  let index = 0
  for (const group of groupedResults.value) {
    if (group.type === type) {
      return index + localIndex
    }
    index += group.items.length
  }
  return -1
}

const handleFocus = () => {
  isFocused.value = true
  showDropdown.value = true
  loadRecentSearches()
}

const handleBlur = () => {
  isFocused.value = false
}

const handleInput = useDebounceFn(() => {
  if (query.value.length >= 2) {
    smartSearch.analyzeQuery(query.value)
    performSearch()
  } else {
    results.value = []
    smartSearch.suggestions = []
  }
}, 300)

const handleKeydown = (e: KeyboardEvent) => {
  const totalResults = results.value.length
  
  switch (e.key) {
    case 'ArrowDown':
      e.preventDefault()
      highlightedIndex.value = Math.min(highlightedIndex.value + 1, totalResults - 1)
      break
    case 'ArrowUp':
      e.preventDefault()
      highlightedIndex.value = Math.max(highlightedIndex.value - 1, 0)
      break
    case 'Enter':
      e.preventDefault()
      if (highlightedIndex.value >= 0 && results.value[highlightedIndex.value]) {
        selectResult(results.value[highlightedIndex.value])
      } else if (query.value) {
        searchAll()
      }
      break
    case 'Escape':
      showDropdown.value = false
      inputRef.value?.blur()
      break
  }
}

const performSearch = async () => {
  isSearching.value = true
  try {
    const response = await searchService.search(query.value)
    results.value = response.data.data as SearchResult[]
    highlightedIndex.value = results.value.length > 0 ? 0 : -1
  } catch (error) {
    console.error('Arama hatası:', error)
    results.value = []
  } finally {
    isSearching.value = false
  }
}

const selectResult = (result: SearchResult) => {
  saveToRecent(query.value)
  showDropdown.value = false
  emit('result', result)
  router.push(result.url)
}

const selectRecentSearch = (search: string) => {
  query.value = search
  performSearch()
}

const selectTrending = (item: string) => {
  query.value = item
  performSearch()
}

const applySuggestion = (text: string) => {
  query.value = text
  smartSearch.analyzeQuery(text)
  performSearch()
}

const applyQuickFilter = (filter: QuickFilter) => {
  showDropdown.value = false
  router.push({ path: '/products', query: filter.params })
}

const clearSearch = () => {
  query.value = ''
  results.value = []
  inputRef.value?.focus()
}

const searchAll = () => {
  if (query.value) {
    saveToRecent(query.value)
    showDropdown.value = false
    emit('search', query.value)
    router.push({ path: '/search', query: { q: query.value } })
  }
}

const saveToRecent = (search: string) => {
  if (!search.trim()) return
  const recent = recentSearches.value.filter(s => s !== search)
  recent.unshift(search)
  recentSearches.value = recent.slice(0, 10)
  localStorage.setItem('recentSearches', JSON.stringify(recentSearches.value))
}

const loadRecentSearches = () => {
  try {
    const saved = localStorage.getItem('recentSearches')
    if (saved) {
      recentSearches.value = JSON.parse(saved)
    }
  } catch {}
}

const clearRecentSearches = () => {
  recentSearches.value = []
  localStorage.removeItem('recentSearches')
}

const getResultIcon = (type: string) => {
  const icons: Record<string, string> = {
    product: '📦',
    category: '📁',
    seller: '🏪',
    brand: '🏷️',
    page: '📄'
  }
  return icons[type] || '🔍'
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(price)
}

const highlightMatch = (text: string, query: string) => {
  if (!query) return text
  const regex = new RegExp(`(${query})`, 'gi')
  return text.replace(regex, '<mark class="bg-yellow-200 rounded">$1</mark>')
}

// Keyboard shortcut Ctrl+K
const handleGlobalKeydown = (e: KeyboardEvent) => {
  if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
    e.preventDefault()
    inputRef.value?.focus()
  }
}

// Click outside to close
onClickOutside(searchRef, () => {
  showDropdown.value = false
})

onMounted(() => {
  document.addEventListener('keydown', handleGlobalKeydown)
  loadRecentSearches()
  if (props.autofocus) {
    inputRef.value?.focus()
  }
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleGlobalKeydown)
})
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
