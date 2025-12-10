import { reactive } from 'vue'

interface FeatureFlags {
  [key: string]: boolean
}

const flags = reactive<FeatureFlags>({
  gemini_3_pro: true, // Enable Gemini 3 Pro (Preview) for all clients
  new_buyer_dashboard: true,
  admin_v2: true,
})

export const useFeatureFlags = () => {
  const isEnabled = (flag: string): boolean => {
    return flags[flag] || false
  }

  const enable = (flag: string) => {
    flags[flag] = true
  }

  const disable = (flag: string) => {
    flags[flag] = false
  }

  return {
    flags,
    isEnabled,
    enable,
    disable
  }
}
