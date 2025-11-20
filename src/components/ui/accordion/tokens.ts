import type { InjectionKey, Ref } from 'vue'

export type AccordionType = 'single' | 'multiple'

export interface AccordionContext {
  type: AccordionType
  openValues: Ref<string[]>
  toggle: (value: string) => void
}

export const accordionKey: InjectionKey<AccordionContext> = Symbol('AccordionContext')
export const accordionItemKey: InjectionKey<string> = Symbol('AccordionItemValue')
