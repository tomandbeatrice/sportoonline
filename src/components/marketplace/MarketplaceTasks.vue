<template>
  <!-- ═══════════════════════════════════════════════════════════════════
       ✅ GÜNLÜK GÖREVLER - Lifestyle Hub
       ═══════════════════════════════════════════════════════════════════ -->
  <section class="daily-tasks py-6">
    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <span class="text-2xl">✅</span> Günlük Rutinim
          </h2>
          <p class="text-sm text-slate-500 mt-1">Sağlıklı yaşam hedeflerin için bugün yapman gerekenler</p>
        </div>
        <div class="flex items-center gap-4">
          <div class="text-right">
            <span class="text-2xl font-black text-indigo-600">{{ dailyTasks.filter(t => t.completed).length }}/{{ dailyTasks.length }}</span>
            <p class="text-xs text-slate-400 font-medium">Tamamlandı</p>
          </div>
          <button 
            @click="showAddTaskModal = true"
            class="bg-indigo-50 text-indigo-600 p-3 rounded-xl hover:bg-indigo-100 transition-colors"
            title="Yeni Görev Ekle"
          >
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div 
          v-for="task in dailyTasks" 
          :key="task.id"
          @click="toggleTask(task.id)"
          class="relative overflow-hidden rounded-xl border-2 p-4 cursor-pointer transition-all duration-300 group"
          :class="task.completed ? 'bg-slate-50 border-slate-200' : 'bg-white border-slate-100 hover:border-indigo-200 hover:shadow-md'"
        >
          <div class="flex items-start justify-between mb-2">
            <span class="text-2xl">{{ task.icon }}</span>
            <div 
              class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors"
              :class="task.completed ? 'bg-green-500 border-green-500' : 'border-slate-200 group-hover:border-indigo-300'"
            >
              <svg v-if="task.completed" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
          </div>
          <h3 
            class="font-bold text-slate-900 mb-1 transition-colors"
            :class="{ 'text-slate-400 line-through': task.completed }"
          >
            {{ task.title }}
          </h3>
          <span class="text-xs font-medium px-2 py-1 rounded-full inline-block" :class="task.color">
            {{ task.time }}
          </span>
        </div>
      </div>
    </div>

    <!-- Add Task Modal -->
    <div v-if="showAddTaskModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl animate-fade-in-up">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-xl font-bold text-slate-900">Yeni Görev Ekle</h3>
          <button @click="showAddTaskModal = false" class="text-slate-400 hover:text-slate-600">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Görev Adı</label>
            <input 
              v-model="newTask.title"
              type="text" 
              class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all"
              placeholder="Örn: 30dk Yürüyüş"
            >
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Zaman</label>
              <input 
                v-model="newTask.time"
                type="text" 
                class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all"
                placeholder="Örn: Sabah"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">İkon (Emoji)</label>
              <input 
                v-model="newTask.icon"
                type="text" 
                class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all"
                placeholder="Örn: 🏃‍♂️"
              >
            </div>
          </div>

          <button 
            @click="addNewTask"
            class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200 mt-2"
          >
            Görevi Ekle
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useMarketplaceStore } from '@/stores/marketplaceStore'

// Store
const marketplaceStore = useMarketplaceStore()

// State
const dailyTasks = computed(() => marketplaceStore.tasks)
const showAddTaskModal = ref(false)
const newTask = ref({ title: '', time: '', icon: '📌' })

// Methods
const fetchTasks = async () => {
  if (dailyTasks.value.length === 0) {
    await marketplaceStore.fetchTasks()
  }
}

const toggleTask = async (id: number) => {
  await marketplaceStore.toggleTask(id)
}

const addNewTask = async () => {
  if (!newTask.value.title) return
  
  const success = await marketplaceStore.addTask(newTask.value)
  if (success) {
    newTask.value = { title: '', time: '', icon: '📌' }
    showAddTaskModal.value = false
  }
}
// Lifecycle
onMounted(() => {
  fetchTasks()
})
</script>
