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
            <router-link to="/mypage">Inicio</router-link>
          </div>
          <div>
            <router-link to="/about">Sobre nosotros</router-link>
          </div>
          <div>
            <a href="https://massumifukuda.work/wp/contact/">Contact</a>
          </div>
        </nav>
      </div>
    </div>
    <div class="login-status-wrapper">
      <div class="container">
        <!-- ログイン状態の表示 -->
        <div class="login-status">
          <template v-if="isLoggedIn">
            <span> Yamni Balram, {{ userName }} !</span>
          </template>
          <template v-else>
            <router-link to="/login">Dimaia</router-link>
          </template>
        </div>
      </div>
    </div>
    <div class="contents">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'

const route = useRoute()
const { locale } = useI18n()

const isLoggedIn = ref(false)
const userName = ref('')

async function updateLoginStatus() {
  const token = localStorage.getItem('access_token')
  if (token) {
    try {
      const res = await fetch('http://localhost:8000/mypage', {
        headers: { Authorization: `Bearer ${token}` }
      })
      if (res.ok) {
        const data = await res.json()
        isLoggedIn.value = true
        userName.value = data.name || data.email || ''
        return
      }
    } catch {}
  }
  isLoggedIn.value = false
  userName.value = ''
}

onMounted(updateLoginStatus)
window.addEventListener('login-success', updateLoginStatus)
</script>

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
.login-status-wrapper {
  padding: 10px;
  margin: 2em auto 0;
}
.login-status-wrapper .container{
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  justify-content: flex-end;
}
</style>