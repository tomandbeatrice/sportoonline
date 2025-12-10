<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="max-w-6xl mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">💬 Mesajlarım</h1>
        <p class="text-slate-500">Satıcılar ve destek ekibi ile iletişim</p>
      </div>

      <div class="flex gap-6 h-[calc(100vh-200px)]">
        <!-- Conversations List -->
        <div class="w-80 bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col">
          <!-- Search -->
          <div class="p-4 border-b border-slate-100">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Görüşme ara..."
              class="w-full px-4 py-2 bg-slate-100 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <!-- Tabs -->
          <div class="flex border-b border-slate-100">
            <button 
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              class="flex-1 py-3 text-sm font-medium transition"
              :class="activeTab === tab.id ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-slate-500'"
            >
              {{ tab.name }}
              <span v-if="tab.count" class="ml-1 px-1.5 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">
                {{ tab.count }}
              </span>
            </button>
          </div>

          <!-- Conversation List -->
          <div class="flex-1 overflow-y-auto">
            <div 
              v-for="conv in filteredConversations"
              :key="conv.id"
              @click="selectConversation(conv)"
              class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 cursor-pointer transition border-b border-slate-50"
              :class="{ 'bg-indigo-50': selectedConversation?.id === conv.id }"
            >
              <div class="relative">
                <img 
                  :src="conv.avatar" 
                  :alt="conv.name"
                  class="w-12 h-12 rounded-full object-cover"
                />
                <div 
                  v-if="conv.online"
                  class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"
                ></div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <span class="font-medium text-slate-900 truncate">{{ conv.name }}</span>
                  <span class="text-xs text-slate-400">{{ formatTime(conv.lastMessageTime) }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <p class="text-sm text-slate-500 truncate">{{ conv.lastMessage }}</p>
                  <span 
                    v-if="conv.unreadCount"
                    class="flex-shrink-0 w-5 h-5 bg-indigo-600 text-white text-xs rounded-full flex items-center justify-center"
                  >
                    {{ conv.unreadCount }}
                  </span>
                </div>
              </div>
            </div>

            <div v-if="filteredConversations.length === 0" class="p-8 text-center">
              <div class="text-4xl mb-3">💬</div>
              <p class="text-slate-500">Görüşme bulunamadı</p>
            </div>
          </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col">
          <template v-if="selectedConversation">
            <!-- Chat Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
              <div class="flex items-center gap-4">
                <img 
                  :src="selectedConversation.avatar" 
                  :alt="selectedConversation.name"
                  class="w-10 h-10 rounded-full object-cover"
                />
                <div>
                  <div class="font-bold text-slate-900">{{ selectedConversation.name }}</div>
                  <div class="text-sm text-slate-500">
                    {{ selectedConversation.online ? '🟢 Çevrimiçi' : `Son görülme: ${formatTime(selectedConversation.lastSeen)}` }}
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button class="p-2 hover:bg-slate-100 rounded-lg transition" title="Ara">📞</button>
                <button class="p-2 hover:bg-slate-100 rounded-lg transition" title="Video">📹</button>
                <button class="p-2 hover:bg-slate-100 rounded-lg transition" title="Bilgi">ℹ️</button>
                <button class="p-2 hover:bg-slate-100 rounded-lg transition" title="Daha fazla">⋮</button>
              </div>
            </div>

            <!-- Messages -->
            <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-4">
              <template v-for="(messages, date) in groupedMessages" :key="date">
                <div class="flex items-center gap-3 my-4">
                  <div class="flex-1 h-px bg-slate-200"></div>
                  <span class="text-xs text-slate-400 font-medium">{{ date }}</span>
                  <div class="flex-1 h-px bg-slate-200"></div>
                </div>
                
                <div 
                  v-for="msg in messages"
                  :key="msg.id"
                  class="flex"
                  :class="msg.sender === 'me' ? 'justify-end' : 'justify-start'"
                >
                  <div class="max-w-[60%]">
                    <div 
                      class="rounded-2xl px-4 py-3"
                      :class="msg.sender === 'me' 
                        ? 'bg-indigo-600 text-white rounded-br-sm' 
                        : 'bg-slate-100 text-slate-900 rounded-bl-sm'"
                    >
                      <!-- Product Card -->
                      <div v-if="msg.product" class="mb-2 p-2 bg-white/10 rounded-xl">
                        <div class="flex items-center gap-3">
                          <img :src="msg.product.image" :alt="msg.product.name" class="w-12 h-12 rounded-lg object-cover" />
                          <div>
                            <div class="text-sm font-medium">{{ msg.product.name }}</div>
                            <div class="text-xs opacity-75">{{ formatPrice(msg.product.price) }}</div>
                          </div>
                        </div>
                      </div>
                      
                      <p>{{ msg.text }}</p>
                      
                      <!-- Attachment -->
                      <div v-if="msg.attachment" class="mt-2">
                        <img 
                          v-if="msg.attachment.type === 'image'"
                          :src="msg.attachment.url" 
                          :alt="`Ek: ${msg.attachment.name || 'resim'}`"
                          class="max-w-full rounded-xl"
                        />
                        <div v-else class="flex items-center gap-2 p-2 bg-black/10 rounded-lg">
                          <span>📄</span>
                          <span class="text-sm">{{ msg.attachment.name }}</span>
                        </div>
                      </div>
                    </div>
                    <div 
                      class="text-xs mt-1 px-2"
                      :class="msg.sender === 'me' ? 'text-right text-slate-400' : 'text-slate-400'"
                    >
                      {{ formatMessageTime(msg.time) }}
                      <span v-if="msg.sender === 'me'" class="ml-1">
                        {{ msg.status === 'read' ? '✓✓' : msg.status === 'delivered' ? '✓✓' : '✓' }}
                      </span>
                    </div>
                  </div>
                </div>
              </template>

              <!-- Typing Indicator -->
              <div v-if="isTyping" class="flex justify-start">
                <div class="bg-slate-100 rounded-2xl rounded-bl-sm px-4 py-3">
                  <div class="flex gap-1">
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce"></span>
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Input -->
            <div class="px-6 py-4 border-t border-slate-100">
              <div class="flex items-end gap-3">
                <button class="p-2 text-slate-400 hover:text-indigo-600 transition">📎</button>
                <button class="p-2 text-slate-400 hover:text-indigo-600 transition">🖼️</button>
                <div class="flex-1 relative">
                  <textarea
                    v-model="messageInput"
                    @keydown.enter.exact.prevent="sendMessage"
                    placeholder="Mesajınızı yazın..."
                    rows="1"
                    class="w-full px-4 py-3 bg-slate-100 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
                  ></textarea>
                </div>
                <button 
                  @click="sendMessage"
                  :disabled="!messageInput.trim()"
                  class="p-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition disabled:opacity-50"
                >
                  ➤
                </button>
              </div>
            </div>
          </template>

          <!-- Empty State -->
          <div v-else class="flex-1 flex items-center justify-center">
            <div class="text-center">
              <div class="text-6xl mb-4">💬</div>
              <h3 class="text-xl font-bold text-slate-900 mb-2">Görüşme Seçin</h3>
              <p class="text-slate-500">Soldan bir görüşme seçerek sohbete başlayın</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, nextTick, watch } from 'vue'

