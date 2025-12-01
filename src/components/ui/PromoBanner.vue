<template>
  <div class="banner">
    <img src="/banner.jpg" alt="Promo" />
    <div class="text">
      <h2>SALE UP TO 50% OFF</h2>
      <button>SHOP NOW</button>
    </div>
  </div>
</template>

<style scoped>
.banner {
  position: relative;
  text-align: center;
}
.banner img {
  width: 100%;
  height: auto;
}
.text {
  position: absolute;
  top: 30%;
  left: 50%;
  transform: translate(-50%, -30%);
  color: white;
}
button {
  margin-top: 1rem;
  padding: 0.5rem 1rem;
  background: black;
  color: white;
  border: none;
  cursor: pointer;
}
</style>