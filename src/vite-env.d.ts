/// <reference types="vite/client" />

declare module '*.vue' {
  import { DefineComponent } from 'vue'
  const component: DefineComponent<{}, {}, any>
  export default component
}

// Global Window extensions for dev tools
declare global {
  interface Window {
    __VUE_APP__: any
    __ANALYTICS__: any
    __ERROR_TRACKING__: any
    __PERFORMANCE__: any
  }
}

export {}
