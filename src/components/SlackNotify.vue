<template>
  <SlackToggle @notify="sendSlackUpdate" />
</template>

<script setup lang="ts">
import SlackToggle from '@/components/SlackToggle.vue'
import { sendSlackUpdate as sendSlackMessage } from '@/services/slackService'

function sendSlackUpdate(message) {
  sendSlackMessage(message)
}
</script>