import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { marketplaceService } from '@/services/marketplaceService'
import type { Task, Campaign, Banner } from '@/types/marketplace'
import logger from '@/utils/logger'

export const useMarketplaceStore = defineStore('marketplace', () => {
  // State
  const tasks = ref<Task[]>([])
  const campaigns = ref<Campaign[]>([])
  const banners = ref<Banner[]>([])
  
  const loadingTasks = ref(false)
  const loadingCampaigns = ref(false)
  const loadingBanners = ref(false)

  // Getters
  const completedTasksCount = computed(() => tasks.value.filter((t: Task) => t.completed).length)
  const activeCampaigns = computed(() => campaigns.value) // Can add filter logic if needed
  const healthyLivingBanner = computed(() => banners.value.find((b: Banner) => b.position === 'healthy_living') || null)

  // Actions
  async function fetchTasks() {
    loadingTasks.value = true
    try {
      const response = await marketplaceService.getUserTasks()
      const tasksData = response.data as any[]
      tasks.value = tasksData.map((task: any) => ({
        id: task.id,
        title: task.title,
        time: task.due_date ? new Date(task.due_date).toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' }) : 'Gün Boyu',
        completed: task.is_completed ?? false,
        icon: task.icon || '📌', 
        color: (task.is_completed ?? false) ? 'bg-slate-50 text-slate-400' : 'bg-indigo-100 text-indigo-600'
      }))
      logger.log('Tasks fetched:', tasks.value.length)
    } catch (error) {
      logger.error('Error fetching tasks:', error)
      // Keep existing tasks if fetch fails
    } finally {
      loadingTasks.value = false
    }
  }

  async function toggleTask(id: number) {
    const task = tasks.value.find((t: Task) => t.id === id)
    if (task) {
      const originalStatus = task.completed
      task.completed = !task.completed
      // Also update color
      task.color = task.completed ? 'bg-slate-50 text-slate-400' : 'bg-indigo-100 text-indigo-600'
      
      try {
        await marketplaceService.updateUserTask(id, {
          completed: task.completed
        })
        logger.log('Task updated:', id, task.completed)
      } catch (error) {
        logger.error('Error updating task:', error)
        task.completed = originalStatus
        task.color = originalStatus ? 'bg-slate-50 text-slate-400' : 'bg-indigo-100 text-indigo-600'
      }
    }
  }

  async function addTask(newTask: { title: string, time: string, icon: string }) {
    try {
      const response = await marketplaceService.createUserTask({
        title: newTask.title,
        due_date: newTask.time ? new Date().toISOString().split('T')[0] : undefined
      } as any)
      
      const createdTask = response.data as any
      tasks.value.push({
        id: createdTask.id,
        title: createdTask.title,
        time: newTask.time || 'Gün Boyu',
        completed: false,
        icon: newTask.icon,
        color: 'bg-indigo-100 text-indigo-600'
      })
      logger.log('Task added:', createdTask.id)
      return true
    } catch (error) {
      logger.error('Error adding task:', error)
      return false
    }
  }

  async function fetchCampaigns() {
    loadingCampaigns.value = true
    try {
      const response = await marketplaceService.getActiveCampaigns()
      const campaignsData = response.data as any[]
      campaigns.value = campaignsData.map((campaign: any) => ({
        id: campaign.id,
        title: campaign.name,
        description: campaign.description || '',
        date: new Date(campaign.end_date).toLocaleDateString('tr-TR', { day: 'numeric', month: 'short' }) + ' Bitiş',
        status: 'Aktif',
        color: 'bg-indigo-100 text-indigo-700',
        icon: '🏷️'
      }))
    } catch (error) {
      console.error('Error fetching campaigns:', error)
    } finally {
      loadingCampaigns.value = false
    }
  }

  async function fetchBanners() {
    loadingBanners.value = true
    try {
      const response = await marketplaceService.getBanners()
      banners.value = response.data
    } catch (error) {
      console.error('Error fetching banners:', error)
    } finally {
      loadingBanners.value = false
    }
  }

  return {
    tasks,
    campaigns,
    banners,
    loadingTasks,
    loadingCampaigns,
    loadingBanners,
    completedTasksCount,
    activeCampaigns,
    healthyLivingBanner,
    fetchTasks,
    toggleTask,
    addTask,
    fetchCampaigns,
    fetchBanners
  }
})
