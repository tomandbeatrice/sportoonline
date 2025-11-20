<template>
  <div class="feedback-panel">
    <h3>Geri Bildirim</h3>
    <textarea v-model="feedback" placeholder="Sprint hakkında yorumunuzu yazın..." />
    <button @click="submitFeedback">Gönder</button>
    <p v-if="submitted" class="flex items-center gap-2 mt-2 text-green-600">
      <BadgeIcon name="check" cls="w-5 h-5" /> Geri bildiriminiz alındı.
    </p>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'
const feedback = ref('')
const submitted = ref(false)

function submitFeedback() {
  if (feedback.value.trim()) {
    // Burada backend'e gönderme işlemi yapılabilir
    submitted.value = true
  }
}
</script>

<style scoped>
.feedback-panel {
  margin-top: 2rem;
  background: #f9f9f9;
  padding: 16px;
  border-radius: 8px;
}
textarea {
  width: 100%;
  height: 100px;
  margin-bottom: 12px;
  padding: 8px;
  border-radius: 4px;
  border: 1px solid #ccc;
}
button {
  background: #00b894;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
}
</style>