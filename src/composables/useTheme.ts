import { ref } from 'vue'
import { themes } from '@/utils/theme'

const currentTheme = ref<'light' | 'dark'>('light')

export function useTheme() {
  function setTheme(name: keyof typeof themes) {
    currentTheme.value = name
    const theme = themes[name]
    Object.entries(theme).forEach(([key, value]) => {
      document.documentElement.style.setProperty(`--${key}`, value as string)
    })
  }

  return { currentTheme, setTheme }
}