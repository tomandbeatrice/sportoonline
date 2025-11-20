import type { Ref } from 'vue'

export type BadgeTone = 'accent' | 'primary' | 'success' | 'warning' | 'neutral' | 'dark'

export interface BadgeAction {
  label: string
  href?: string
  to?: string
  trackingId?: string
}

export interface NormalizedBadge {
  code: string
  label: string
  tone: BadgeTone
  icon?: string
  description?: string
  tooltip?: string
  href?: string
  action?: BadgeAction
  meta?: Record<string, unknown>
}

type BackendBadge = string | number | Record<string, any>

type BadgePreset = {
  label: string
  tone: BadgeTone
  icon?: string
}

const BADGE_PRESETS: Record<string, BadgePreset> = {
  express: { label: 'Express Teslimat', tone: 'success', icon: 'bolt' },
  flash: { label: 'Flash Fırsat', tone: 'warning', icon: 'clock' },
  vip: { label: 'VIP Seçimi', tone: 'dark', icon: 'star' },
  new: { label: 'Yeni', tone: 'accent', icon: 'rocket' },
  best_seller: { label: 'Çok Satan', tone: 'primary', icon: 'trophy' },
  trend: { label: 'Trend', tone: 'primary', icon: 'target' },
  limited: { label: 'Son Şans', tone: 'warning', icon: 'clock' },
  eco: { label: 'Eco Paket', tone: 'success', icon: 'flag' },
  verified: { label: 'Onaylı Satıcı', tone: 'primary', icon: 'star' },
  loyalty: { label: 'Sadakat', tone: 'accent', icon: 'gift' },
  default: { label: 'Öne Çıkan', tone: 'neutral' }
}

const BADGE_ALIASES: Record<string, string> = {
  express_delivery: 'express',
  express_shipping: 'express',
  same_day: 'express',
  same_day_delivery: 'express',
  fast_ship: 'express',
  flash_deal: 'flash',
  flash: 'flash',
  lightning: 'flash',
  limited_stock: 'limited',
  low_stock: 'limited',
  vip_only: 'vip',
  premium: 'vip',
  new_arrival: 'new',
  yeni: 'new',
  best_seller: 'best_seller',
  bestseller: 'best_seller',
  top_seller: 'best_seller',
  popular: 'trend',
  trending: 'trend',
  trend: 'trend',
  eco_friendly: 'eco',
  sustainable: 'eco',
  verified_seller: 'verified',
  loyalty_club: 'loyalty'
}

const toneAliasMap: Record<string, BadgeTone> = {
  accent: 'accent',
  pink: 'accent',
  magenta: 'accent',
  primary: 'primary',
  info: 'primary',
  blue: 'primary',
  indigo: 'primary',
  success: 'success',
  green: 'success',
  emerald: 'success',
  warning: 'warning',
  amber: 'warning',
  orange: 'warning',
  danger: 'dark',
  error: 'dark',
  red: 'dark',
  dark: 'dark',
  black: 'dark',
  slate: 'neutral',
  gray: 'neutral',
  neutral: 'neutral'
}

export const badgeToneClasses: Record<BadgeTone, string> = {
  accent: 'bg-fuchsia-50 text-fuchsia-600 ring-1 ring-fuchsia-100',
  primary: 'bg-blue-50 text-blue-600 ring-1 ring-blue-100',
  success: 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100',
  warning: 'bg-amber-50 text-amber-600 ring-1 ring-amber-100',
  neutral: 'bg-slate-100 text-slate-600 ring-1 ring-slate-200',
  dark: 'bg-slate-900 text-white ring-1 ring-slate-900/70'
}

export function deriveBadgesFromProduct(product: Record<string, any>): NormalizedBadge[] {
  const fromBackend = extractBadgeCandidates(product)
    .map((entry) => normalizeBadgeEntry(entry))
    .filter((badge): badge is NormalizedBadge => Boolean(badge))

  if (fromBackend.length) {
    debugLogBadges(product, fromBackend, 'backend')
    return dedupeBadges(fromBackend)
  }

  const heuristics = buildHeuristicBadges(product)
  debugLogBadges(product, heuristics, 'heuristic')
  return heuristics.length ? heuristics : []
}

