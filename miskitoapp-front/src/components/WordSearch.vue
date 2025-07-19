<template>
  <div class="field-wrap">
    <SearchForm
      v-model="word"
      placeholder="ponga una palabra en español o en miskitu"
      button-label="buscar / plikaia"
      @submit="search"
    />
    <div v-if="result && result.input_word" class="result">
      <h2>{{ result.input_word }} ({{ result.input_lang }})</h2>
      <ul>
        <li v-for="(rel, idx) in result.relations" :key="idx">
          <template v-if="idx > 0">, </template>
          <a :href="`/${result.target_lang}/${rel.id}`">{{ rel.word }}</a>
        </li>
      </ul>
      <div v-if="result.compose_words">
        <strong>Composed words:</strong>
        <span v-for="(word, idx) in result.compose_words" :key="idx">
          <a :href="`/${result.target_lang}/${word}`">{{ word }}</a>
          <span v-if="idx < result.compose_words.length - 1">, </span>
        </span>
      </div>
    </div>
    <div v-else-if="result && result.error">
      <p style="color: red;">Error: {{ result.error }}</p>
    </div>
  </div>
</template>

<script>
import SearchForm from './SearchForm.vue';

export default {
  components: { SearchForm },
  data() {
    return {
      word: "",
      result: null
    };
  },
  methods: {
    async search() {
      console.log("search called")
      try {
        const res = await fetch(`/api/word-relations?word=${encodeURIComponent(this.word)}`);
        this.result = await res.json();
      } catch (e) {
        this.result = { error: e.message };
        console.error(e);
      }
    }
  }
};
</script>