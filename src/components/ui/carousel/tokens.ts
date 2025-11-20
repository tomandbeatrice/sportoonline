import type { InjectionKey, Ref } from 'vue'

export interface CarouselContext {
  registerContent: (el: HTMLDivElement | null) => void
  slideBy: (direction: 'next' | 'prev') => void
  activeIndex: Ref<number>
}

export const carouselKey: InjectionKey<CarouselContext> = Symbol('CarouselContext')
