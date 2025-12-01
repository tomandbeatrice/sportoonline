<template>
  <div class="checkout-form max-w-md mx-auto p-6 bg-white rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-4">Ödeme</h2>
    <form @submit.prevent="pay" class="space-y-4">
      <FormField label="Kart Numarası">
        <Input v-model="cardNumber" type="text" placeholder="Kart Numarası" required />
      </FormField>
      <FormField label="Kart Üzerindeki İsim">
        <Input v-model="cardName" type="text" placeholder="Kart Üzerindeki İsim" required />
      </FormField>
      <FormField label="Son Kullanma Tarihi">
        <Input v-model="expiry" type="text" placeholder="AA/YY" required />
      </FormField>
      <FormField label="CVC">
        <Input v-model="cvc" type="text" placeholder="CVC" required />
      </FormField>
      <Button type="submit" class="w-full">Öde</Button>
    </form>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import { Input, Button, FormField } from 'shadcn-ui';
const cardNumber = ref('');
const cardName = ref('');
const expiry = ref('');
const cvc = ref('');
function pay() {
  // Ödeme işlemi
}
</script>
<style scoped>
.checkout-form {
  /* shadcn UI ile modernleştirildi */
}
</style>