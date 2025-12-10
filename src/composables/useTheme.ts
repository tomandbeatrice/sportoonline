import { computed } from 'vue'
import { useRoute } from 'vue-router'

export type ThemeModule = 'market' | 'food' | 'transport' | 'hotel' | 'career' | 'default'

interface ThemeConfig {
  name: string
  primary: string
  secondary: string
  accent: string
  gradient: string
  border: string
  text: string
  bg: string
  icon: string
}

const themes: Record<ThemeModule, ThemeConfig> = {
  default: {
    name: 'Marketplace',
    primary: 'blue',
    secondary: 'indigo',
    accent: 'sky',
    gradient: 'bg-gradient-to-r from-blue-600 to-indigo-600',
    border: 'border-blue-100',
    text: 'text-blue-600',
    bg: 'bg-blue-50',
    icon: 'text-blue-500'
  },
  market: {
    name: 'Marketplace',
    primary: 'blue',
    secondary: 'indigo',
    accent: 'sky',
    gradient: 'bg-gradient-to-r from-blue-600 to-indigo-600',
    border: 'border-blue-100',
    text: 'text-blue-600',
    bg: 'bg-blue-50',
    icon: 'text-blue-500'
  },
  food: {
    name: 'Yemek',
    primary: 'orange',
    secondary: 'amber',
    accent: 'yellow',
    gradient: 'bg-gradient-to-r from-orange-500 to-amber-500',
    border: 'border-orange-100',
    text: 'text-orange-600',
    bg: 'bg-orange-50',
    icon: 'text-orange-500'
  },
  transport: {
    name: 'Ulaşım',
    primary: 'emerald',
    secondary: 'green',
    accent: 'teal',
    gradient: 'bg-gradient-to-r from-emerald-600 to-green-600',
    border: 'border-emerald-100',
    text: 'text-emerald-600',
    bg: 'bg-emerald-50',
    icon: 'text-emerald-500'
  },
  hotel: {
    name: 'Seyahat',
    primary: 'purple',
    secondary: 'fuchsia',
    accent: 'pink',
    gradient: 'bg-gradient-to-r from-purple-600 to-fuchsia-600',
    border: 'border-purple-100',
    text: 'text-purple-600',
    bg: 'bg-purple-50',
    icon: 'text-purple-500'
  },
  career: {
    name: 'Kariyer',
    primary: 'cyan',
    secondary: 'teal',
    accent: 'sky',
    gradient: 'bg-gradient-to-r from-cyan-600 to-teal-600',
    border: 'border-cyan-100',
    text: 'text-cyan-600',
    bg: 'bg-cyan-50',
    icon: 'text-cyan-500'
  }
}

export function useTheme() {
  const route = useRoute()

  const currentModule = computed<ThemeModule>(() => {
    const path = route.path
    if (path.startsWith('/food')) return 'food'
    if (path.startsWith('/transport')) return 'transport'
    if (path.startsWith('/hotel')) return 'hotel'
    if (path.startsWith('/career')) return 'career'
    if (path.startsWith('/market')) return 'market'
    return 'default'
  })

  const theme = computed(() => themes[currentModule.value])

  return {
    currentModule,
    theme
  }
}