export function attachBadges<T extends Record<string, any>>(items: T[] | Ref<T[]>): T[] {
  const list = Array.isArray(items) ? items : items.value
  return list.map((item) => ({
    ...item,
    badgeTokens: deriveBadgesFromProduct(item)
  }))
}

function extractBadgeCandidates(product: Record<string, any>): BackendBadge[] {
  const pools = [
    product.badges,
    product.badge_meta,
    product.badgeMeta,
    product.badgeCodes,
    product.badge_codes,
    product.badgeLabels,
    product.badge_labels,
    product.rozetler,
    product.labels,
    product.tags,
    product.flags,
    product.product_tags,
    product.sale_badges,
    product.pdp_badges,
    product.plp_badges,
    product.attributes?.badges,
    product.meta?.badges,
    product.metadata?.badges,
    product.merchandising?.badges,
    product.highlights,
    product?.badgeSet
  ]

  return pools.flatMap((entry) => toArray(entry))
}

function toArray(value: unknown): BackendBadge[] {
  if (!value) return []
  if (Array.isArray(value)) return value
  if (value instanceof Set) return Array.from(value)

  if (typeof value === 'string') {
    const trimmed = value.trim()
    if (!trimmed) return []

    if (trimmed.startsWith('[') || trimmed.startsWith('{')) {
      try {
        const parsed = JSON.parse(trimmed)
        return toArray(parsed)
      } catch (error) {
        return [trimmed]
      }
    }

    return trimmed.split(/[,;|]/).map((chunk) => chunk.trim()).filter(Boolean)
  }

  if (typeof value === 'object') {
    if ('data' in (value as Record<string, any>) && Array.isArray((value as Record<string, any>).data)) {
      return (value as Record<string, any>).data
    }

    return Object.values(value as Record<string, any>)
  }

  return []
}

function normalizeBadgeEntry(entry: BackendBadge): NormalizedBadge | null {
  if (entry === null || entry === undefined) return null

  if (typeof entry === 'string' || typeof entry === 'number') {
    const raw = String(entry)
    const code = resolveCode(raw)
    if (!code) return null
    return createBadge(code, { label: prettifyLabel(raw) })
  }

  if (typeof entry === 'object') {
    const rawCode = (entry.code ?? entry.key ?? entry.slug ?? entry.type ?? entry.tag ?? entry.variant ?? entry?.id) as string | undefined
    const labelCandidate = (entry.label ?? entry.title ?? entry.text ?? entry.name ?? entry.copy) as string | undefined
    const code = resolveCode(rawCode ?? labelCandidate ?? '')
    if (!code) return null

    const toneCandidate = entry.tone ?? entry.variant ?? entry.style
    const tone = resolveTone(toneCandidate)

    const action = resolveAction(entry)
    const href = (entry.href ?? entry.url ?? entry.link) as string | undefined
    const description = (entry.description ?? entry.subtitle ?? entry.copy) as string | undefined
    const tooltip = (entry.tooltip ?? entry.hoverText ?? entry.helper) as string | undefined
    const meta = (entry.meta ?? entry.metadata ?? entry.analytics) as Record<string, unknown> | undefined

    return createBadge(code, {
      label: labelCandidate ?? prettifyLabel(rawCode ?? labelCandidate ?? code),
      tone,
      icon: entry.icon,
      description,
      tooltip,
      href,
      action,
      meta
    })
  }

  return null
}

