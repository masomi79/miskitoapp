<script setup>
import HelloWorld from './components/HelloWorld.vue'
import WordSearch from './components/WordSearch.vue'
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()
</script>

<template>
  <div class="site-header">
    <div class="header-container">
      <h1>
        <router-link to="/">miskito.org</router-link>
      </h1>
      <nav>
        <div>
            <router-link to="/about">Sobre nosotros</router-link>
        </div>
        <div><a href="https://massumifukuda.work/wp/contact/">Contact</a></div>
      </nav>
    </div>
  </div>
  <div class="contents">
    <router-view />
  </div>
</template>

<style scoped>
.footer-container {
  background: #333;
  color: #eee;
}
nav {
  display: flex;
  gap: 10px;
}

h1{
  margin: 0;
  font-size: 16px;
  font-weight: normal;
}
.logo {
  height: 4em;
  will-change: filter;
  transition: filter 300ms;
}
.logo:hover {
  filter: drop-shadow(0 0 2em #81CACD);
}
.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}
</style>
