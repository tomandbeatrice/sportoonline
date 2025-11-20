<template>
  <div class="campaigns">
    <h2>Aktif Kampanyalar</h2>
    <div class="card" v-for="item in campaigns" :key="item.id">
      <img :src="item.image" alt="Kampanya Görseli" />
      <h3>{{ item.title }}</h3>
      <p>{{ item.description }}</p>
      <button @click="goToDetail(item.id)">İncele</button>
    </div>
  </div>
</template>

<script setup lang="ts">
// filepath: c:\Users\sport\Desktop\sportoonline\src\views\Campaign.vue
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const campaigns = ref<any[]>([])
const router = useRouter()

onMounted(async () => {
  const res = await fetch('/panel.json')
  campaigns.value = await res.json()
})

function goToDetail(id: number) {
  router.push(`/campaign/${id}`)
}
</script>

<style scoped>
.campaigns {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  padding: 20px;
}
.card {
  width: 250px;
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 10px;
  text-align: center;
}
img {
  max-width: 100%;
  height: auto;
}
button {
  margin-top: 10px;
  padding: 8px 16px;
  background-color: #28a745;
  color: white;
  border: none;
  cursor: pointer;
}
</style>