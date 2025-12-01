import { ref } from 'vue'

export function useI18n() {
  const locale = ref('tr')
  
  const t = (key: string) => {
    // Simple translation stub
    return key
  }
  
  return {
    locale,
    t
  }
}
