<template>
  <div class="fixed bottom-20 right-4 z-40 md:bottom-8 md:right-8">
    <!-- Chat Window -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 translate-y-4 scale-95"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 translate-y-4 scale-95"
    >
      <div 
        v-if="isOpen" 
        class="mb-4 flex h-[500px] w-[350px] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
      >
        <!-- Header -->
        <div class="flex items-center justify-between bg-gradient-to-r from-blue-600 to-indigo-600 p-4 text-white">
          <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-xl">
              🤖
            </div>
            <div>
              <h3 class="font-bold">Sporto Asistan</h3>
              <p class="text-xs text-blue-100">Her zaman yardıma hazır</p>
            </div>
          </div>
          <button @click="isOpen = false" class="text-white/80 hover:text-white">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto bg-slate-50 p-4" ref="messagesContainer">
          <div v-for="(msg, index) in messages" :key="index" class="mb-4 flex" :class="msg.isUser ? 'justify-end' : 'justify-start'">
            <div 
              class="max-w-[80%] rounded-2xl px-4 py-2 text-sm shadow-sm"
              :class="msg.isUser ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-slate-700 rounded-bl-none'"
            >
              {{ msg.text }}
            </div>
          </div>
          <div v-if="isTyping" class="flex justify-start">
            <div class="flex items-center gap-1 rounded-2xl rounded-bl-none bg-white px-4 py-3 shadow-sm">
              <span class="h-2 w-2 animate-bounce rounded-full bg-slate-400"></span>
              <span class="h-2 w-2 animate-bounce rounded-full bg-slate-400 delay-75"></span>
              <span class="h-2 w-2 animate-bounce rounded-full bg-slate-400 delay-150"></span>
            </div>
          </div>
        </div>

        <!-- Input -->
        <div class="border-t border-slate-100 bg-white p-4">
          <div class="flex gap-2">
            <input 
              v-model="newMessage" 
              @keyup.enter="sendMessage"
              type="text" 
              placeholder="Bir şeyler yazın..." 
              class="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm focus:border-blue-500 focus:outline-none"
            />
            <button 
              @click="sendMessage"
              class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white transition-colors hover:bg-blue-700"
            >
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Toggle Button -->
    <button 
      @click="isOpen = !isOpen"
      class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/30 transition-transform hover:scale-110 active:scale-95"
    >
      <svg v-if="!isOpen" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
      </svg>
      <svg v-else class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref, nextTick } from 'vue'

const isOpen = ref(false)
const newMessage = ref('')
const isTyping = ref(false)
const messagesContainer = ref<HTMLElement | null>(null)

const messages = ref([
  { text: 'Merhaba! Size nasıl yardımcı olabilirim?', isUser: false }
])

const sendMessage = async () => {
  if (!newMessage.value.trim()) return

  // Add user message
  messages.value.push({ text: newMessage.value, isUser: true })
  const userText = newMessage.value
  newMessage.value = ''
  scrollToBottom()

  // Simulate AI response
  isTyping.value = true
  scrollToBottom()

  setTimeout(() => {
    isTyping.value = false
    let response = 'Bunu henüz anlayamıyorum ama öğreniyorum!'
    
    if (userText.toLowerCase().includes('sipariş')) {
      response = 'Siparişlerinizle ilgili detayları "Hesabım > Siparişlerim" sayfasından görebilirsiniz.'
    } else if (userText.toLowerCase().includes('yemek')) {
      response = 'Acıktınız mı? Hemen size en yakın restoranları listeleyebilirim.'
    } else if (userText.toLowerCase().includes('merhaba')) {
      response = 'Merhaba! Bugün size nasıl yardımcı olabilirim?'
    }

    messages.value.push({ text: response, isUser: false })
    scrollToBottom()
  }, 1500)
}

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}
</script>
