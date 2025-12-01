<template>
  <ModuleOutput :title="title" :output="output" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import ModuleOutput from './ModuleOutput.vue'

const props = defineProps<{
  title: string
  endpoint: string
}>()

const output = ref<string | undefined>()

onMounted(async () => {
  try {
    const { data } = await axios.get(props.endpoint)
    output.value = data?.output ?? '⚠️ Veri boş geldi.'
  } catch (error) {
    output.value = '❌ API bağlantısı başarısız.'
  }
})
</script>