<template>
  <div class="filters">
    <label>
      Durum:
      <select v-model="selectedStatus">
        <option value="">Tümü</option>
        <option value="active">Aktif</option>
        <option value="pending">Bekliyor</option>
        <option value="error">Hatalı</option>
      </select>
    </label>
    <label>
      Min. Hata:
      <input type="number" v-model.number="minError" />
    </label>
    <label>
      Tarih:
      <input type="date" v-model="startDate" />
      <input type="date" v-model="endDate" />
    </label>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'

const emit = defineEmits(['filter-change'])

const selectedStatus = ref('')
const minError = ref(0)
const startDate = ref('')
const endDate = ref('')

watch([selectedStatus, minError, startDate, endDate], () => {
  emit('filter-change', {
    status: selectedStatus.value,
    minError: minError.value,
    startDate: startDate.value,
    endDate: endDate.value
  })
})
</script>

<style scoped>
.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}
</style>