interface Message {
  id: string
  text: string
  sender: 'me' | 'other'
  time: Date
  status: 'sent' | 'delivered' | 'read'
  product?: { name: string; price: number; image: string }
  attachment?: { type: 'image' | 'file'; url: string; name: string }
}

interface Conversation {
  id: string
  name: string
  avatar: string
  type: 'seller' | 'support'
  online: boolean
  lastSeen: Date
  lastMessage: string
  lastMessageTime: Date
  unreadCount: number
  messages: Message[]
}

const searchQuery = ref('')
const activeTab = ref('all')
const messageInput = ref('')
const isTyping = ref(false)
const messagesContainer = ref<HTMLElement | null>(null)
const selectedConversation = ref<Conversation | null>(null)

const conversations = ref<Conversation[]>([
  {
    id: '1',
    name: 'SportMax Store',
    avatar: 'https://via.placeholder.com/100',
    type: 'seller',
    online: true,
    lastSeen: new Date(),
    lastMessage: 'Ürününüz bugün kargoya verilecektir.',
    lastMessageTime: new Date(Date.now() - 1800000),
    unreadCount: 2,
    messages: [
      { id: '1', text: 'Merhaba, Nike Air Max 270 için sipariş verdim.', sender: 'me', time: new Date(Date.now() - 7200000), status: 'read', product: { name: 'Nike Air Max 270', price: 2499, image: 'https://via.placeholder.com/100' } },
      { id: '2', text: 'Merhaba! Siparişiniz hazırlanıyor.', sender: 'other', time: new Date(Date.now() - 5400000), status: 'read' },
      { id: '3', text: 'Ne zaman kargoya verilecek?', sender: 'me', time: new Date(Date.now() - 3600000), status: 'read' },
      { id: '4', text: 'Ürününüz bugün kargoya verilecektir.', sender: 'other', time: new Date(Date.now() - 1800000), status: 'read' },
    ]
  },
  {
    id: '2',
    name: 'FitLife Spor',
    avatar: 'https://via.placeholder.com/100',
    type: 'seller',
    online: false,
    lastSeen: new Date(Date.now() - 3600000),
    lastMessage: 'Teşekkür ederiz, iyi günler!',
    lastMessageTime: new Date(Date.now() - 86400000),
    unreadCount: 0,
    messages: [
      { id: '1', text: 'Ürün hakkında bilgi alabilir miyim?', sender: 'me', time: new Date(Date.now() - 172800000), status: 'read' },
      { id: '2', text: 'Tabii! Hangi ürün hakkında bilgi almak istersiniz?', sender: 'other', time: new Date(Date.now() - 169200000), status: 'read' },
      { id: '3', text: 'Teşekkürler, sorumu yanıtladınız.', sender: 'me', time: new Date(Date.now() - 90000000), status: 'read' },
      { id: '4', text: 'Teşekkür ederiz, iyi günler!', sender: 'other', time: new Date(Date.now() - 86400000), status: 'read' },
    ]
  },
  {
    id: '3',
    name: 'Destek Ekibi',
    avatar: 'https://via.placeholder.com/100',
    type: 'support',
    online: true,
    lastSeen: new Date(),
    lastMessage: 'İade talebiniz onaylandı.',
    lastMessageTime: new Date(Date.now() - 43200000),
    unreadCount: 1,
    messages: [
      { id: '1', text: 'İade yapmak istiyorum.', sender: 'me', time: new Date(Date.now() - 86400000), status: 'read' },
      { id: '2', text: 'Tabii, hangi sipariş için iade yapmak istiyorsunuz?', sender: 'other', time: new Date(Date.now() - 82800000), status: 'read' },
      { id: '3', text: 'Sipariş #1234', sender: 'me', time: new Date(Date.now() - 79200000), status: 'read' },
      { id: '4', text: 'İade talebiniz onaylandı.', sender: 'other', time: new Date(Date.now() - 43200000), status: 'read' },
    ]
  }
])

