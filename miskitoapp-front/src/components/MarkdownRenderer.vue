<template>
  <div v-html="compiledMarkdown"></div>
</template>

<script setup>
import { computed } from 'vue'
import { marked } from 'marked'

const props = defineProps({
  source: String
})

const compiledMarkdown = computed(() => props.source ? marked.parse(props.source) : '')
</script>