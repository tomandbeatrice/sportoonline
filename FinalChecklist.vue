<template>
  <div class="final-checklist">
    <h2>✅ Yayın Öncesi Test ve Dokümantasyon</h2>
    <v-list>
      <v-list-item
        v-for="modül in modüller" :key="modül.id"
        :class="{ completed: modül.testEdildi }"
      >
        <v-list-item-content>
          <v-list-item-title>{{ modül.ad }}</v-list-item-title>
          <v-list-item-subtitle>{{ modül.aciklama }}</v-list-item-subtitle>
        </v-list-item-content>
        <v-list-item-action>
          <v-checkbox v-model="modül.testEdildi" />
        </v-list-item-action>
      </v-list-item>
    </v-list>
  </div>
</template>

<script setup lang="ts">
import { useFinalChecklist } from './final/useFinalChecklist'

const { modüller } = useFinalChecklist()
</script>

<style scoped>
.final-checklist {
  max-width: 700px;
  margin: auto;
}
.completed {
  background-color: #e8f5e9;
}
</style>