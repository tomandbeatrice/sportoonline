<template>
  <TeamTabs :teams="teamList" @select="setActiveTeam" />
</template>

<script setup lang="ts">
import { ref } from 'vue'
import TeamTabs from '@/components/TeamTabs.vue'
import { useTeamStore } from '@/stores/teamStore'

const teamStore = useTeamStore()
const teamList = teamStore.teams
const setActiveTeam = (teamId: string) => teamStore.setActive(teamId)
</script>