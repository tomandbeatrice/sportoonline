<template>
  <div>
    <component :is="roleComponent" />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useUserStore } from '@/stores/user'
import AdminCockpit from '@/components/AdminCockpit.vue'
import DevCockpit from '@/components/DevCockpit.vue'
import ViewerCockpit from '@/components/ViewerCockpit.vue'

const userStore = useUserStore()

const roleComponent = computed(() => {
  switch (userStore.role) {
    case 'admin': return AdminCockpit
    case 'developer': return DevCockpit
    default: return ViewerCockpit
  }
})
</script>