function buildHeuristicBadges(product: Record<string, any>): NormalizedBadge[] {
  const heuristics: NormalizedBadge[] = []

  const discountRate = Number(product.discount_rate ?? product.discountRate ?? product.discount)
  if (!Number.isNaN(discountRate) && discountRate >= 15) {
    heuristics.push(createBadge('flash', { label: `-%${Math.round(discountRate)}` }))
  }

  const avgRating = Number(product.avg_rating ?? product.rating ?? product.average_rating)
  if (!Number.isNaN(avgRating) && avgRating >= 4.7) {
    heuristics.push(createBadge('best_seller', { label: `${avgRating.toFixed(1)}` }))
  }

  const stock = Number(product.stock)
  if (!Number.isNaN(stock) && stock > 0 && stock < 10) {
    heuristics.push(createBadge('limited', { label: 'Son Ürünler' }))
  }

  const createdAt = product.created_at ? new Date(product.created_at) : null
  if (createdAt && !Number.isNaN(createdAt.valueOf())) {
    const diffDays = (Date.now() - createdAt.getTime()) / (1000 * 60 * 60 * 24)
    if (diffDays <= 21) {
      heuristics.push(createBadge('new'))
    }
  }

  const shipmentTags = [product.campaign_tag, product.fulfillment_tag, product.delivery_tag, product.shipping_tag]
  shipmentTags.forEach((tag) => {
    if (!tag || typeof tag !== 'string') return
    if (tag.toLowerCase().includes('express')) {
      heuristics.push(createBadge('express'))
      return
    }
    const code = resolveCode(tag)
    if (code && code !== 'default') {
      heuristics.push(createBadge(code, { label: prettifyLabel(tag) }))
    }
  })

  return dedupeBadges(heuristics)
}

function resolveCode(raw: string): string {
  if (!raw) return ''
  const normalized = normalizeKey(raw)
  if (!normalized) return ''
  return BADGE_ALIASES[normalized] ?? normalized
}

function resolveTone(value: unknown): BadgeTone | undefined {
  if (!value) return undefined
  const normalized = normalizeKey(String(value))
  return toneAliasMap[normalized]
}

function createBadge(codeInput: string, overrides: Partial<Omit<NormalizedBadge, 'code'>> = {}): NormalizedBadge {
  const canonical = codeInput || 'default'
  const preset = BADGE_PRESETS[canonical] ?? BADGE_PRESETS.default

  return {
    code: canonical,
    label: overrides.label ?? preset.label,
    tone: overrides.tone ?? preset.tone,
    icon: overrides.icon ?? preset.icon,
    description: overrides.description,
    tooltip: overrides.tooltip,
    href: overrides.href,
    action: overrides.action,
    meta: overrides.meta
  }
}

function dedupeBadges(badges: NormalizedBadge[]): NormalizedBadge[] {
  const seen = new Set<string>()
  return badges.filter((badge) => {
    const key = `${badge.code}-${badge.label}`
    if (seen.has(key)) return false
    seen.add(key)
    return true
  })
}

function normalizeKey(value: string): string {
  return value
    .toString()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^\w\s-]/g, '')
    .trim()
    .toLowerCase()
    .replace(/[-\s]+/g, '_')
}

function prettifyLabel(value: string): string {
  return value
    .toString()
    .replace(/[_-]+/g, ' ')
    .replace(/\s+/g, ' ')
    .trim()
    .split(' ')
    .filter(Boolean)
    .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
    .join(' ')
}

function resolveAction(entry: Record<string, any>): BadgeAction | undefined {
  const payload = entry.action ?? entry.cta ?? entry.callToAction
  if (!payload) return undefined

  if (typeof payload === 'string') {
    return { label: payload }
  }

  if (typeof payload === 'object') {
    const label = payload.label ?? payload.text ?? payload.title
    if (!label) return undefined
    return {
      label,
      href: payload.href ?? payload.url ?? payload.link,
      to: payload.to,
      trackingId: payload.trackingId ?? payload.analyticsId ?? payload.id
    }
  }

  return undefined
}

let badgeDebugCache: boolean | null = null

export function isBadgeDebugEnabled(): boolean {
  if (badgeDebugCache !== null) return badgeDebugCache
  if (typeof window === 'undefined') return false

  const search = window.location?.search ?? ''
  const params = new URLSearchParams(search)
  const queryFlag = params.has('showBadges') || params.get('badgeDebug') === '1'
  const localFlag = window.localStorage?.getItem('badgeDebug') === '1'
  const envFlag = typeof import.meta !== 'undefined' && import.meta.env?.VITE_BADGE_DEBUG === 'true'

  badgeDebugCache = Boolean(queryFlag || localFlag || envFlag)
  return badgeDebugCache
}

function debugLogBadges(product: Record<string, any>, badges: NormalizedBadge[], source: 'backend' | 'heuristic') {
  if (!isBadgeDebugEnabled() || !badges.length) return
  // eslint-disable-next-line no-console
  console.debug('[badge-debug]', {
    productId: product.id ?? product.slug ?? product.name,
    source,
    badges
  })
}
