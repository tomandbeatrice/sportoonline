<template>
  <section class="p-6 bg-white rounded shadow max-w-xl mx-auto">
    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
      <BadgeIcon name="plus-circle" cls="w-6 h-6 text-blue-600" /> Yeni Kampanya Oluştur
    </h2>

    <form @submit.prevent="submit">
      <div class="mb-4">
        <label class="block font-medium mb-1">Kampanya Adı</label>
        <input v-model="form.name" type="text" class="input input-bordered w-full" required />
      </div>

      <div class="mb-4 flex gap-4">
        <div class="flex-1">
          <label class="block font-medium mb-1">Başlangıç Tarihi</label>
          <input v-model="form.start_date" type="date" class="input input-bordered w-full" required />
        </div>
        <div class="flex-1">
          <label class="block font-medium mb-1">Bitiş Tarihi</label>
          <input v-model="form.end_date" type="date" class="input input-bordered w-full" required />
        </div>
      </div>

      <div class="mb-4">
        <label class="block font-medium mb-1">Durum</label>
        <select v-model="form.status" class="select select-bordered w-full">
          <option value="active">Aktif</option>
          <option value="passive">Pasif</option>
        </select>
      </div>

      <div class="mb-6">
        <label class="block font-medium mb-1">Skor</label>
        <input v-model="form.score" type="number" class="input input-bordered w-full" min="0" max="100" />
      </div>

      <button class="btn btn-primary w-full flex items-center justify-center gap-2" type="submit">
        <BadgeIcon name="check" cls="w-5 h-5" /> Oluştur
      </button>
    </form>

    <div v-if="success" class="mt-4 text-green-600 font-medium flex items-center gap-2">
      <BadgeIcon name="check" cls="w-5 h-5" /> Kampanya başarıyla oluşturuldu!
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, defineEmits } from 'vue'
import { useApi } from '@/composables/useApi'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

const emit = defineEmits(['created'])
const { post } = useApi()
const success = ref(false)

const form = ref({
  name: '',
  start_date: '',
  end_date: '',
  status: 'active',
  score: 0
})

const submit = async () => {
  try {
    const newCampaign = await post('/admin/campaign-create', form.value)
    emit('created', newCampaign)
    success.value = true
    form.value = {
      name: '',
      start_date: '',
      end_date: '',
      status: 'active',
      score: 0
    }
  } catch (err) {
    console.error('Kampanya oluşturulamadı:', err)
  }
}
</script>

<style scoped>
section {
  background-color: #f9fafb;
}
</style>