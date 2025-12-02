
<template>
  <div>
    <h3>Öneri Varyantları</h3>
    <table>
      <thead>
        <tr>
          <th>Segment</th>
          <th>Kampanya</th>
          <th>İndirim</th>
          <th>Süre</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(variant, index) in variants" :key="index">
          <td>{{ variant.segment }}</td>
          <td>{{ variant.campaign_type }}</td>
          <td>%{{ variant.discount }}</td>
          <td>{{ variant.duration }} gün</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const variants = ref([])

onMounted(async () => {
  const res = await axios.get('/api/admin/campaign-variants')
  variants.value = res.data.variants
})
</script>