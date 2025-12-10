import { defineStore } from 'pinia'
import { ref } from 'vue'

export interface ChatMessage {
  id: string
  text: string
  sender: 'user' | 'bot' | 'agent'
  timestamp: Date
  type?: 'text' | 'product' | 'order' | 'options'
  data?: any
}

export const useChatBotStore = defineStore('chatBot', () => {
  const messages = ref<ChatMessage[]>([])
  const isTyping = ref(false)

  const initialMessage: ChatMessage = {
    id: 'welcome',
    text: 'Merhaba! Ben SportoOnline Asistanı. Size nasıl yardımcı olabilirim?',
    sender: 'bot',
    timestamp: new Date(),
    type: 'options',
    data: {
      options: [
        { label: '📦 Siparişim nerede?', action: 'track_order' },
        { label: '↩️ İade işlemleri', action: 'return_info' },
        { label: '💳 Ödeme sorunları', action: 'payment_help' },
        { label: '👤 Canlı desteğe bağlan', action: 'connect_agent' }
      ]
    }
  }

  const addMessage = (msg: Partial<ChatMessage>) => {
    messages.value.push({
      id: Math.random().toString(36).substring(7),
      timestamp: new Date(),
      sender: 'bot',
      type: 'text',
      ...msg
    } as ChatMessage)
  }

  const handleAction = async (action: string) => {
    // Add user selection as message
    // This part is usually handled by the UI calling addMessage for user first
    
    isTyping.value = true
    
    // Simulate AI delay
    setTimeout(() => {
      isTyping.value = false
      
      switch (action) {
        case 'track_order':
          addMessage({
            text: 'Son siparişlerinizi kontrol ediyorum...',
            sender: 'bot'
          })
          setTimeout(() => {
            addMessage({
              text: 'İşte son siparişiniz:',
              type: 'order',
              data: {
                orderId: '#12345',
                status: 'Kargoda',
                estimatedDelivery: 'Yarın'
              }
            })
          }, 1000)
          break
          
        case 'return_info':
          addMessage({
            text: 'İade işlemleri için sipariş detay sayfasından "İade Talebi Oluştur" butonunu kullanabilirsiniz. İade süresi teslimattan itibaren 14 gündür.',
            sender: 'bot'
          })
          break
          
        case 'connect_agent':
          addMessage({
            text: 'Sizi müşteri temsilcisine aktarıyorum, lütfen bekleyin...',
            sender: 'bot'
          })
          // Trigger agent connection logic here
          break
          
        default:
          addMessage({
            text: 'Anlaşılmadı, lütfen tekrar deneyin.',
            sender: 'bot'
          })
      }
    }, 1000)
  }

  const resetChat = () => {
    messages.value = [initialMessage]
  }

  // Initialize
  if (messages.value.length === 0) {
    resetChat()
  }

  return {
    messages,
    isTyping,
    addMessage,
    handleAction,
    resetChat
  }
})
