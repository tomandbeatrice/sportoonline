<template>
  <div class="flex flex-col h-full bg-white rounded-lg shadow-lg overflow-hidden">
    <!-- Header -->
    <div class="p-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <button
            v-if="currentRoom"
            @click="handleBack"
            class="p-1 hover:bg-white/20 rounded-lg transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <h2 class="text-lg font-semibold">
            {{ currentRoom ? currentRoom.name : 'Mesajlar' }}
          </h2>
        </div>
        <div class="flex items-center gap-2">
          <span v-if="totalUnreadCount > 0" class="px-2 py-1 bg-white/20 rounded-full text-xs">
            {{ totalUnreadCount }} okunmamış
          </span>
          <button
            @click="$emit('close')"
            class="p-1 hover:bg-white/20 rounded-lg transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      <p v-if="onlineUsers.length > 0 && currentRoom" class="text-sm text-white/80 mt-1">
        {{ onlineUsers.length }} kişi çevrimiçi
      </p>
    </div>

    <!-- Room List -->
    <div v-if="!currentRoom" class="flex-1 overflow-y-auto">
      <div v-if="isLoading" class="p-8 text-center text-gray-500">
        <svg class="animate-spin mx-auto w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="mt-2">Yükleniyor...</p>
      </div>

      <div v-else-if="rooms.length === 0" class="p-8 text-center text-gray-500">
        <svg class="mx-auto w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <p>Henüz sohbet yok</p>
      </div>

      <div v-else class="divide-y divide-gray-100">
        <button
          v-for="room in rooms"
          :key="room.id"
          @click="handleJoinRoom(room.id)"
          class="w-full p-4 flex items-start gap-3 hover:bg-gray-50 transition-colors text-left"
        >
          <!-- Avatar -->
          <div class="relative flex-shrink-0">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold">
              {{ getInitials(room.name) }}
            </div>
            <span
              v-if="room.unreadCount > 0"
              class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"
            >
              {{ room.unreadCount > 9 ? '9+' : room.unreadCount }}
            </span>
          </div>

          <!-- Content -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-1">
              <h3 class="font-semibold text-gray-900 truncate">{{ room.name }}</h3>
              <span v-if="room.lastMessage" class="text-xs text-gray-500">
                {{ formatTime(room.lastMessage.timestamp) }}
              </span>
            </div>
            <p class="text-sm text-gray-600 truncate">
              {{ room.lastMessage ? room.lastMessage.message : 'Henüz mesaj yok' }}
            </p>
          </div>
        </button>
      </div>
    </div>

    <!-- Chat Room -->
    <div v-else class="flex-1 flex flex-col overflow-hidden">
      <!-- Messages -->
      <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
        <div
          v-for="message in messages"
          :key="message.id"
          class="flex"
          :class="isOwnMessage(message) ? 'justify-end' : 'justify-start'"
        >
          <div
            class="max-w-[70%] rounded-lg p-3"
            :class="isOwnMessage(message) 
              ? 'bg-blue-600 text-white' 
              : 'bg-gray-100 text-gray-900'"
          >
            <p v-if="!isOwnMessage(message)" class="text-xs font-semibold mb-1 opacity-70">
              {{ message.userName }}
            </p>
            <p class="text-sm break-words">{{ message.message }}</p>
            <p class="text-xs mt-1 opacity-70">
              {{ formatTime(message.timestamp) }}
            </p>
          </div>
        </div>

        <!-- Typing Indicator -->
        <div v-if="typingText" class="flex justify-start">
          <div class="bg-gray-100 rounded-lg p-3">
            <p class="text-sm text-gray-600 italic">{{ typingText }}</p>
          </div>
        </div>
      </div>

      <!-- Message Input -->
      <div class="p-4 border-t border-gray-200">
        <form @submit.prevent="handleSendMessage" class="flex gap-2">
          <input
            v-model="messageInput"
            @input="handleTypingInput"
            type="text"
            placeholder="Mesajınızı yazın..."
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <button
            type="submit"
            :disabled="!messageInput.trim() || isLoading"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, nextTick } from 'vue'
import { useChat } from '@/composables'

const emit = defineEmits(['close'])

const {
  messages,
  rooms,
  currentRoom,
  usersTyping,
  onlineUsers,
  isLoading,
  typingText,
  totalUnreadCount,
  init,
  joinRoom,
  leaveRoom,
  sendMessage,
  handleTyping
} = useChat()

const messageInput = ref('')
const messagesContainer = ref<HTMLElement | null>(null)

onMounted(() => {
  init()
})

// Auto-scroll to bottom when new messages arrive
watch(messages, async () => {
  await nextTick()
  scrollToBottom()
})

function scrollToBottom() {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

async function handleJoinRoom(roomId: string) {
  await joinRoom(roomId)
  await nextTick()
  scrollToBottom()
}

function handleBack() {
  if (currentRoom.value) {
    leaveRoom(currentRoom.value.id)
  }
}

async function handleSendMessage() {
  if (!messageInput.value.trim()) return

  try {
    await sendMessage(messageInput.value)
    messageInput.value = ''
  } catch (error) {
    console.error('Failed to send message:', error)
  }
}

function handleTypingInput() {
  handleTyping()
}

function isOwnMessage(message: any) {
  const currentUser = JSON.parse(localStorage.getItem('user') || '{}')
  return message.userId === currentUser.id
}

function getInitials(name: string) {
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

function formatTime(timestamp: Date) {
  const now = new Date()
  const messageTime = new Date(timestamp)
  const diff = now.getTime() - messageTime.getTime()
  const seconds = Math.floor(diff / 1000)
  const minutes = Math.floor(seconds / 60)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)

  if (seconds < 60) return 'Az önce'
  if (minutes < 60) return `${minutes}dk`
  if (hours < 24) return `${hours}sa`
  if (days < 7) return `${days}g`

  return messageTime.toLocaleDateString('tr-TR', {
    day: 'numeric',
    month: 'short'
  })
}
</script>
