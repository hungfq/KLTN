<template>
  <router-view data-theme="cupcake" />
  <modals-container />
</template>
  // This starter template is using Vue 3
<script setup>
import { ModalsContainer } from 'vue-final-modal';

localStorage.theme = 'light';
</script>
<style scoped>
.logo {
  height: 6em;
  padding: 1.5em;
  will-change: filter;
}
.logo:hover {
  filter: drop-shadow(0 0 2em #646cffaa);
}
.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}
</style>
<style>
.btn {
  min-height: 40px !important;
  height: fit-content !important;

  font-weight: 600 !important;
}
</style>
