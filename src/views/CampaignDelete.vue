<template>
  <dialog v-if="visible" class="modal modal-open">
    <div class="modal-box max-w-md">
      <h3 class="font-bold text-lg mb-4">🗑 Kampanya Sil</h3>
      <p class="mb-6">“{{ campaign.name }}” kampanyasını silmek istediğine emin misin?</p>
      <div class="modal-action">
        <button class="btn btn-error" @click="confirmDelete">Sil</button>
        <button class="btn" @click="close">İptal</button>
      </div>
    </div>
  </dialog>
</template>

<script setup lang="ts">
import { ref, defineProps, defineEmits, watch } from 'vue'
import { useApi } from '@/composables/useApi'

const props = defineProps<{ modelValue: boolean; campaign: any }>()
const emit = defineEmits(['update:modelValue', 'deleted'])

const { del } = useApi()
const visible = ref(props.modelValue)

watch(() => props.modelValue, val => {
  visible.value = val
})

const close = () => {
  emit('update:modelValue', false)
}

const confirmDelete = async () => {
  try {
    await del(`/admin/campaign-delete/${props.campaign.id}`)
    emit('deleted', props.campaign.id)
    close()
  } catch (err) {
    console.error('Silme hatası:', err)
  }
}
</script>

<style scoped>
.modal-open {
  display: block;
}
</style>