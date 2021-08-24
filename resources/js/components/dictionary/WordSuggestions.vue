<template>
    <h3>Talvez Busca ... </h3>
    <ul>
        <li
            v-for="suggestion in suggestionsSet"
            v-bind:key="suggestion.id"
        >
            <span
                v-on:click="sendResearchData(suggestion.id, suggestion.miskitoWord, 'miskito')"
                class="pointer"
            >{{ suggestion.miskitoWord }}</span>
        </li>
    </ul>
</template>
<script>
export default {
    inheritAttrs: false,
    props:['suggestionsSet'],
    emits:['catchResearchData'],
    methods:{
        sendResearchData(id,word,lang){
            this.$emit('catchResearchData', id, word, lang);
        }
    }
}
</script>