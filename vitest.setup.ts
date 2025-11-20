import { config } from '@vue/test-utils'
import { vi } from 'vitest'

// i18n mock örneği
config.global.mocks = {
  $t: (msg: string) => msg,
}

// Görsel import mock'u (örneğin banner.jpg)
vi.mock('/banner.jpg', () => ({
  default: '',
}))
