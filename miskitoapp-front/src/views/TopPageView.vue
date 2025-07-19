<template>
  <div>
    <div class="language-selector">
      <select v-model="locale" style="margin-left: 1em;">
        <option value="es">español</option>
        <option value="miq">Miskitu</option>
        <option value="ja">日本語</option>
        <option value="en">English</option>
      </select>
    </div>
    <h1>
      <router-link to="/">
        <img src="/logo.png" class="logo" alt="miskito.org logo" />
      </router-link>
    </h1>
    <div>
      <WordSearch />
    </div>
    <nav>
      <MarkdownRenderer :source="markdownContent" />
    </nav>
  </div>
</template>

<script setup>
import { ref, watchEffect } from 'vue'
import { useI18n } from 'vue-i18n'
import WordSearch from '../components/WordSearch.vue'
import MarkdownRenderer from '../components/MarkdownRenderer.vue'

const { locale } = useI18n()
const markdownContent = ref('')

const loadMarkdown = async () => {
  let url
  if (locale.value === 'ja') {
    url = '/top/top.ja.md'
  } else if (locale.value === 'en') {
    url = '/top/top.en.md'
  } else if (locale.value === 'es') {
    url = '/top/top.es.md'
  } else {
    url = '/top/top.miq.md'
  }
  markdownContent.value = await fetch(url).then(r => r.text())
}

watchEffect(() => {
  loadMarkdown()
})
</script>

<style scope>
    nav ul{
        display: flex;
        justify-content: space-around;
        margin: 4em auto 1em;
    }
    nav ul li {
        margin-right: 10px;
        list-style: none;
        justify-content: center;
        align-items: center;
        display: inline-block;
    }
    nav ul li a {
        text-decoration: none;
        color: #42b883;
    }
    nav ul li a:hover {
        text-decoration: underline;
    }
</style>