const tabs = computed(() => [
  { id: 'all', name: 'Tümü', count: conversations.value.reduce((sum, c) => sum + c.unreadCount, 0) },
  { id: 'seller', name: 'Satıcılar', count: conversations.value.filter(c => c.type === 'seller').reduce((sum, c) => sum + c.unreadCount, 0) },
  { id: 'support', name: 'Destek', count: conversations.value.filter(c => c.type === 'support').reduce((sum, c) => sum + c.unreadCount, 0) },
])

const filteredConversations = computed(() => {
  let filtered = conversations.value
  
  if (activeTab.value !== 'all') {
    filtered = filtered.filter(c => c.type === activeTab.value)
  }
  
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    filtered = filtered.filter(c => 
      c.name.toLowerCase().includes(q) || 
      c.lastMessage.toLowerCase().includes(q)
    )
  }
  
  return filtered.sort((a, b) => b.lastMessageTime.getTime() - a.lastMessageTime.getTime())
})

const groupedMessages = computed(() => {
  if (!selectedConversation.value) return {}
  
  const groups: Record<string, Message[]> = {}
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  const yesterday = new Date(today)
  yesterday.setDate(yesterday.getDate() - 1)
  
  selectedConversation.value.messages.forEach(msg => {
    const msgDate = new Date(msg.time)
    msgDate.setHours(0, 0, 0, 0)
    
    let key: string
    if (msgDate.getTime() === today.getTime()) {
      key = 'Bugün'
    } else if (msgDate.getTime() === yesterday.getTime()) {
      key = 'Dün'
    } else {
      key = msgDate.toLocaleDateString('tr-TR', { day: 'numeric', month: 'long', year: 'numeric' })
    }
    
    if (!groups[key]) groups[key] = []
    groups[key].push(msg)
  })
  
  return groups
})

const selectConversation = (conv: Conversation) => {
  selectedConversation.value = conv
  conv.unreadCount = 0
  scrollToBottom()
}

const sendMessage = () => {
  if (!messageInput.value.trim() || !selectedConversation.value) return
  
  const newMessage: Message = {
    id: crypto.randomUUID(),
    text: messageInput.value,
    sender: 'me',
    time: new Date(),
    status: 'sent'
  }
  
  selectedConversation.value.messages.push(newMessage)
  selectedConversation.value.lastMessage = messageInput.value
  selectedConversation.value.lastMessageTime = new Date()
  
  messageInput.value = ''
  scrollToBottom()
  
  // Simulate response
  isTyping.value = true
  setTimeout(() => {
    isTyping.value = false
    if (selectedConversation.value) {
      selectedConversation.value.messages.push({
        id: crypto.randomUUID(),
        text: 'Mesajınız alındı, en kısa sürede yanıtlayacağım.',
        sender: 'other',
        time: new Date(),
        status: 'read'
      })
      scrollToBottom()
    }
  }, 2000)
}

const scrollToBottom = async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const formatTime = (date: Date) => {
  const now = new Date()
  const diff = now.getTime() - date.getTime()
  
  if (diff < 60000) return 'Şimdi'
  if (diff < 3600000) return `${Math.floor(diff / 60000)} dk`
  if (diff < 86400000) return date.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' })
  return date.toLocaleDateString('tr-TR', { day: 'numeric', month: 'short' })
}

const formatMessageTime = (date: Date) => {
  return date.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' })
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(price)
}

watch(selectedConversation, () => {
  scrollToBottom()
}, { deep: true })
</script>
