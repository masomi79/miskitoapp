<template>
  <div class="field-wrap">
    <div v-if="wordset" class="result">
      <h2>{{ wordset.source_word }}  ({{ wordset.source_lang }})</h2>
      <ul>
        <li v-for="(rel, idx) in wordset.relations" :key="idx">
          <template v-if="idx > 0">, </template>
          <a :href="`/${wordset.target_lang}/${rel.id}`">{{ rel.word }}</a>
        </li>
      </ul>
      <div v-if="wordset.compose_words && wordset.target_lang == 'es'">
        Palabras compuestas:
        <ul>
          <li v-for="(rel, idx) in wordset.compose_words" :key="idx">
            <span><a :href="`/${wordset.source_lang}/${rel.id}`">{{ rel.miskitoWord }}</a></span>
            <span>...</span>
            <span v-for="(rell, j) in rel.translations" :key="j">
              <a :href="`/${wordset.target_lang}/${rell.id}`">{{ rell.word }}</a><span v-if="j < rel.translations.length - 1">, </span>
            </span>
          </li>
        </ul>
      </div>
      <div v-if="wordset.notes && wordset.notes.length">
        <ul>
          <li v-for="(note, idx) in wordset.notes" :key="idx">{{ note }}</li>
        </ul>
      </div>
     <div v-if="wordset.examples && wordset.examples.length">
        <h3>Sampla</h3>
        <ul>
          <li v-for="(ex, idx) in wordset.examples" :key="idx">
            <span v-if="ex.miskito_sentence">{{ ex.miskito_sentence }}</span>
            <span v-if="ex.spanish_sentence">（{{ ex.spanish_sentence }}）</span>
          </li>
        </ul>
      </div>
      <p class="tar">ID:{{ id }}</p>
    </div>
  </div>
</template>

<script>

export default {
  props: ['lang', 'id'],
  data() {
    return {
      wordset: null
    }
  },
  async created() {
    const res = await fetch(`/api/word-relations-by-id?lang=${this.lang}&word_id=${this.id}`);
    this.wordset = await res.json();
  }
}
/** * Wordsetの構造
  "source_lang": 言語コード
  "source_word_id": 単語のID,
  "source_word" : 単語
  "target_lang": 目標言語の言語コード
  "relations": [],  # 関連する単語のリスト
  "notes": [],　メモ
  "examples": [],　例文のリスト
  "miskito_sentence": Miskitoの例文
  "spanish_sentence": スペイン語の例文
  "compose_words": [],  # 2語以上の単語がある場合に使用
  "compose_words_relations": []  # compose_wordsの訳語リスト
 */
</script>
<style scoped>
</style>