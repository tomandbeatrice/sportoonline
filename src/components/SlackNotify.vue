<template>
  <SlackToggle @notify="sendSlackUpdate" />
</template>

<script setup lang="ts">
import SlackToggle from '@/components/SlackToggle.vue'
import { sendSlackUpdate } from '@/services/slackService'

function sendSlackUpdate(message) {
  sendSlackUpdate(message)
}
</script>