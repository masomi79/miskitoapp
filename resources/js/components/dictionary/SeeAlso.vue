<template> 
    <h3>Vease También</h3>
    <ul>
        <li 
            v-for="relatedTerm in relatedTermsSet"
            v-bind:key = relatedTerm.id
        >
            <span 
                class="related-word pointer">
                <router-link
                    :to="{
                        path: 'word',
                        query:{
                            id: relatedTerm.id,
                            lang: 'miq'
                        }
                    }">{{ relatedTerm.word }}
                </router-link>
            </span>
            <span
                class="related-meaning pointer"
                v-for="(meaning, index) in relatedTerm.meanings"
                v-bind:key="index"
            >
                <router-link
                    :to="{
                        path: 'word',
                        query:{
                            id: meaning.id,
                            lang: 'esp'
                        }
                    }">{{ meaning.word }}
                </router-link>
            </span>
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
        margin-top: 1.6rem;
    }
</style>