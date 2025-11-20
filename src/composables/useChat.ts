/**
 * Real-time Chat Composable
 * Handle live chat functionality with presence tracking
 */

import { ref, computed, onUnmounted } from 'vue'
import { websocket } from '@/services/websocket.js'
import axios from 'axios'

export interface ChatMessage {
  id: string
  roomId: string
  userId: number
  userName: string
  userAvatar?: string
  message: string
  timestamp: Date
  type: 'text' | 'image' | 'file'
  fileUrl?: string
  fileName?: string
  read: boolean
}

export interface ChatUser {
  id: number
  name: string
  avatar?: string
  online: boolean
  lastSeen?: Date
}

export interface ChatRoom {
  id: string
  name: string
  type: 'direct' | 'group' | 'support'
  participants: ChatUser[]
  lastMessage?: ChatMessage
  unreadCount: number
}

export function useChat(roomId?: string) {
  const messages = ref<ChatMessage[]>([])
  const rooms = ref<ChatRoom[]>([])
  const currentRoom = ref<ChatRoom | null>(null)
  const usersTyping = ref<ChatUser[]>([])
  const onlineUsers = ref<ChatUser[]>([])
  const isConnected = ref(false)
  const isLoading = ref(false)
  const typingTimeout = ref<number | null>(null)

  /**
   * Initialize chat
   */
  function init(initialRoomId?: string) {
    if (!websocket.connected) {
      websocket.init()
    }

    isConnected.value = true

    // Load rooms
    loadRooms()

    // Join room if provided
    if (initialRoomId || roomId) {
      joinRoom(initialRoomId || roomId!)
    }
  }

  /**
   * Load chat rooms
   */
  async function loadRooms() {
    isLoading.value = true
    try {
      const response = await axios.get('/api/chat/rooms')
      rooms.value = response.data.map((room: any) => ({
        ...room,
        participants: room.participants || [],
        unreadCount: room.unread_count || 0
      }))
    } catch (error) {
      console.error('Failed to load chat rooms:', error)
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Join a chat room
   */
  async function joinRoom(roomId: string) {
    if (!roomId) return

    // Leave current room
    if (currentRoom.value) {
      leaveRoom(currentRoom.value.id)
    }

    isLoading.value = true

    try {
      // Load room details
      const response = await axios.get(`/api/chat/rooms/${roomId}`)
      currentRoom.value = response.data

      // Load messages
      await loadMessages(roomId)

      // Subscribe to WebSocket channel
      websocket.subscribeToChatRoom(roomId, {
        onMessage: handleNewMessage,
        onUserJoined: handleUserJoined,
        onUserLeft: handleUserLeft,
        onTyping: handleUserTyping
      })

      // Mark messages as read
      markRoomAsRead(roomId)
    } catch (error) {
      console.error('Failed to join room:', error)
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Leave current room
   */
  function leaveRoom(roomId: string) {
    if (roomId) {
      websocket.leave(`chat.${roomId}`)
    }
    currentRoom.value = null
    messages.value = []
    usersTyping.value = []
  }

  /**
   * Load messages for a room
   */
  async function loadMessages(roomId: string, offset = 0, limit = 50) {
    try {
      const response = await axios.get(`/api/chat/rooms/${roomId}/messages`, {
        params: { offset, limit }
      })
      
      const newMessages = response.data.map((msg: any) => ({
        id: msg.id,
        roomId: msg.room_id,
        userId: msg.user_id,
        userName: msg.user_name,
        userAvatar: msg.user_avatar,
        message: msg.message,
        timestamp: new Date(msg.created_at),
        type: msg.type || 'text',
        fileUrl: msg.file_url,
        fileName: msg.file_name,
        read: msg.read || false
      }))

      if (offset === 0) {
        messages.value = newMessages
      } else {
        messages.value = [...newMessages, ...messages.value]
      }
    } catch (error) {
      console.error('Failed to load messages:', error)
    }
  }

  /**
   * Send a message
   */
  async function sendMessage(message: string, type: 'text' | 'image' | 'file' = 'text', file?: File) {
    if (!currentRoom.value || !message.trim()) return

    const tempId = `temp-${Date.now()}`
    const currentUser = JSON.parse(localStorage.getItem('user') || '{}')

    // Optimistically add message
    const newMessage: ChatMessage = {
      id: tempId,
      roomId: currentRoom.value.id,
      userId: currentUser.id,
      userName: currentUser.name,
      userAvatar: currentUser.avatar,
      message: message.trim(),
      timestamp: new Date(),
      type,
      read: false
    }

    messages.value.push(newMessage)

    try {
      const formData = new FormData()
      formData.append('message', message.trim())
      formData.append('type', type)
      if (file) {
        formData.append('file', file)
      }

      const response = await axios.post(
        `/api/chat/rooms/${currentRoom.value.id}/messages`,
        formData
      )

      // Replace temp message with real one
      const index = messages.value.findIndex(m => m.id === tempId)
      if (index !== -1) {
        messages.value[index] = {
          ...response.data,
          timestamp: new Date(response.data.created_at)
        }
      }
    } catch (error) {
      console.error('Failed to send message:', error)
      // Remove temp message on error
      messages.value = messages.value.filter(m => m.id !== tempId)
      throw error
    }
  }

  /**
   * Handle new message from WebSocket
   */
  function handleNewMessage(data: any) {
    const message: ChatMessage = {
      id: data.id,
      roomId: data.room_id,
      userId: data.user_id,
      userName: data.user_name,
      userAvatar: data.user_avatar,
      message: data.message,
      timestamp: new Date(data.created_at),
      type: data.type || 'text',
      fileUrl: data.file_url,
      fileName: data.file_name,
      read: false
    }

    // Only add if not already in list (avoid duplicates from optimistic update)
    if (!messages.value.find(m => m.id === message.id)) {
      messages.value.push(message)
    }

    // Update room's last message
    const room = rooms.value.find(r => r.id === message.roomId)
    if (room) {
      room.lastMessage = message
      if (currentRoom.value?.id !== message.roomId) {
        room.unreadCount++
      }
    }

    // Play notification sound if not own message
    const currentUser = JSON.parse(localStorage.getItem('user') || '{}')
    if (message.userId !== currentUser.id) {
      playMessageSound()
    }
  }

  /**
   * Handle user joined
   */
  function handleUserJoined(user: ChatUser) {
    if (!onlineUsers.value.find(u => u.id === user.id)) {
      onlineUsers.value.push(user)
    }
  }

  /**
   * Handle user left
   */
  function handleUserLeft(user: ChatUser) {
    onlineUsers.value = onlineUsers.value.filter(u => u.id !== user.id)
  }

  /**
   * Handle typing indicator
   */
  function handleUserTyping(data: { user: ChatUser; typing: boolean }) {
    if (data.typing) {
      if (!usersTyping.value.find(u => u.id === data.user.id)) {
        usersTyping.value.push(data.user)
      }
    } else {
      usersTyping.value = usersTyping.value.filter(u => u.id !== data.user.id)
    }
  }

  /**
   * Emit typing indicator
   */
  function emitTyping(isTyping: boolean) {
    if (!currentRoom.value) return

    const currentUser = JSON.parse(localStorage.getItem('user') || '{}')
    websocket.whisper(`chat.${currentRoom.value.id}`, 'typing', {
      user: {
        id: currentUser.id,
        name: currentUser.name,
        avatar: currentUser.avatar
      },
      typing: isTyping
    })
  }

  /**
   * Auto-stop typing indicator
   */
  function handleTyping() {
    emitTyping(true)

    if (typingTimeout.value) {
      clearTimeout(typingTimeout.value)
    }

    typingTimeout.value = window.setTimeout(() => {
      emitTyping(false)
    }, 2000)
  }

  /**
   * Mark room as read
   */
  async function markRoomAsRead(roomId: string) {
    try {
      await axios.post(`/api/chat/rooms/${roomId}/read`)
      
      const room = rooms.value.find(r => r.id === roomId)
      if (room) {
        room.unreadCount = 0
      }

      messages.value.forEach(m => {
        if (m.roomId === roomId) {
          m.read = true
        }
      })
    } catch (error) {
      console.error('Failed to mark room as read:', error)
    }
  }

  /**
   * Create a new room
   */
  async function createRoom(participantIds: number[], type: 'direct' | 'group' | 'support' = 'direct', name?: string) {
    try {
      const response = await axios.post('/api/chat/rooms', {
        participant_ids: participantIds,
        type,
        name
      })

      const newRoom = response.data
      rooms.value.unshift(newRoom)

      return newRoom
    } catch (error) {
      console.error('Failed to create room:', error)
      throw error
    }
  }

  /**
   * Play message sound
   */
  function playMessageSound() {
    try {
      const audio = new Audio('/sounds/message.mp3')
      audio.volume = 0.2
      audio.play().catch(() => {
        // Ignore errors
      })
    } catch (error) {
      // Ignore errors
    }
  }

  /**
   * Get typing indicator text
   */
  const typingText = computed(() => {
    if (usersTyping.value.length === 0) return ''
    if (usersTyping.value.length === 1) return `${usersTyping.value[0].name} yazıyor...`
    if (usersTyping.value.length === 2) return `${usersTyping.value[0].name} ve ${usersTyping.value[1].name} yazıyor...`
    return `${usersTyping.value.length} kişi yazıyor...`
  })

  /**
   * Get total unread count
   */
  const totalUnreadCount = computed(() => {
    return rooms.value.reduce((sum, room) => sum + room.unreadCount, 0)
  })

  /**
   * Cleanup on unmount
   */
  onUnmounted(() => {
    if (currentRoom.value) {
      leaveRoom(currentRoom.value.id)
    }
    if (typingTimeout.value) {
      clearTimeout(typingTimeout.value)
    }
  })

  return {
    messages,
    rooms,
    currentRoom,
    usersTyping,
    onlineUsers,
    isConnected,
    isLoading,
    typingText,
    totalUnreadCount,
    init,
    loadRooms,
    joinRoom,
    leaveRoom,
    loadMessages,
    sendMessage,
    createRoom,
    markRoomAsRead,
    handleTyping,
    emitTyping
  }
}
