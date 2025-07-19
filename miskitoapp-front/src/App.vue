<script setup>
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'

const route = useRoute()
const { locale } = useI18n()
</script>

<template>
  <div>
    <!-- トップページ以外でヘッダー表示 -->
    <div v-if="route.path !== '/'" class="site-header">
      <div class="header-container">
        <h1>
          <router-link to="/">miskito.org</router-link>
        </h1>
        <nav>
          <div>
            <router-link to="/about">Sobre nosotros</router-link>
          </div>
          <div>
            <a href="https://massumifukuda.work/wp/contact/">Contact</a>
          </div>
        </nav>
      </div>
    </div>
    <div class="contents">
      <router-view />
    </div>
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
