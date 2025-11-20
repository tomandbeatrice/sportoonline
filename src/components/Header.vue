<template>
  <header class="header">
    <h1 class="logo">SPORTOONLINE</h1>
    <div class="icons">
      <span class="search">🔍</span>
      <span class="cart">🛒</span>
    </div>
  </header>
</template>

<style scoped>
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
}
.logo {
  font-weight: bold;
  font-size: 1.5rem;
}
.icons span {
  margin-left: 1rem;
  cursor: pointer;
}
</style>