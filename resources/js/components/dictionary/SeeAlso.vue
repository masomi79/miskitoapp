<template> 
    <h3>Vease También</h3>
    <ul>
        <li 
            v-for="relatedTerm in relatedTermsSet"
            v-bind:key = relatedTerm.id
        >
            <span 
                class="related-word pointer"
                v-on:click="sendResearchData(relatedTerm.id, relatedTerm.word, 'miskito')"
            >{{ relatedTerm.word }}</span>
            <span
                class="related-meaning pointer"
                v-for="(meaning, index) in relatedTerm.meanings"
                v-bind:key="index"
                v-on:click="sendResearchData(meaning.id, meaning.word)"
            >{{ meaning.word }}</span>
        </li>
    </ul>
</template>
<script>
export default {
    inheritAttrs: false,
    props:['relatedTermsSet'],
    emits:['catchResearchData'],
    methods:{
        sendResearchData(id,word,lang){
            this.$emit('catchResearchData', id, word, lang);
        }
    }
}
</script>
<style scoped>

    h3{
        margin-top: 1rem;
    }
</style>