<template>
  <component :is="iconComponent" v-if="iconComponent" :class="cls" aria-hidden="true" />
</template>

<script setup lang="ts">
import { computed } from 'vue'
import IconBolt from './IconBolt.vue'
import IconClock from './IconClock.vue'
import IconStar from './IconStar.vue'
import IconRocket from './IconRocket.vue'
import IconTrophy from './IconTrophy.vue'
import IconTarget from './IconTarget.vue'
import IconFlag from './IconFlag.vue'
import IconGift from './IconGift.vue'
import IconCart from './IconCart.vue'
import IconMoney from './IconMoney.vue'
import IconClipboard from './IconClipboard.vue'
import IconTicket from './IconTicket.vue'
import IconMedal from './IconMedal.vue'
import IconRunner from './IconRunner.vue'
import IconUser from './IconUser.vue'
import IconImage from './IconImage.vue'
import IconBell from './IconBell.vue'
import IconCheck from './IconCheck.vue'
import IconTruck from './IconTruck.vue'
import IconFile from './IconFile.vue'
import IconClose from './IconClose.vue'
import IconChat from './IconChat.vue'
import IconBox from './IconBox.vue'

const props = defineProps<{
  name?: string
  cls?: string
}>()

const iconMap: Record<string, any> = {
  bolt: IconBolt,
  clock: IconClock,
  star: IconStar,
  rocket: IconRocket,
  trophy: IconTrophy,
  target: IconTarget,
  flag: IconFlag,
  gift: IconGift,
  cart: IconCart,
  money: IconMoney,
  clipboard: IconClipboard,
  ticket: IconTicket,
  medal: IconMedal,
  runner: IconRunner,
  check: IconCheck,
  box: IconBox,
  truck: IconTruck,
  file: IconFile,
  close: IconClose,
  chat: IconChat,
  // fallbacks
  chart: IconTrophy,
  user: IconUser,
  image: IconImage,
  bell: IconBell,
}

const iconComponent = computed(() => {
  if (!props.name) return null
  return iconMap[props.name] || null
})
</script>
