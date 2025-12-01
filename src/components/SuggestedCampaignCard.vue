<template>
  <div v-if="suggestion">
    <h3>Önerilen Kampanya</h3>
    <p>Segment: {{ suggestion.segment }}</p>
    <p>Tip: {{ suggestion.campaign_type }}</p>
    <p>İndirim: %{{ suggestion.suggested_discount }}</p>
    <p>Güven Katsayısı: <strong>{{ suggestion.confidence_score }}%</strong></p>
  </div>
</template>

<script setup>
defineProps({
  suggestion: {
    type: Object,
    required: true
  }
})
</script>