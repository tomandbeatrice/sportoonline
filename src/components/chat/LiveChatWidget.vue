<script setup lang="ts">
import { ref, computed, nextTick, watch, onMounted } from 'vue'
import { useChatBotStore } from '@/stores/chatBotStore'
import { storeToRefs } from 'pinia'
import axios from 'axios'

const isOpen = ref(false)
const isMinimized = ref(false)
const currentView = ref<'home' | 'conversations' | 'chat' | 'support'>('home')
const messagesContainer = ref<HTMLElement | null>(null)
const userInput = ref('')

// Chat Bot Store
const chatBotStore = useChatBotStore()
const { messages: botMessages, isTyping } = storeToRefs(chatBotStore)

// State
const unreadCount = ref(0)
const isAgentOnline = ref(true)
const conversations = ref<any[]>([])

const quickFaqs = [
  { id: 1, question: 'Kargo takibi nasıl yapabilirim?', action: 'track_order' },
  { id: 2, question: 'İade koşulları nelerdir?', action: 'return_info' },
  { id: 3, question: 'Ödeme seçenekleri', action: 'payment_help' }
]

// Fetch conversations from API
const fetchConversations = async () => {
  try {
    const { data } = await axios.get('/api/messages/conversations')
    conversations.value = data.conversations || data || []
    unreadCount.value = conversations.value.reduce((sum: number, c: any) => sum + (c.unread || 0), 0)
  } catch (error) {
    // Fallback demo data
    conversations.value = [
      {
        id: 1,
        name: 'Nike Official Store',
        avatar: 'https://logo.clearbit.com/nike.com',
        lastMessage: 'Siparişiniz kargoya verildi 📦',
        lastMessageTime: new Date(Date.now() - 1000 * 60 * 30),
        unread: 1
      }
    ]
    unreadCount.value = 1
  }
}

onMounted(() => {
  fetchConversations()
})

const activeConversation = ref<any>(null)

// Computed
const currentMessages = computed(() => {
  if (currentView.value === 'support') {
    return botMessages.value
  }
  return activeConversation.value?.messages || [] // Mock messages for seller chat
})

// Methods
const openChat = () => {
  isOpen.value = true
  isMinimized.value = false
}

const closeChat = () => {
  isOpen.value = false
}

const toggleMinimize = () => {
  isMinimized.value = !isMinimized.value
}

const startSupportChat = () => {
  currentView.value = 'support'
  if (botMessages.value.length === 0) {
    chatBotStore.resetChat()
  }
  scrollToBottom()
}

const openConversation = async (conv: any) => {
  activeConversation.value = conv
  currentView.value = 'chat'
  
  // Fetch messages from API
  if (!conv.messages) {
    try {
      const { data } = await axios.get(`/api/messages/conversations/${conv.id}`)
      conv.messages = data.messages || data || []
    } catch (error) {
      // Fallback demo messages
      conv.messages = [
        { id: 1, text: 'Merhaba, ürün hakkında bilgi alabilir miyim?', sender: 'user', timestamp: new Date(Date.now() - 100000) },
        { id: 2, text: 'Tabii, hangi ürün?', sender: 'agent', timestamp: new Date(Date.now() - 90000) },
        { id: 3, text: conv.lastMessage, sender: 'agent', timestamp: conv.lastMessageTime }
      ]
    }
  }
  scrollToBottom()
}

const selectFaq = (faq: any) => {
  startSupportChat()
  chatBotStore.addMessage({ text: faq.question, sender: 'user' })
  chatBotStore.handleAction(faq.action)
}

const sendMessage = () => {
  if (!userInput.value.trim()) return

  if (currentView.value === 'support') {
    chatBotStore.addMessage({ text: userInput.value, sender: 'user' })
    // Simple echo bot for now if not an action
    setTimeout(() => {
      chatBotStore.addMessage({ 
        text: 'Şu an otomatik yanıt sistemindesiniz. Müşteri temsilcisine bağlanmak için "Canlı Destek" seçeneğini kullanabilirsiniz.', 
        sender: 'bot' 
      })
    }, 1000)
  } else {
    // Seller chat logic
    activeConversation.value.messages.push({
      id: Date.now(),
      text: userInput.value,
      sender: 'user',
      timestamp: new Date()
    })
  }
  
  userInput.value = ''
  scrollToBottom()
}

const handleOptionClick = (option: any) => {
  chatBotStore.addMessage({ text: option.label, sender: 'user' })
  chatBotStore.handleAction(option.action)
}

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

