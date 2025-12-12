<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 px-6 py-4">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
            🏷️ Kategori ve Özellik Yönetimi
            <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 text-xs font-bold">ENHANCED</span>
          </h1>
          <p class="text-slate-500 text-sm mt-1">Kategoriler, özellik setleri ve varyant şablonları</p>
        </div>
        <div class="flex gap-3">
          <button @click="showTreeView = !showTreeView" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 transition">
            <span>🌳</span> {{ showTreeView ? 'Liste' : 'Ağaç' }} Görünümü
          </button>
          <button @click="showCategoryModal = true" class="px-4 py-2 bg-orange-600 text-white rounded-lg font-bold hover:bg-orange-700 transition shadow-lg">
            <span>➕</span> Yeni Kategori
          </button>
        </div>
      </div>
    </header>

    <!-- Tabs -->
    <div class="bg-white border-b border-slate-200">
      <div class="px-6 flex gap-1">
        <button @click="activeTab = 'categories'" class="px-6 py-3 font-medium border-b-2 transition" :class="activeTab === 'categories' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-600 hover:text-slate-800'">
          📁 Kategoriler
        </button>
        <button @click="activeTab = 'attributes'" class="px-6 py-3 font-medium border-b-2 transition" :class="activeTab === 'attributes' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-600 hover:text-slate-800'">
          🎨 Özellik Setleri
        </button>
        <button @click="activeTab = 'variants'" class="px-6 py-3 font-medium border-b-2 transition" :class="activeTab === 'variants' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-600 hover:text-slate-800'">
          🔧 Varyant Şablonları
        </button>
      </div>
    </div>

    <!-- Categories Tab -->
    <div v-if="activeTab === 'categories'" class="p-6">
      <!-- Stats -->
      <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">Toplam Kategori</p>
              <p class="text-2xl font-bold text-slate-800">{{ categories.length }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-2xl">📁</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">Ana Kategoriler</p>
              <p class="text-2xl font-bold text-slate-800">{{ categories.filter(c => !c.parent_id).length }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-2xl">🌳</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">Alt Kategoriler</p>
              <p class="text-2xl font-bold text-slate-800">{{ categories.filter(c => c.parent_id).length }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-2xl">📂</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">Toplam Ürün</p>
              <p class="text-2xl font-bold text-slate-800">{{ categories.reduce((sum, c) => sum + c.product_count, 0) }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center text-2xl">📦</div>
          </div>
        </div>
      </div>

      <!-- Category Table -->
      <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
        <div class="p-4 border-b border-slate-200 flex justify-between items-center">
          <div class="flex gap-3">
            <input v-model="searchQuery" type="text" placeholder="Kategori ara..." class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
            <select class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              <option>Tüm Durumlar</option>
              <option>Aktif</option>
              <option>Pasif</option>
            </select>
          </div>
        </div>
        <table class="w-full">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">Kategori</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">Üst Kategori</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">Özellik Seti</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">Ürün Sayısı</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">Durum</th>
              <th class="px-6 py-3 text-right text-xs font-bold text-slate-600 uppercase">İşlemler</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="category in filteredCategories" :key="category.id" class="hover:bg-slate-50">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <img :src="category.image" class="w-10 h-10 rounded-lg object-cover" alt="">
                  <div>
                    <div class="font-medium text-slate-900">{{ category.name }}</div>
                    <div class="text-xs text-slate-500">/{{ category.slug }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-slate-600">
                <span v-if="category.parent_id" class="px-2 py-1 bg-slate-100 rounded text-xs">{{ getParentName(category.parent_id) }}</span>
                <span v-else class="text-slate-400 italic">Ana Kategori</span>
              </td>
              <td class="px-6 py-4 text-sm text-slate-600">
                <span v-if="category.attribute_set" class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">{{ category.attribute_set }}</span>
                <span v-else class="text-slate-400 italic">-</span>
              </td>
              <td class="px-6 py-4 text-sm text-slate-600">{{ category.product_count }} ürün</td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="category.is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'">
                  {{ category.is_active ? 'Aktif' : 'Pasif' }}
                </span>
              </td>
              <td class="px-6 py-4 text-right space-x-2">
                <button @click="editCategory(category)" class="text-orange-600 hover:text-orange-800 text-sm font-medium">Düzenle</button>
                <button @click="moveCategory(category)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Taşı</button>
                <button @click="deleteCategory(category)" class="text-red-600 hover:text-red-800 text-sm font-medium" :disabled="category.product_count > 0">Sil</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Attributes Tab -->
    <div v-if="activeTab === 'attributes'" class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-slate-800">Özellik Setleri</h2>
        <button @click="showAttributeSetModal = true" class="px-4 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">
          ➕ Yeni Özellik Seti
        </button>
      </div>

      <div class="grid grid-cols-3 gap-6">
        <div v-for="attrSet in attributeSets" :key="attrSet.id" class="bg-white rounded-lg border border-slate-200 shadow-sm p-6">
          <div class="flex justify-between items-start mb-4">
            <div>
              <h3 class="text-lg font-bold text-slate-800">{{ attrSet.name }}</h3>
              <p class="text-sm text-slate-500">{{ attrSet.description }}</p>
            </div>
            <button class="text-slate-400 hover:text-slate-600">⚙️</button>
          </div>

          <div class="space-y-2 mb-4">
            <div v-for="attr in attrSet.attributes" :key="attr.id" class="flex items-center justify-between p-2 bg-slate-50 rounded-lg">
              <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-slate-700">{{ attr.name }}</span>
                <span v-if="attr.required" class="text-xs text-red-500">*</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500">{{ attr.type }}</span>
                <span v-if="attr.values" class="text-xs text-slate-400">({{ attr.values.length }})</span>
              </div>
            </div>
          </div>

          <div class="flex gap-2">
            <button @click="editAttributeSet(attrSet)" class="flex-1 px-3 py-2 bg-orange-50 text-orange-600 rounded-lg text-sm font-medium hover:bg-orange-100 transition">Düzenle</button>
            <button @click="deleteAttributeSet(attrSet)" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition">Sil</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Variants Tab -->
    <div v-if="activeTab === 'variants'" class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-slate-800">Varyant Şablonları</h2>
        <button @click="showVariantTemplateModal = true" class="px-4 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">
          ➕ Yeni Şablon
        </button>
      </div>

      <div class="space-y-4">
        <div v-for="template in variantTemplates" :key="template.id" class="bg-white rounded-lg border border-slate-200 shadow-sm p-6">
          <div class="flex justify-between items-start mb-4">
            <div>
              <h3 class="text-lg font-bold text-slate-800">{{ template.name }}</h3>
              <p class="text-sm text-slate-500">{{ template.description }}</p>
            </div>
            <div class="flex gap-2">
              <button @click="editTemplate(template)" class="text-orange-600 hover:text-orange-800 text-sm font-medium">Düzenle</button>
              <button @click="deleteTemplate(template)" class="text-red-600 hover:text-red-800 text-sm font-medium">Sil</button>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="bg-slate-50 p-4 rounded-lg">
              <h4 class="text-sm font-bold text-slate-700 mb-2">SKU Şeması</h4>
              <code class="text-xs font-mono text-blue-600">{{ template.sku_schema }}</code>
              <p class="text-xs text-slate-500 mt-2">Örnek: {{ template.sku_example }}</p>
            </div>

            <div class="bg-slate-50 p-4 rounded-lg">
              <h4 class="text-sm font-bold text-slate-700 mb-2">Fiyatlandırma Kuralı</h4>
              <div class="space-y-1">
                <div class="flex justify-between text-xs">
                  <span class="text-slate-600">Fiyat Tipi:</span>
                  <span class="font-medium text-slate-800">{{ template.price_rule.type }}</span>
                </div>
                <div v-if="template.price_rule.min" class="flex justify-between text-xs">
                  <span class="text-slate-600">Min Fiyat:</span>
                  <span class="font-medium text-slate-800">{{ formatCurrency(template.price_rule.min) }}</span>
                </div>
                <div v-if="template.price_rule.max" class="flex justify-between text-xs">
                  <span class="text-slate-600">Max Fiyat:</span>
                  <span class="font-medium text-slate-800">{{ formatCurrency(template.price_rule.max) }}</span>
                </div>
              </div>
            </div>

            <div class="bg-slate-50 p-4 rounded-lg">
              <h4 class="text-sm font-bold text-slate-700 mb-2">Stok Kuralı</h4>
              <div class="space-y-1">
                <div class="flex justify-between text-xs">
                  <span class="text-slate-600">Takip:</span>
                  <span class="font-medium text-slate-800">{{ template.stock_rule.track ? 'Evet' : 'Hayır' }}</span>
                </div>
                <div class="flex justify-between text-xs">
                  <span class="text-slate-600">Min Stok:</span>
                  <span class="font-medium text-slate-800">{{ template.stock_rule.min_quantity }}</span>
                </div>
                <div v-if="template.stock_rule.allow_backorder" class="flex justify-between text-xs">
                  <span class="text-slate-600">Ön Sipariş:</span>
                  <span class="font-medium text-green-600">İzinli</span>
                </div>
              </div>
            </div>

            <div class="bg-slate-50 p-4 rounded-lg">
              <h4 class="text-sm font-bold text-slate-700 mb-2">Varyant Boyutları</h4>
              <div class="flex flex-wrap gap-1">
                <span v-for="dim in template.dimensions" :key="dim" class="px-2 py-1 bg-white border border-slate-200 rounded text-xs text-slate-700">{{ dim }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Category Modal -->
    <Transition name="modal">
      <div v-if="showCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="showCategoryModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6 border-b border-slate-200 flex justify-between items-center sticky top-0 bg-white">
            <h2 class="text-xl font-bold text-slate-800">{{ editingCategory ? 'Kategori Düzenle' : 'Yeni Kategori Ekle' }}</h2>
            <button @click="showCategoryModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
          </div>

          <div class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Kategori Adı *</label>
              <input v-model="categoryForm.name" type="text" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500" placeholder="Elektronik">
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Slug</label>
              <input v-model="categoryForm.slug" type="text" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500" placeholder="elektronik">
              <p class="text-xs text-slate-500 mt-1">URL'de görünecek</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Üst Kategori</label>
              <select v-model="categoryForm.parent_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
                <option :value="null">Ana Kategori</option>
                <option v-for="cat in categories.filter(c => !c.parent_id)" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Açıklama</label>
              <textarea v-model="categoryForm.description" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500"></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Özellik Seti</label>
              <select v-model="categoryForm.attribute_set_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
                <option :value="null">Yok</option>
                <option v-for="attrSet in attributeSets" :key="attrSet.id" :value="attrSet.id">{{ attrSet.name }}</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Görsel URL</label>
              <input v-model="categoryForm.image" type="text" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500" placeholder="https://...">
            </div>

            <div class="flex items-center gap-2">
              <input v-model="categoryForm.is_active" type="checkbox" id="is_active" class="w-4 h-4 text-orange-600 rounded focus:ring-orange-500">
              <label for="is_active" class="text-sm font-medium text-slate-700">Aktif</label>
            </div>
          </div>

          <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
            <button @click="showCategoryModal = false" class="px-6 py-2 border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50 transition">İptal</button>
            <button @click="saveCategory" class="px-6 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">{{ editingCategory ? 'Güncelle' : 'Ekle' }}</button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Attribute Set Modal -->
    <Transition name="modal">
      <div v-if="showAttributeSetModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="showAttributeSetModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6 border-b border-slate-200 flex justify-between items-center sticky top-0 bg-white">
            <h2 class="text-xl font-bold text-slate-800">Yeni Özellik Seti</h2>
            <button @click="showAttributeSetModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
          </div>

          <div class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Set Adı *</label>
              <input v-model="attributeSetForm.name" type="text" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500" placeholder="Giyim Özellikleri">
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Açıklama</label>
              <textarea v-model="attributeSetForm.description" rows="2" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500"></textarea>
            </div>

            <div>
              <div class="flex justify-between items-center mb-3">
                <label class="text-sm font-medium text-slate-700">Özellikler</label>
                <button @click="addAttribute" class="text-orange-600 hover:text-orange-800 text-sm font-medium">+ Özellik Ekle</button>
              </div>

              <div class="space-y-3">
                <div v-for="(attr, index) in attributeSetForm.attributes" :key="index" class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                  <div class="flex-1 grid grid-cols-2 gap-3">
                    <input v-model="attr.name" type="text" placeholder="Özellik Adı" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-orange-500">
                    <select v-model="attr.type" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-orange-500">
                      <option value="text">Metin</option>
                      <option value="select">Seçim</option>
                      <option value="multiselect">Çoklu Seçim</option>
                      <option value="number">Sayı</option>
                      <option value="color">Renk</option>
                    </select>
                    <input v-if="attr.type === 'select' || attr.type === 'multiselect'" v-model="attr.valuesText" type="text" placeholder="Değerler (virgülle ayır)" class="col-span-2 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-orange-500">
                  </div>
                  <div class="flex items-center gap-2">
                    <input v-model="attr.required" type="checkbox" class="w-4 h-4 text-orange-600 rounded">
                    <label class="text-xs text-slate-600">Zorunlu</label>
                  </div>
                  <button @click="attributeSetForm.attributes.splice(index, 1)" class="text-red-500 hover:text-red-700">🗑️</button>
                </div>
              </div>
            </div>
          </div>

          <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
            <button @click="showAttributeSetModal = false" class="px-6 py-2 border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50 transition">İptal</button>
            <button @click="saveAttributeSet" class="px-6 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">Kaydet</button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Variant Template Modal -->
    <Transition name="modal">
      <div v-if="showVariantTemplateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="showVariantTemplateModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6 border-b border-slate-200 flex justify-between items-center sticky top-0 bg-white">
            <h2 class="text-xl font-bold text-slate-800">Yeni Varyant Şablonu</h2>
            <button @click="showVariantTemplateModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
          </div>

          <div class="p-6 space-y-6">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Şablon Adı *</label>
                <input v-model="variantTemplateForm.name" type="text" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500" placeholder="Giyim Varyantları">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">SKU Şeması *</label>
                <input v-model="variantTemplateForm.sku_schema" type="text" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500" placeholder="{CATEGORY}-{COLOR}-{SIZE}">
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Açıklama</label>
              <textarea v-model="variantTemplateForm.description" rows="2" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500"></textarea>
            </div>

            <div class="bg-slate-50 p-4 rounded-lg">
              <h4 class="font-medium text-slate-800 mb-3">Fiyatlandırma Kuralları</h4>
              <div class="grid grid-cols-3 gap-3">
                <div>
                  <label class="block text-xs text-slate-600 mb-1">Fiyat Tipi</label>
                  <select v-model="variantTemplateForm.price_rule.type" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-orange-500">
                    <option value="fixed">Sabit</option>
                    <option value="range">Aralık</option>
                    <option value="custom">Özel</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs text-slate-600 mb-1">Min Fiyat (TL)</label>
                  <input v-model.number="variantTemplateForm.price_rule.min" type="number" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-orange-500">
                </div>
                <div>
                  <label class="block text-xs text-slate-600 mb-1">Max Fiyat (TL)</label>
                  <input v-model.number="variantTemplateForm.price_rule.max" type="number" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-orange-500">
                </div>
              </div>
            </div>

            <div class="bg-slate-50 p-4 rounded-lg">
              <h4 class="font-medium text-slate-800 mb-3">Stok Kuralları</h4>
              <div class="grid grid-cols-2 gap-3">
                <div class="flex items-center gap-2">
                  <input v-model="variantTemplateForm.stock_rule.track" type="checkbox" class="w-4 h-4 text-orange-600 rounded">
                  <label class="text-sm text-slate-700">Stok Takibi Yap</label>
                </div>
                <div class="flex items-center gap-2">
                  <input v-model="variantTemplateForm.stock_rule.allow_backorder" type="checkbox" class="w-4 h-4 text-orange-600 rounded">
                  <label class="text-sm text-slate-700">Ön Sipariş İzni</label>
                </div>
                <div>
                  <label class="block text-xs text-slate-600 mb-1">Minimum Stok</label>
                  <input v-model.number="variantTemplateForm.stock_rule.min_quantity" type="number" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-orange-500">
                </div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Varyant Boyutları</label>
              <input v-model="variantTemplateForm.dimensionsText" type="text" placeholder="Renk, Beden, Materyal (virgülle ayır)" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
            </div>
          </div>

          <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
            <button @click="showVariantTemplateModal = false" class="px-6 py-2 border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50 transition">İptal</button>
            <button @click="saveVariantTemplate" class="px-6 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">Kaydet</button>
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
const activeTab = ref('categories')
const showTreeView = ref(false)

// Categories
const categories = ref([
  { id: 1, name: 'Elektronik', slug: 'elektronik', parent_id: null, product_count: 245, is_active: true, image: 'https://placehold.co/100x100/3b82f6/fff?text=E', attribute_set: 'Elektronik Özellikleri', description: 'Elektronik ürünler' },
  { id: 2, name: 'Telefonlar', slug: 'telefonlar', parent_id: 1, product_count: 89, is_active: true, image: 'https://placehold.co/100x100/8b5cf6/fff?text=T', attribute_set: 'Elektronik Özellikleri', description: 'Akıllı telefonlar' },
  { id: 3, name: 'Giyim', slug: 'giyim', parent_id: null, product_count: 512, is_active: true, image: 'https://placehold.co/100x100/ec4899/fff?text=G', attribute_set: 'Giyim Özellikleri', description: 'Giyim ürünleri' },
  { id: 4, name: 'Spor Giyim', slug: 'spor-giyim', parent_id: 3, product_count: 156, is_active: true, image: 'https://placehold.co/100x100/f59e0b/fff?text=S', attribute_set: 'Giyim Özellikleri', description: 'Spor kıyafetleri' },
  { id: 5, name: 'Ev & Yaşam', slug: 'ev-yasam', parent_id: null, product_count: 324, is_active: true, image: 'https://placehold.co/100x100/10b981/fff?text=E', attribute_set: null, description: 'Ev ve yaşam ürünleri' },
])

const searchQuery = ref('')

const filteredCategories = computed(() => {
  if (!searchQuery.value) return categories.value
  return categories.value.filter(c => 
    c.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    c.slug.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const getParentName = (parentId: number | null) => {
  if (!parentId) return '-'
  const parent = categories.value.find(c => c.id === parentId)
  return parent ? parent.name : '-'
}

// Attribute Sets
const attributeSets = ref([
  {
    id: 1,
    name: 'Elektronik Özellikleri',
    description: 'Elektronik ürünler için özellikler',
    attributes: [
      { id: 1, name: 'Marka', type: 'select', required: true, values: ['Apple', 'Samsung', 'Xiaomi', 'Huawei'] },
      { id: 2, name: 'Model', type: 'text', required: true, values: null },
      { id: 3, name: 'Renk', type: 'color', required: false, values: ['#000000', '#FFFFFF', '#0000FF'] },
      { id: 4, name: 'Bellek', type: 'select', required: false, values: ['64GB', '128GB', '256GB', '512GB'] },
    ]
  },
  {
    id: 2,
    name: 'Giyim Özellikleri',
    description: 'Giyim ürünleri için özellikler',
    attributes: [
      { id: 5, name: 'Beden', type: 'select', required: true, values: ['XS', 'S', 'M', 'L', 'XL', 'XXL'] },
      { id: 6, name: 'Renk', type: 'color', required: true, values: ['#000000', '#FFFFFF', '#FF0000', '#0000FF'] },
      { id: 7, name: 'Materyal', type: 'select', required: false, values: ['Pamuk', 'Polyester', 'Yün', 'Karışık'] },
      { id: 8, name: 'Desen', type: 'select', required: false, values: ['Düz', 'Çizgili', 'Desenli'] },
    ]
  }
])

// Variant Templates
const variantTemplates = ref([
  {
    id: 1,
    name: 'Giyim Varyantları',
    description: 'Beden ve renk kombinasyonları için şablon',
    sku_schema: '{CATEGORY}-{COLOR}-{SIZE}',
    sku_example: 'TSH-BLK-M',
    price_rule: { type: 'range', min: 50, max: 500 },
    stock_rule: { track: true, min_quantity: 5, allow_backorder: false },
    dimensions: ['Renk', 'Beden']
  },
  {
    id: 2,
    name: 'Elektronik Varyantları',
    description: 'Renk ve bellek kombinasyonları için şablon',
    sku_schema: '{BRAND}-{MODEL}-{STORAGE}',
    sku_example: 'APPL-14PRO-256',
    price_rule: { type: 'custom', min: 5000, max: 50000 },
    stock_rule: { track: true, min_quantity: 3, allow_backorder: true },
    dimensions: ['Renk', 'Depolama']
  }
])

// Modals
const showCategoryModal = ref(false)
const showAttributeSetModal = ref(false)
const showVariantTemplateModal = ref(false)

// Forms
const editingCategory = ref<any>(null)
const categoryForm = ref({
  name: '',
  slug: '',
  parent_id: null,
  description: '',
  attribute_set_id: null,
  image: '',
  is_active: true
})

const attributeSetForm = ref({
  name: '',
  description: '',
  attributes: [] as any[]
})

const variantTemplateForm = ref({
  name: '',
  description: '',
  sku_schema: '',
  price_rule: { type: 'range', min: 0, max: 0 },
  stock_rule: { track: true, min_quantity: 0, allow_backorder: false },
  dimensionsText: ''
})

// Actions
const editCategory = (category: any) => {
  editingCategory.value = category
  categoryForm.value = { ...category }
  showCategoryModal.value = true
}

const moveCategory = (category: any) => {
  toast.info(`Kategori taşıma özelliği yakında`)
}

const deleteCategory = (category: any) => {
  if (category.product_count > 0) {
    toast.error('Bu kategoride ürünler var! Önce ürünleri taşıyın.')
    return
  }
  
  if (confirm(`"${category.name}" kategorisini silmek istediğinizden emin misiniz?`)) {
    const index = categories.value.findIndex(c => c.id === category.id)
    if (index > -1) {
      categories.value.splice(index, 1)
      toast.success('Kategori silindi')
    }
  }
}

const saveCategory = () => {
  if (!categoryForm.value.name) {
    toast.error('Kategori adı gerekli')
    return
  }

  if (editingCategory.value) {
    const index = categories.value.findIndex(c => c.id === editingCategory.value.id)
    if (index > -1) {
      categories.value[index] = { ...editingCategory.value, ...categoryForm.value }
      toast.success('Kategori güncellendi')
    }
  } else {
    const newCategory = {
      id: categories.value.length + 1,
      ...categoryForm.value,
      product_count: 0
    }
    categories.value.push(newCategory)
    toast.success('Kategori eklendi')
  }

  showCategoryModal.value = false
  editingCategory.value = null
  categoryForm.value = { name: '', slug: '', parent_id: null, description: '', attribute_set_id: null, image: '', is_active: true }
}

const addAttribute = () => {
  attributeSetForm.value.attributes.push({ name: '', type: 'text', required: false, valuesText: '' })
}

const saveAttributeSet = () => {
  if (!attributeSetForm.value.name) {
    toast.error('Set adı gerekli')
    return
  }

  const newSet = {
    id: attributeSets.value.length + 1,
    name: attributeSetForm.value.name,
    description: attributeSetForm.value.description,
    attributes: attributeSetForm.value.attributes.map((attr, idx) => ({
      id: idx + 1,
      name: attr.name,
      type: attr.type,
      required: attr.required,
      values: attr.valuesText ? attr.valuesText.split(',').map((v: string) => v.trim()) : null
    }))
  }

  attributeSets.value.push(newSet)
  toast.success('Özellik seti eklendi')
  showAttributeSetModal.value = false
  attributeSetForm.value = { name: '', description: '', attributes: [] }
}

const editAttributeSet = (attrSet: any) => {
  toast.info('Düzenleme özelliği yakında')
}

const deleteAttributeSet = (attrSet: any) => {
  if (confirm(`"${attrSet.name}" özellik setini silmek istediğinizden emin misiniz?`)) {
    const index = attributeSets.value.findIndex(a => a.id === attrSet.id)
    if (index > -1) {
      attributeSets.value.splice(index, 1)
      toast.success('Özellik seti silindi')
    }
  }
}

const saveVariantTemplate = () => {
  if (!variantTemplateForm.value.name || !variantTemplateForm.value.sku_schema) {
    toast.error('Şablon adı ve SKU şeması gerekli')
    return
  }

  const newTemplate = {
    id: variantTemplates.value.length + 1,
    ...variantTemplateForm.value,
    sku_example: variantTemplateForm.value.sku_schema.replace('{CATEGORY}', 'CAT').replace('{COLOR}', 'BLK').replace('{SIZE}', 'M'),
    dimensions: variantTemplateForm.value.dimensionsText.split(',').map(d => d.trim())
  }

  variantTemplates.value.push(newTemplate)
  toast.success('Varyant şablonu eklendi')
  showVariantTemplateModal.value = false
  variantTemplateForm.value = {
    name: '',
    description: '',
    sku_schema: '',
    price_rule: { type: 'range', min: 0, max: 0 },
    stock_rule: { track: true, min_quantity: 0, allow_backorder: false },
    dimensionsText: ''
  }
}

const editTemplate = (template: any) => {
  toast.info('Düzenleme özelliği yakında')
}

const deleteTemplate = (template: any) => {
  if (confirm(`"${template.name}" şablonunu silmek istediğinizden emin misiniz?`)) {
    const index = variantTemplates.value.findIndex(t => t.id === template.id)
    if (index > -1) {
      variantTemplates.value.splice(index, 1)
      toast.success('Şablon silindi')
    }
  }
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY', maximumFractionDigits: 0 }).format(value)
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
