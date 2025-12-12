import { reactive, readonly } from 'vue'
import { isFeatureEnabled as simpleIsEnabled } from '@/utils/featureToggle'

export interface FeatureConfig {
  enabled: boolean
  rolloutPercentage?: number
  allowedRoles?: string[]
  description?: string
}

export interface FeatureFlags {
  [key: string]: FeatureConfig | boolean
}

// Feature flags with detailed configuration
const flags = reactive<FeatureFlags>({
  // AI Features
  gemini_3_pro: {
    enabled: true,
    rolloutPercentage: 100,
    description: 'Enable Gemini 3 Pro (Preview) for all clients'
  },
  
  // Dashboard Features
  new_buyer_dashboard: {
    enabled: true,
    rolloutPercentage: 100,
    description: 'Modern buyer dashboard with enhanced UI'
  },
  admin_v2: {
    enabled: true,
    rolloutPercentage: 100,
    allowedRoles: ['admin', 'super_admin'],
    description: 'Admin panel v2 with component architecture'
  },
  seller_analytics: {
    enabled: true,
    rolloutPercentage: 80,
    allowedRoles: ['seller', 'admin'],
    description: 'Advanced analytics for sellers'
  },
  
  // Service Features
  service_booking: {
    enabled: true,
    rolloutPercentage: 100,
    description: 'Hotel, Transport, Food service bookings'
  },
  career_portal: {
    enabled: true,
    rolloutPercentage: 100,
    description: 'Career opportunities and job applications'
  },
  
  // Payment & Commerce
  turbo_mode: {
    enabled: true,
    rolloutPercentage: 50,
    description: 'Turbo shopping mode with gamification'
  },
  wallet_system: {
    enabled: true,
    rolloutPercentage: 100,
    description: 'Digital wallet and balance management'
  },
  
  // Experimental
  ai_recommendations: {
    enabled: false,
    rolloutPercentage: 10,
    description: 'AI-powered product recommendations'
  },
  real_time_chat: {
    enabled: false,
    rolloutPercentage: 0,
    description: 'Real-time customer support chat'
  }
})

// Persist flags to localStorage
const STORAGE_KEY = 'feature_flags_override'

// Load overrides from localStorage
const loadOverrides = () => {
  try {
    const stored = localStorage.getItem(STORAGE_KEY)
    if (stored) {
      const overrides = JSON.parse(stored)
      Object.keys(overrides).forEach(key => {
        if (flags[key]) {
          if (typeof flags[key] === 'object') {
            (flags[key] as FeatureConfig).enabled = overrides[key]
          } else {
            flags[key] = overrides[key]
          }
        }
      })
    }
  } catch (e) {
    console.warn('Failed to load feature flag overrides:', e)
  }
}

// Save override to localStorage
const saveOverride = (flag: string, enabled: boolean) => {
  try {
    const stored = localStorage.getItem(STORAGE_KEY)
    const overrides = stored ? JSON.parse(stored) : {}
    overrides[flag] = enabled
    localStorage.setItem(STORAGE_KEY, JSON.stringify(overrides))
  } catch (e) {
    console.warn('Failed to save feature flag override:', e)
  }
}

// Initialize overrides
loadOverrides()

export const useFeatureFlags = () => {
  const isEnabled = (flag: string, userRole?: string): boolean => {
    // Delegate to simple toggles for dot-notated keys (e.g., homepage.*, admin.services.*)
    if (flag.includes('.')) {
      try {
        // Cast is safe for known keys; unknown returns false in helper
        return simpleIsEnabled(flag as any)
      } catch {
        // Fallback to complex flags below
      }
    }
    const config = flags[flag]
    
    if (!config) return false
    
    // Simple boolean flag
    if (typeof config === 'boolean') {
      return config
    }
    
    // Complex configuration
    if (!config.enabled) return false
    
    // Check role restriction
    if (config.allowedRoles && userRole) {
      if (!config.allowedRoles.includes(userRole)) {
        return false
      }
    }
    
    // Check rollout percentage (simple hash-based)
    if (config.rolloutPercentage !== undefined && config.rolloutPercentage < 100) {
      const hash = flag.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0)
      const userHash = (hash % 100) + 1
      return userHash <= config.rolloutPercentage
    }
    
    return true
  }

  const enable = (flag: string, persist = true) => {
    const config = flags[flag]
    if (typeof config === 'object') {
      config.enabled = true
    } else {
      flags[flag] = true
    }
    if (persist) saveOverride(flag, true)
  }

  const disable = (flag: string, persist = true) => {
    const config = flags[flag]
    if (typeof config === 'object') {
      config.enabled = false
    } else {
      flags[flag] = false
    }
    if (persist) saveOverride(flag, false)
  }

  const getConfig = (flag: string): FeatureConfig | boolean | undefined => {
    return flags[flag]
  }

  const getAllFlags = () => {
    return readonly(flags)
  }

  const resetOverrides = () => {
    localStorage.removeItem(STORAGE_KEY)
    loadOverrides()
  }

  return {
    flags: readonly(flags),
    isEnabled,
    enable,
    disable,
    getConfig,
    getAllFlags,
    resetOverrides
  }
}
