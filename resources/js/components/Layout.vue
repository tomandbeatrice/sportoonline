<template>
  <div class="min-h-screen bg-gray-50">
    <Navigation />
    <main>
      <router-view />
    </main>
    <Footer />
  </div>
</template>

<script setup lang="ts">
import Navigation from './Navigation.vue';
import Footer from './Footer.vue';
</script>

<style>
/* Global styles */
</style>
