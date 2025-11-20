<template>
  <dialog v-if="visible" class="modal modal-open">
    <div class="modal-box max-w-xl">
      <h3 class="font-bold text-lg mb-4">✏️ Kampanya Düzenle</h3>

      <form @submit.prevent="submit">
        <div class="mb-4">
          <label class="block font-medium mb-1">Kampanya Adı</label>
          <input v-model="form.name" type="text" class="input input-bordered w-full" required />
        </div>

        <div class="mb-4 flex gap-4">
          <div class="flex-1">
            <label class="block font-medium mb-1">Başlangıç</label>
            <input v-model="form.start_date" type="date" class="input input-bordered w-full" required />
          </div>
          <div class="flex-1">
            <label class="block font-medium mb-1">Bitiş</label>
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

        <div class="modal-action">
          <button class="btn btn-primary" type="submit">💾 Kaydet</button>
          <button class="btn" @click="close">İptal</button>
        </div>
      </form>
    </div>
  </dialog>
</template>

<script setup lang="ts">
import { ref, watch, defineProps, defineEmits } from 'vue'
import { useApi } from '@/composables/useApi'

const props = defineProps<{ modelValue: boolean; campaign: any }>()
const emit = defineEmits(['update:modelValue', 'updated'])

const { put } = useApi()
const visible = ref(props.modelValue)
const form = ref({ ...props.campaign })

watch(() => props.modelValue, val => {
  visible.value = val
  form.value = { ...props.campaign }
})

const close = () => {
  emit('update:modelValue', false)
}

const submit = async () => {
  try {
    const updated = await put('/admin/campaign-update', form.value)
    emit('updated', updated)
    close()
  } catch (err) {
    console.error('Güncelleme hatası:', err)
  }
}
</script>

<style scoped>
.modal-open {
  display: block;
}
</style>