declare global {
  interface ModuleLog {
    module: string
    status: 'active' | 'pending' | 'error'
    duration: string
    errorCount: number
    lastAction: string
    data: { timestamp: string; count: number }[]
  }
}