const formatTime = (date: Date) => {
  return new Intl.DateTimeFormat('tr-TR', { hour: '2-digit', minute: '2-digit' }).format(date)
}

watch(currentMessages, () => {
  scrollToBottom()
}, { deep: true })
</script>

<template>
  <Teleport to="body">
    <!-- Chat Button -->
    <button 
      v-if="!isOpen"
      @click="openChat"
      class="fixed bottom-6 right-6 w-16 h-16 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full shadow-2xl flex items-center justify-center text-2xl hover:scale-110 transition-transform z-50"
    >
      💬
      <span 
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <!-- Chat Widget -->
    <Transition name="chat">
      <div 
        v-if="isOpen"
        class="fixed bottom-6 right-6 w-96 h-[600px] bg-white rounded-3xl shadow-2xl overflow-hidden z-50 flex flex-col font-sans"
        :class="{ 'h-auto': isMinimized }"
      >
        <!-- Header -->
        <div 
          class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-4 cursor-pointer"
          @click="isMinimized ? toggleMinimize() : null"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-xl backdrop-blur-sm">
                {{ currentView === 'support' ? '🤖' : '💬' }}
              </div>
              <div>
                <div class="font-bold text-lg">
                  {{ currentView === 'support' ? 'Sporto Asistan' : activeConversation?.name || 'Mesajlar' }}
                </div>
                <div class="text-xs opacity-90 flex items-center gap-1">
                  <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                  {{ currentView === 'support' ? 'Otomatik Yanıt' : 'Çevrimiçi' }}
                </div>
              </div>
            </div>
            <div class="flex items-center gap-1">
              <button 
                @click.stop="toggleMinimize"
                class="p-2 hover:bg-white/20 rounded-lg transition"
              >
                {{ isMinimized ? '◻️' : '—' }}
              </button>
              <button 
                @click.stop="closeChat"
                class="p-2 hover:bg-white/20 rounded-lg transition"
              >
                ✕
              </button>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div v-if="!isMinimized" class="flex-1 flex flex-col overflow-hidden bg-slate-50">
          
          <!-- Welcome Screen -->
          <div v-if="currentView === 'home'" class="flex-1 p-5 overflow-y-auto">
            <div class="text-center mb-8 mt-4">
              <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">
                👋
              </div>
              <h3 class="text-xl font-bold text-slate-900 mb-2">Merhaba!</h3>
              <p class="text-slate-600">Size nasıl yardımcı olabiliriz?</p>
            </div>

            <div class="space-y-3">
              <button 
                @click="startSupportChat"
                class="w-full p-4 bg-white border border-indigo-100 rounded-2xl text-left hover:shadow-lg hover:border-indigo-300 transition group"
              >
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition">🤖</div>
                  <div>
                    <div class="font-bold text-slate-900">Sporto Asistan</div>
                    <div class="text-sm text-slate-500">7/24 Anında Yanıt</div>
                  </div>
                </div>
              </button>

              <button 
                @click="currentView = 'conversations'"
                class="w-full p-4 bg-white border border-slate-200 rounded-2xl text-left hover:shadow-lg hover:border-indigo-300 transition group"
              >
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition">💬</div>
                  <div>
                    <div class="font-bold text-slate-900">Satıcı Mesajları</div>
                    <div class="text-sm text-slate-500">{{ conversations.length }} aktif görüşme</div>
                  </div>
                </div>
              </button>
            </div>

            <!-- FAQ Quick Links -->
            <div class="mt-8">
              <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 ml-1">Sık Sorulanlar</h4>
              <div class="space-y-2">
                <button 
                  v-for="faq in quickFaqs"
                  :key="faq.id"
                  @click="selectFaq(faq)"
                  class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-left text-sm hover:border-indigo-400 hover:text-indigo-600 transition flex items-center justify-between group"
                >
                  {{ faq.question }}
                  <span class="opacity-0 group-hover:opacity-100 transition">→</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Conversations List -->
          <div v-if="currentView === 'conversations'" class="flex-1 overflow-y-auto bg-white">
            <div class="sticky top-0 bg-white/80 backdrop-blur-sm border-b border-slate-100 p-2 z-10">
              <button 
                @click="currentView = 'home'"
                class="px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 rounded-lg flex items-center gap-2 transition"
              >
                ← Geri Dön
              </button>
            </div>
            
            <div v-if="conversations.length === 0" class="p-12 text-center">
              <div class="text-5xl mb-4 opacity-20">💬</div>
              <p class="text-slate-500">Henüz görüşme yok</p>
            </div>

            <div 
              v-for="conv in conversations"
              :key="conv.id"
              @click="openConversation(conv)"
              class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 cursor-pointer border-b border-slate-50 transition"
            >
              <div class="relative">
                <img :src="conv.avatar" :alt="conv.name" class="w-12 h-12 rounded-full object-cover border border-slate-200" />
                <div v-if="conv.unread" class="absolute -top-1 -right-1 w-5 h-5 bg-indigo-600 text-white text-xs font-bold rounded-full flex items-center justify-center border-2 border-white">
                  {{ conv.unread }}
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                  <span class="font-bold text-slate-900">{{ conv.name }}</span>
                  <span class="text-xs text-slate-400">{{ formatTime(conv.lastMessageTime) }}</span>
                </div>
                <p class="text-sm text-slate-500 truncate" :class="{ 'font-semibold text-slate-800': conv.unread }">
                  {{ conv.lastMessage }}
                </p>
              </div>
            </div>
          </div>

          <!-- Chat View -->
          <div v-if="currentView === 'chat' || currentView === 'support'" class="flex-1 flex flex-col overflow-hidden bg-slate-100">
            <div class="bg-white border-b border-slate-200 p-2 shadow-sm z-10">
              <button 
                @click="currentView = currentView === 'support' ? 'home' : 'conversations'"
                class="px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 rounded-lg flex items-center gap-2 transition"
              >
                ← Geri
              </button>
            </div>

            <!-- Messages -->
            <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
              <div 
                v-for="msg in currentMessages"
                :key="msg.id"
                class="flex flex-col"
                :class="msg.sender === 'user' ? 'items-end' : 'items-start'"
              >
                <!-- Message Bubble -->
                <div 
                  class="max-w-[85%] p-3 rounded-2xl shadow-sm text-sm relative group"
                  :class="[
                    msg.sender === 'user' 
                      ? 'bg-indigo-600 text-white rounded-tr-none' 
                      : 'bg-white text-slate-800 rounded-tl-none border border-slate-200'
                  ]"
                >
                  <!-- Text Content -->
                  <div v-if="msg.type === 'text' || !msg.type">{{ msg.text }}</div>

                  <!-- Order Card -->
                  <div v-else-if="msg.type === 'order'" class="min-w-[200px]">
                    <div class="font-bold border-b border-slate-100 pb-2 mb-2">{{ msg.text }}</div>
                    <div class="space-y-2">
                      <div class="flex justify-between">
                        <span class="text-slate-500">Sipariş No:</span>
                        <span class="font-mono">{{ msg.data.orderId }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-slate-500">Durum:</span>
                        <span class="text-green-600 font-bold">{{ msg.data.status }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-slate-500">Teslimat:</span>
                        <span>{{ msg.data.estimatedDelivery }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Options -->
                  <div v-else-if="msg.type === 'options'" class="space-y-2">
                    <div class="mb-3">{{ msg.text }}</div>
                    <div class="flex flex-col gap-2">
                      <button 
                        v-for="opt in msg.data.options" 
                        :key="opt.action"
                        @click="handleOptionClick(opt)"
                        class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg text-sm font-medium hover:bg-indigo-100 transition text-left"
                      >
                        {{ opt.label }}
                      </button>
                    </div>
                  </div>

                  <!-- Time -->
                  <div 
                    class="text-[10px] mt-1 opacity-0 group-hover:opacity-70 transition absolute -bottom-5 w-max"
                    :class="msg.sender === 'user' ? 'right-0' : 'left-0'"
                  >
                    {{ formatTime(new Date(msg.timestamp)) }}
                  </div>
                </div>
              </div>

              <!-- Typing Indicator -->
              <div v-if="isTyping && currentView === 'support'" class="flex items-start">
                <div class="bg-white p-3 rounded-2xl rounded-tl-none border border-slate-200 shadow-sm">
                  <div class="flex gap-1">
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce"></span>
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce delay-75"></span>
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce delay-150"></span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Input Area -->
            <div class="p-4 bg-white border-t border-slate-200">
              <form @submit.prevent="sendMessage" class="flex gap-2">
                <input 
                  v-model="userInput"
                  type="text" 
                  placeholder="Bir mesaj yazın..." 
                  class="flex-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                >
                <button 
                  type="submit"
                  :disabled="!userInput.trim()"
                  class="p-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition shadow-lg shadow-indigo-200"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                    <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                  </svg>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.chat-enter-active,
.chat-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.chat-enter-from,
.chat-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}
</style>
