<template>
  <div class="single-page-contents">
  <div class="language-selector">
    <select v-model="locale" style="margin-left: 1em;">
      <option value="es">español</option>
      <option value="miq">Miskitu</option>
      <option value="ja">日本語</option>
      <option value="en">English</option>
    </select>
  </div>  
    <MarkdownRenderer :source="markdownContent" />
  </div>
</template>

<script setup>
import { ref, watchEffect } from 'vue'
import { useI18n } from 'vue-i18n'
import MarkdownRenderer from '../components/MarkdownRenderer.vue'

const { locale } = useI18n()
const markdownContent = ref('')

const loadMarkdown = async () => {
  let url
  if (locale.value === 'ja') {
    url = '/about/about.ja.md'
  } else if (locale.value === 'en') {
    url = '/about/about.en.md'
  } else if (locale.value === 'es') {
    url = '/about/about.es.md'
  } else {
    url = '/about/about.miq.md' // Default to miskitu if no match
  }
  markdownContent.value = await fetch(url).then(r => r.text())
}

watchEffect(() => {
  loadMarkdown()
})
</script>