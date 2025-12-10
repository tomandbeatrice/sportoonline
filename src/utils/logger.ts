/**
 * Production-safe Logger Utility
 * Only logs in development mode, always logs errors
 */

const isDev = import.meta.env.DEV

export const logger = {
  /**
   * Log info messages (only in development)
   */
  log: (...args: unknown[]) => {
    if (isDev) console.log(...args)
  },

  /**
   * Log debug messages (only in development)
   */
  debug: (...args: unknown[]) => {
    if (isDev) console.debug(...args)
  },

  /**
   * Log info messages (only in development)
   */
  info: (...args: unknown[]) => {
    if (isDev) console.info(...args)
  },

  /**
   * Log warning messages (only in development)
   */
  warn: (...args: unknown[]) => {
    if (isDev) console.warn(...args)
  },

  /**
   * Log error messages (always, including production)
   */
  error: (...args: unknown[]) => {
    console.error(...args)
  },

  /**
   * Log API request info (only in development)
   */
  api: (method: string, url: string, status?: number) => {
    if (isDev) {
      const emoji = status && status >= 400 ? '🚨' : '🌐'
      console.log(`${emoji} ${method.toUpperCase()} ${url}`, status ? `[${status}]` : '')
    }
  },

  /**
   * Log performance metrics (only in development)
   */
  perf: (label: string, duration: number) => {
    if (isDev) {
      console.log(`⏱️ ${label}: ${duration.toFixed(2)}ms`)
    }
  }
}

export default logger
