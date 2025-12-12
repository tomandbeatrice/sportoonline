/**
 * Error Tracking Service
 * Centralized error logging and monitoring
 */

import { analytics } from './analytics.js'

export interface ErrorContext {
  userId?: string | number
  userEmail?: string
  url?: string
  component?: string
  action?: string
  extra?: Record<string, any>
}

export interface ErrorReport {
  message: string
  stack?: string
  timestamp: Date
  severity: 'low' | 'medium' | 'high' | 'critical'
  context: ErrorContext
  userAgent: string
  url: string
}

class ErrorTrackingService {
  private errors: ErrorReport[] = []
  private readonly maxErrors = 100
  private readonly reportEndpoint = '/api/errors'

  /**
   * Initialize error tracking
   */
  init() {
    // Global error handler
    globalThis.addEventListener('error', (event) => {
      this.captureError(event.error || new Error(event.message), {
        severity: 'high',
        context: {
          url: globalThis.location.href
        }
      })
    })

    // Unhandled promise rejection
    globalThis.addEventListener('unhandledrejection', (event) => {
      this.captureError(
        new Error(event.reason?.message || 'Unhandled Promise Rejection'),
        {
          severity: 'high',
          context: {
            url: globalThis.location.href,
            extra: { reason: event.reason }
          }
        }
      )
    })

    // Vue error handler (if using Vue 3)
    const globalWithVue = globalThis as any
    if (globalWithVue.__VUE_APP__) {
      const app = globalWithVue.__VUE_APP__
      app.config.errorHandler = (err: any, instance: any, info: string) => {
        this.captureError(err, {
          severity: 'high',
          context: {
            component: instance?.$options?.name || 'Unknown',
            action: info,
            url: globalThis.location.href
          }
        })
      }
    }

    console.log('✅ Error tracking initialized')
  }

  /**
   * Capture and report an error
   */
  captureError(
    error: Error | string,
    options: {
      severity?: ErrorReport['severity']
      context?: ErrorContext
    } = {}
  ) {
    const errorMessage = typeof error === 'string' ? error : error.message
    const errorStack = typeof error === 'string' ? undefined : error.stack

    const report: ErrorReport = {
      message: errorMessage,
      stack: errorStack,
      timestamp: new Date(),
      severity: options.severity || 'medium',
      context: this.enrichContext(options.context || {}),
      userAgent: navigator.userAgent,
      url: globalThis.location.href
    }

    // Add to local queue
    this.errors.push(report)
    if (this.errors.length > this.maxErrors) {
      this.errors.shift()
    }

    // Log only high/critical errors in development
    if (import.meta.env.DEV && (report.severity === 'high' || report.severity === 'critical')) {
      console.error('🔴 Error captured:', report)
    }

    // Track in analytics
    analytics.exception(errorMessage, report.severity === 'critical')

    // Send to backend
    this.sendReport(report)
  }

  /**
   * Capture a message (non-error)
   */
  captureMessage(
    message: string,
    severity: ErrorReport['severity'] = 'low',
    context?: ErrorContext
  ) {
    this.captureError(new Error(message), { severity, context })
  }

  /**
   * Capture API error
   */
  captureAPIError(
    error: any,
    endpoint: string,
    method: string = 'GET'
  ) {
    const status = error.response?.status
    const statusText = error.response?.statusText
    const data = error.response?.data

    this.captureError(error, {
      severity: status >= 500 ? 'high' : 'medium',
      context: {
        action: `API ${method} ${endpoint}`,
        extra: {
          status,
          statusText,
          data,
          endpoint,
          method
        }
      }
    })
  }

  /**
   * Capture performance issue
   */
  capturePerformance(
    metric: string,
    value: number,
    threshold: number,
    context?: ErrorContext
  ) {
    if (value > threshold) {
      this.captureMessage(
        `Performance issue: ${metric} took ${value}ms (threshold: ${threshold}ms)`,
        'low',
        {
          ...context,
          extra: {
            ...context?.extra,
            metric,
            value,
            threshold
          }
        }
      )
    }
  }

  /**
   * Enrich context with user and session data
   */
  private enrichContext(context: ErrorContext): ErrorContext {
    const user = this.getCurrentUser()

    return {
      ...context,
      userId: context.userId || user?.id,
      userEmail: context.userEmail || user?.email,
      url: context.url || globalThis.location.href,
      extra: {
        ...context.extra,
        timestamp: new Date().toISOString(),
        sessionId: this.getSessionId()
      }
    }
  }

  /**
   * Get current user from localStorage
   */
  private getCurrentUser() {
    try {
      const userStr = localStorage.getItem('user')
      return userStr ? JSON.parse(userStr) : null
    } catch {
      return null
    }
  }

  /**
   * Get or create session ID
   */
  private getSessionId(): string {
    let sessionId = sessionStorage.getItem('sessionId')
    if (!sessionId) {
      sessionId = `${Date.now()}-${Math.random().toString(36).substring(2, 11)}`
      sessionStorage.setItem('sessionId', sessionId)
    }
    return sessionId
  }

  /**
   * Send error report to backend
   */
  private async sendReport(report: ErrorReport) {
    try {
      // Only send in production
      if (!import.meta.env.PROD) return

      await fetch(this.reportEndpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token') || ''}`
        },
        body: JSON.stringify(report)
      })
    } catch (error) {
      // Silently fail - don't create infinite error loop
      console.error('Failed to send error report:', error)
    }
  }

  /**
   * Get recent errors
   */
  getRecentErrors(count: number = 10): ErrorReport[] {
    return this.errors.slice(-count)
  }

  /**
   * Clear error queue
   */
  clearErrors() {
    this.errors = []
  }

  /**
   * Set user context
   */
  setUser(user: { id: string | number; email?: string; name?: string }) {
    // Store user info for error reporting
    analytics.setUserId(user.id)
    analytics.setUserProperties({
      email: user.email,
      name: user.name
    })
  }

  /**
   * Add breadcrumb (for debugging context)
   */
  addBreadcrumb(
    category: string,
    message: string,
    data?: Record<string, any>
  ) {
    if (import.meta.env.DEV) {
      console.log('🍞 Breadcrumb:', category, message, data)
    }
  }
}

// Export singleton instance
export const errorTracking = new ErrorTrackingService()

export default ErrorTrackingService
