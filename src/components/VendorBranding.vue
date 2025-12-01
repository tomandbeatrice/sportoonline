<template>
  <div v-if="vendor">
    <h2>{{ vendor.name }} Marka Teması</h2>
    <div :style="{ backgroundColor: vendor.branding_color, fontFamily: vendor.branding_font }">
      <img :src="vendor.branding_logo" alt="Logo" style="height: 80px;" />
      <p>Renk: {{ vendor.branding_color }}</p>
      <p>Font: {{ vendor.branding_font }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const vendor = ref(null)
const route = useRoute()

onMounted(async () => {
  const res = await fetch(`/api/seller/${route.params.id}/branding`)
  vendor.value = await res.json()
})
</script>